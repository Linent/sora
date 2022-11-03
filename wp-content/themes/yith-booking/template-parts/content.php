<?php
/**
 * Template part for displaying posts
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package YITH_Booking
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
        <div class="entry-meta">
            <?php yith_booking_entry_meta(); ?>
        </div>
    </header><!-- .entry-header -->

    <?php yith_booking_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        the_content( sprintf(
                         wp_kses(
                         /* translators: %s: Name of current post. Only visible to screen readers */
                             __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'yith-booking' ),
                             array(
                                 'span' => array(
                                     'class' => array(),
                                 ),
                             )
                         ),
                         get_the_title()
                     ) );

        wp_link_pages( array(
                           'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'yith-booking' ),
                           'after'  => '</div>',
                       ) );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php yith_booking_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
