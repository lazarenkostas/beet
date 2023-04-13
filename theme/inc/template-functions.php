<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package beet
 */

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function beet_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'beet_pingback_header' );

/**
 * Changes comment form default fields.
 *
 * @param array $defaults The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function beet_comment_form_defaults( $defaults ) {
	$comment_field = $defaults['comment_field'];

	// Adjust height of comment form.
	$defaults['comment_field'] = preg_replace( '/rows="\d+"/', 'rows="5"', $comment_field );

	return $defaults;
}
add_filter( 'comment_form_defaults', 'beet_comment_form_defaults' );

/**
 * Filters the default archive titles.
 */
function beet_get_the_archive_title() {
	if ( is_category() ) {
		$title = __( 'Category Archives: ', 'beet' ) . '<span>' . single_term_title( '', false ) . '</span>';
	} elseif ( is_tag() ) {
		$title = __( 'Tag Archives: ', 'beet' ) . '<span>' . single_term_title( '', false ) . '</span>';
	} elseif ( is_author() ) {
		$title = __( 'Author Archives: ', 'beet' ) . '<span>' . get_the_author_meta( 'display_name' ) . '</span>';
	} elseif ( is_year() ) {
		$title = __( 'Yearly Archives: ', 'beet' ) . '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'beet' ) ) . '</span>';
	} elseif ( is_month() ) {
		$title = __( 'Monthly Archives: ', 'beet' ) . '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'beet' ) ) . '</span>';
	} elseif ( is_day() ) {
		$title = __( 'Daily Archives: ', 'beet' ) . '<span>' . get_the_date() . '</span>';
	} elseif ( is_post_type_archive() ) {
		$cpt   = get_post_type_object( get_queried_object()->name );
		$title = sprintf(
			/* translators: %s: Post type singular name */
			esc_html__( '%s Archives', 'beet' ),
			$cpt->labels->singular_name
		);
	} elseif ( is_tax() ) {
		$tax   = get_taxonomy( get_queried_object()->taxonomy );
		$title = sprintf(
			/* translators: %s: Taxonomy singular name */
			esc_html__( '%s Archives', 'beet' ),
			$tax->labels->singular_name
		);
	} else {
		$title = __( 'Archives:', 'beet' );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'beet_get_the_archive_title' );

/**
 * Determines whether the post thumbnail can be displayed.
 */
function beet_can_show_post_thumbnail() {
	return apply_filters( 'beet_can_show_post_thumbnail', ! post_password_required() && ! is_attachment() && has_post_thumbnail() );
}

/**
 * Returns the size for avatars used in the theme.
 */
function beet_get_avatar_size() {
	return 60;
}

/**
 * Create the continue reading link
 *
 * @param string $more_string The string shown within the more link.
 */
function beet_continue_reading_link( $more_string ) {

	if ( ! is_admin() ) {
		$continue_reading = sprintf(
			/* translators: %s: Name of current post. */
			wp_kses( __( 'Continue reading %s', 'beet' ), array( 'span' => array( 'class' => array() ) ) ),
			the_title( '<span class="sr-only">"', '"</span>', false )
		);

		$more_string = '<a href="' . esc_url( get_permalink() ) . '">' . $continue_reading . '</a>';
	}

	return $more_string;
}

// Filter the excerpt more link.
add_filter( 'excerpt_more', 'beet_continue_reading_link' );

// Filter the content more link.
add_filter( 'the_content_more_link', 'beet_continue_reading_link' );

/**
 * Outputs a comment in the HTML5 format.
 *
 * This function overrides the default WordPress comment output in HTML5
 * format, adding the required class for Tailwind Typography. Based on the
 * `html5_comment()` function from WordPress core.
 *
 * @param WP_Comment $comment Comment to display.
 * @param array      $args    An array of arguments.
 * @param int        $depth   Depth of the current comment.
 */
function beet_html5_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

	$commenter          = wp_get_current_commenter();
	$show_pending_links = ! empty( $commenter['comment_author'] );

	if ( $commenter['comment_author_email'] ) {
		$moderation_note = __( 'Your comment is awaiting moderation.', 'beet' );
	} else {
		$moderation_note = __( 'Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.', 'beet' );
	}
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $comment->has_children ? 'parent' : '', $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
					if ( 0 !== $args['avatar_size'] ) {
						echo get_avatar( $comment, $args['avatar_size'] );
					}
					?>
					<?php
					$comment_author = get_comment_author_link( $comment );

					if ( '0' === $comment->comment_approved && ! $show_pending_links ) {
						$comment_author = get_comment_author( $comment );
					}

					printf(
						/* translators: %s: Comment author link. */
						wp_kses_post( __( '%s <span class="says">says:</span>', 'beet' ) ),
						sprintf( '<b class="fn">%s</b>', wp_kses_post( $comment_author ) )
					);
					?>
				</div><!-- .comment-author -->

				<div class="comment-metadata">
					<?php
					printf(
						'<a href="%s"><time datetime="%s">%s</time></a>',
						esc_url( get_comment_link( $comment, $args ) ),
						esc_attr( get_comment_time( 'c' ) ),
						esc_html(
							sprintf(
							/* translators: 1: Comment date, 2: Comment time. */
								__( '%1$s at %2$s', 'beet' ),
								get_comment_date( '', $comment ),
								get_comment_time()
							)
						)
					);

					edit_comment_link( __( 'Edit', 'beet' ), ' <span class="edit-link">', '</span>' );
					?>
				</div><!-- .comment-metadata -->

				<?php if ( '0' === $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation"><?php echo esc_html( $moderation_note ); ?></em>
				<?php endif; ?>
			</footer><!-- .comment-meta -->

			<div <?php beet_content_class( 'comment-content' ); ?>>
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<?php
			if ( '1' === $comment->comment_approved || $show_pending_links ) {
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="reply">',
							'after'     => '</div>',
						)
					)
				);
			}
			?>
		</article><!-- .comment-body -->
	<?php
}

/**
 * Allow SVG uploads for administrator users.
 *
 * @param array $upload_mimes Allowed mime types.
 *
 * @return mixed
 */
add_filter(
		'upload_mimes',
		function ($upload_mimes) {
			// By default, only administrator users are allowed to add SVGs.
			// To enable more user types edit or comment the lines below but beware of
			// the security risks if you allow any user to upload SVG files.
			if (!current_user_can('administrator')) {
				return $upload_mimes;
			}
			$upload_mimes['svg'] = 'image/svg+xml';
			$upload_mimes['svgz'] = 'image/svg+xml';
			return $upload_mimes;
		}
);

/**
 * Add SVG files mime check.
 *
 * @param array $wp_check_filetype_and_ext Values for the extension, mime type, and corrected filename.
 * @param string $file Full path to the file.
 * @param string $filename The name of the file (may differ from $file due to $file being in a tmp directory).
 * @param string[] $mimes Array of mime types keyed by their file extension regex.
 * @param string|false $real_mime The actual mime type or false if the type cannot be determined.
 */
add_filter(
		'wp_check_filetype_and_ext',
		function ($wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime) {
			if (!$wp_check_filetype_and_ext['type']) {
				$check_filetype = wp_check_filetype($filename, $mimes);
				$ext = $check_filetype['ext'];
				$type = $check_filetype['type'];
				$proper_filename = $filename;
				if ($type && 0 === strpos($type, 'image/') && 'svg' !== $ext) {
					$ext = false;
					$type = false;
				}
				$wp_check_filetype_and_ext = compact('ext', 'type', 'proper_filename');
			}
			return $wp_check_filetype_and_ext;
		},
		10,
		5
);

// Custom Logo
add_theme_support( 'custom-logo', array(
		'height'      => '150',
		'flex-height' => true,
		'flex-width'  => true,
) );

function show_custom_logo( $size = 'medium' ) {
	if ( $custom_logo_id = get_theme_mod( 'custom_logo' ) ) {
		$logo_image = wp_get_attachment_image( $custom_logo_id, $size, false, array(
				'class'    => 'custom-logo w-full',
				'itemprop' => 'siteLogo',
				'alt'      => get_bloginfo( 'name' ),
		) );
	} else {
		$logo_url = get_stylesheet_directory_uri() . '/assets/images/custom-logo.png';
		$w        = 100;
		$h        = 60;
		$logo_image = '<img src="' . $logo_url . '" width="' . $w . '" height="' . $h . '" class="custom-logo123123 w-full" itemprop="siteLogo" alt="' . get_bloginfo( 'name' ) . '">';
	}

	$html       = sprintf( '<a href="%1$s" class="custom-logo-link w-full" rel="home" title="%2$s" itemscope>%3$s</a>', esc_url( home_url( '/' ) ), get_bloginfo( 'name' ), $logo_image );
	echo apply_filters( 'get_custom_logo', $html );
}

/**
 * ACF Gutenberg blocks
 */
function register_custom_acf_blocks()
{

	// Check function exists.
	if (function_exists('acf_register_block_type')) {

		// See icons here - https://developer.wordpress.org/resource/dashicons
		$blocks = [
				[
						'name' => 'hero',
						'title' => __('Hero block', 'default'),
						'icon' => 'screenoptions',
				],
		];

		foreach ($blocks as $block) {
			$block_args = array(
					'render_template' => "parts/blocks/{$block['name']}.php",
					'category' => 'design',
					'mode' => 'auto',
					'align' => 'full',
					'enqueue_assets' => function () {
						if (is_admin()) {
							wp_enqueue_style('fw-gutenberg-editor-style');
						}
					},
			);
			$block_args = wp_parse_args($block, $block_args);

			acf_register_block_type($block_args);
		}
	}
}

add_action('acf/init', 'register_custom_acf_blocks');


/**
 * Output background image style
 *
 * @param array|string $img Image array or url
 * @param string $size Image size to retrieve
 * @param bool $echo Whether to output the the style tag or return it.
 *
 * @return string|void String when retrieving.
 */
function bg( $img = '', $size = '', $echo = true ) {

	if ( empty( $img ) ) {
		return false;
	}

	if ( is_array( $img ) ) {
		$url = $size ? $img['sizes'][ $size ] : $img['url'];
	} else {
		$url = $img;
	}

	$string = 'style="background-image: url(' . $url . ')"';

	if ( $echo ) {
		echo $string;
	} else {
		return $string;
	}
}
