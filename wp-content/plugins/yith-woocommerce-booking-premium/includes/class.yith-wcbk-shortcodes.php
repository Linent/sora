<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Shortcodes' ) ) {
    /**
     * Class YITH_WCBK_Shortcodes
     * register and manage shortcodes
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Shortcodes {

        public static function init() {
            $shortcodes = array(
                'booking_form'        => __CLASS__ . '::booking_form', // print booking form
                'booking_search_form' => __CLASS__ . '::booking_search_form', // print booking form
                'booking_map'         => __CLASS__ . '::booking_map', // print booking form
                'booking_services'    => __CLASS__ . '::booking_services', // print booking form
            );

            foreach ( $shortcodes as $shortcode => $function ) {
                add_shortcode( $shortcode, $function );
            }
        }

        /**
         * Booking Form
         *
         * @param array $atts
         * @return string
         */
        public static function booking_form( $atts ) {
            global $product;
            ob_start();
            $booking_id = isset( $atts[ 'id' ] ) ? $atts[ 'id' ] : 0;

            if ( !$booking_id && $product && $product->get_id() ) {
                $booking_id = $product->get_id();
            }

            if ( $booking_id ) {
                $booking_product = wc_get_product( $booking_id );
                if ( $booking_product && $booking_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
                    global $product, $post;
                    $old_product = $product;
                    $old_post    = $post;
                    $post        = get_post( $booking_product->get_id() );
                    $product     = $booking_product;
                    wc_get_template( 'shortcodes/booking-form.php', array(), '', YITH_WCBK_TEMPLATE_PATH );
                    $product = $old_product;
                    $post    = $old_post;
                }
            }

            return ob_get_clean();
        }

        /**
         * Booking search form
         *
         * @param array $atts
         * @return string
         */
        public static function booking_search_form( $atts ) {
            ob_start();
            $form_id = isset( $atts[ 'id' ] ) ? $atts[ 'id' ] : 0;
            if ( $form_id ) {
                $form = new YITH_WCBK_Search_Form( $form_id );
                $form->output( $atts );
            }

            return ob_get_clean();
        }

        /**
         * Booking map
         *
         * @param array $atts
         * @return string
         */
        public static function booking_map( $atts ) {
            $product_id = isset( $atts[ 'id' ] ) ? absint( $atts[ 'id' ] ) : false;
            ob_start();
            /** @var WC_Product_Booking $product */
            $product = wc_get_product( $product_id );

            $coordinates = false;
            if ( isset( $atts[ 'latitude' ] ) && isset( $atts[ 'longitude' ] ) ) {
                $coordinates = array(
                    'lat' => $atts[ 'latitude' ],
                    'lng' => $atts[ 'longitude' ],
                );
            } else if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
                $coordinates = $product->get_location_coordinates();
            }

            if ( $coordinates ) {
                $width  = isset( $atts[ 'width' ] ) ? $atts[ 'width' ] : '100%';
                $height = isset( $atts[ 'height' ] ) ? $atts[ 'height' ] : '500px';
                $zoom   = isset( $atts[ 'zoom' ] ) ? absint( $atts[ 'zoom' ] ) : 9;
                $type   = isset( $atts[ 'type' ] ) ? $atts[ 'type' ] : 'ROADMAP';

                $width  = ( !is_numeric( $width ) ) ? $width : $width . 'px';
                $height = ( !is_numeric( $height ) ) ? $height : $height . 'px';

                wc_get_template( 'shortcodes/booking-map.php', compact( 'coordinates', 'product', 'width', 'height', 'zoom', 'type' ), '', YITH_WCBK_TEMPLATE_PATH );

            }

            return ob_get_clean();

        }

        /**
         * Booking services
         *
         * @param array $atts
         * @return string
         */
        public static function booking_services( $atts ) {
            global $product;
            $html = '';
            /** @var WC_Product_Booking $product */
            if ( $product && yith_wcbk_is_booking_product( $product ) ) {
                $defaults          = array(
                    'type'              => 'all',
                    'show_title'        => 'yes',
                    'show_prices'       => 'no',
                    'show_descriptions' => 'yes'
                );
                $atts              = wp_parse_args( $atts, $defaults );
                $atts[ 'product' ] = $product;
                ob_start();

                wc_get_template( 'shortcodes/booking-services.php', $atts, '', YITH_WCBK_TEMPLATE_PATH );

                $html = ob_get_clean();
            }

            return $html;

        }
    }
}