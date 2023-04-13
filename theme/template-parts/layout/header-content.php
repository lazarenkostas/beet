<?php
/**
 * Template part for displaying the header content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package beet
 */

?>

<header id="masthead" class="py-[42px]">
	<div class="container flex justify-between items-center">
		<div class="logo w-2/12">
			<h1 class="custom-logo max-w-[118px]"><?php show_custom_logo(); ?><span class="css-clip hidden"><?php echo get_bloginfo('name'); ?></span></h1>
		</div>


	<nav class ="w-6/12 flex justify-between" id="site-navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'beet' ); ?>">
		<button class="block lg:hidden" aria-controls="primary-menu" aria-expanded="false"><span class="menu-mobile"></span></button>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'primary-menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" aria-label="submenu">%3$s</ul>',
				"menu_class"	=> 'lg:flex hidden w-full justify-between text-dark capitalize text-[16px] font-bold',
				"container_class" => "w-full flex",
			)
		);
		?>
	</nav><!-- #site-navigation -->
	</div>
</header><!-- #masthead -->
