<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package YITH_Booking
 */

?>

	</div><!-- #content -->

    <?php if ( is_active_sidebar( 'sidebar-footer-row-top' ) || is_active_sidebar( 'sidebar-footer-left' ) || is_active_sidebar( 'sidebar-footer-center' ) || is_active_sidebar( 'sidebar-footer-right' ) || is_active_sidebar( 'sidebar-footer-row-bottom' ) ): ?>

        <footer id="colophon" class="site-footer">
            <div class="site-footer__container">
                <?php if ( is_active_sidebar( 'sidebar-footer-row-top' ) ): ?>
                    <div class="site-footer__row-top">
                        <?php dynamic_sidebar( 'sidebar-footer-row-top' ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( is_active_sidebar( 'sidebar-footer-left' ) || is_active_sidebar( 'sidebar-footer-center' ) || is_active_sidebar( 'sidebar-footer-right' ) ): ?>
                    <div class="site-footer__row-center">
                        <div class="site-footer__left">
                            <?php dynamic_sidebar( 'sidebar-footer-left' ); ?>
                        </div>
                        <div class="site-footer__center">
                            <?php dynamic_sidebar( 'sidebar-footer-center' ); ?>
                        </div>
                        <div class="site-footer__right">
                            <?php dynamic_sidebar( 'sidebar-footer-right' ); ?>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if ( is_active_sidebar( 'sidebar-footer-row-bottom' ) ): ?>
                    <div class="site-footer__row-bottom">
                        <?php dynamic_sidebar( 'sidebar-footer-row-bottom' ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </footer><!-- #colophon -->

    <?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
