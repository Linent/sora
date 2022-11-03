<?php
/**
 * Template Name: No Sidebar
 *
 * Full Width template for pages
 *
 * @package YITH_Booking
 */

get_header();
?>

	<div id="primary" class="content-area no-sidebar-template">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
