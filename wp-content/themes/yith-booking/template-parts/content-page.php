<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package YITH_Booking
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( yith_booking_get_page_option( get_the_ID(), 'hide_title' ) !== 'yes' ): ?>
        <header class="entry-header">
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        </header><!-- .entry-header -->
    <?php endif; ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages( array(
                           'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'yith-booking' ),
                           'after'  => '</div>',
                       ) );
        ?>
    </div><!-- .entry-content -->

    <?php if ( get_edit_post_link() ) : ?>
        <footer class="entry-footer">
            <?php yith_booking_entry_footer(); ?>
        </footer><!-- .entry-footer -->
    <?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
