<?php
/**
 * Template part for displaying the footer content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package beet
 */

?>

<footer id="colophon">


	<?php if ( has_nav_menu( 'menu-2' ) ) : ?>
		<nav aria-label="<?php esc_attr_e( 'Footer Menu', 'beet' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-2',
					'menu_class'     => 'footer-menu',
					'depth'          => 1,
				)
			);
			?>
		</nav>
	<?php endif; ?>

	<div>
		<?php
		$beet_blog_info = get_bloginfo( 'name' );
		if ( ! empty( $beet_blog_info ) ) :
			?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>,
			<?php
		endif;

		?>
	</div>

</footer><!-- #colophon -->
