<?php
/**
 * The header for our theme
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package YITH_Booking
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'yith-booking' ); ?></a>

	<?php $header_sticky_class = 'yes' === get_theme_mod( 'yith_booking_header_sticky', 'yes' ) ? '' : 'no-sticky'; ?>

	<header id="masthead" class="site-header <?php echo $header_sticky_class; ?>">
		<div class="site-header-main">
			<?php
			$site_branding_class = "site-branding";
			//$site_branding_class .= !!get_theme_mod( 'custom_logo' ) ? ' has-custom-logo' : '';
			?>
			<div class="<?php echo $site_branding_class ?>">
				<?php
				the_custom_logo();
				if ( is_front_page() && is_home() ) :
					?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
				else :
					?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
				endif;
				?>
			</div><!-- .site-branding -->

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<div id="menu-toggle" class="menu-toggle"><span></span><span></span><span></span></div>

				<div id="site-header-menu" class="site-header-menu">
					<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'yith-booking' ); ?>">
						<?php
						wp_nav_menu( array(
										 'theme_location' => 'primary',
										 'menu_class'     => 'primary-menu',
									 ) );
						?>
					</nav><!-- .main-navigation -->

				</div><!-- .site-header-menu -->
			<?php else: ?>
				<div id="site-header-menu" class="site-header-menu site-header-menu-empty">
				</div><!-- .site-header-menu -->
			<?php endif; ?>
		</div><!-- .site-header-main -->
	</header><!-- #masthead -->

	<?php if ( apply_filters( 'yith_booking_show_header_image', ! function_exists( 'is_product' ) || ! is_product() ) ): ?>
		<div id="header-image">
			<?php the_header_image_tag(); ?>
		</div>
	<?php endif; ?>

	<div id="content-header" class="site-content-header">
		<?php
		global $post;
		do_action( 'yith_booking_content_header' );

		if ( ( is_singular() && 'post' === get_post_type() ) || ( $post && is_page( $post->ID ) ) ) {
			yith_booking_post_thumbnail();
		}
		?>
	</div>

	<div id="content" class="site-content">
