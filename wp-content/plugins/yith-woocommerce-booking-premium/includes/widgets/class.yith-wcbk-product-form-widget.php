<?php
/**
 * Product Form Widget
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 */


!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Product_Form_Widget' ) ) {
    /**
     * YITH_WCBK_Product_Form_Widget
     *
     * @since  2.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Product_Form_Widget extends WC_Widget {
        /**
         * Constructor
         */
        public function __construct() {
            $this->widget_cssclass    = 'yith_wcbk_booking_product_form_widget';
            $this->widget_description = __( 'Display booking form', 'yith-booking-for-woocommerce' );
            $this->widget_id          = 'yith_wcbk_product_form';
            $this->widget_name        = _x( 'Booking Product Form', 'Widget Name', 'yith-booking-for-woocommerce' );

            $this->settings = array();

            parent::__construct();
        }

        /**
         * print the widget
         *
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance ) {
            global $product;

            if ( $this->get_cached_widget( $args ) ) {
                return;
            }

            if ( is_product() && yith_wcbk_is_booking_product( $product ) ) {

                wp_enqueue_script( 'yith-wcbk-product-form-widget' );

				$mobile_fixed_enabled = apply_filters( 'yith_wcbk_product_form_widget_mobile_fixed', true );
				if ( $mobile_fixed_enabled ) {
					$args['before_widget'] = str_replace(
						'yith_wcbk_booking_product_form_widget',
						'yith_wcbk_booking_product_form_widget yith_wcbk_booking_product_form_widget--mobile-fixed',
						$args['before_widget']
					);
				}

                ob_start();
                $this->widget_start( $args, $instance );

                wc_get_template( 'single-product/add-to-cart/booking-form/widget-booking-form.php', compact( 'product' ), '', YITH_WCBK_TEMPLATE_PATH );

                $this->widget_end( $args );
                wp_reset_postdata();
                echo $this->cache_widget( $args, ob_get_clean() );
            }
        }

        /**
         * Outputs the settings update form.
         *
         * @see   WP_Widget->form
         *
         * @param array $instance
         *
         * @return string|void
         */
        public function form( $instance ) {
            parent::form( $instance );

            $text = __( 'The booking product form', 'yith-booking-for-woocommerce' );

            echo "<p>$text</p>";
        }
    }
}