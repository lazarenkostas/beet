<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package beet
 */

?>



	<main>
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div>' . __( 'Pages:', 'beet' ),
				'after'  => '</div>',
			)
		);
		?>
	</main>

	<footer class="entry-footer">
		<?php beet_entry_footer(); ?>
	</footer><!-- .entry-footer -->

