<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_Booking_Theme_Shortcodes' ) ) {
    /**
     * Class YITH_Booking_Theme_Shortcodes
     * register and manage shortcodes
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_Booking_Theme_Shortcodes {

        public static function init() {
            $shortcodes = array(
                'bk_box_container',
                'bk_box',
                'bk_read_more'
            );

            foreach ( $shortcodes as $shortcode ) {
                add_shortcode( $shortcode, array( __CLASS__, $shortcode ) );
            }
        }

        public static function bk_box_container( $atts, $content ) {
            $defaults = array(
                'border_radius' => 0,
                'border'        => 0,
                'border_color'  => '#DBDBDB',
                'shadow'        => 'no',
                'width'         => 100,
                'padding'       => 0,
                'margin'        => '15px 0'
            );
            $atts     = shortcode_atts( $defaults, $atts, __FUNCTION__ );

            $style_array = array(
                'display'       => 'block',
                'border-radius' => is_numeric( $atts[ 'border_radius' ] ) ? ( $atts[ 'border_radius' ] . 'px' ) : $atts[ 'border_radius' ],
                'border'        => is_numeric( $atts[ 'border' ] ) ? ( $atts[ 'border' ] . 'px solid ' . $atts[ 'border_color' ] ) : $atts[ 'border' ],
                'box-shadow'    => 'yes' === $atts[ 'shadow' ] ? '0 1px 4px 0 rgba(0, 0, 0, 0.1)' : '',
                'width'         => is_numeric( $atts[ 'width' ] ) ? ( $atts[ 'width' ] . '%' ) : $atts[ 'width' ],
                'padding'       => is_numeric( $atts[ 'padding' ] ) ? ( $atts[ 'padding' ] . 'px' ) : $atts[ 'padding' ],
                'margin'        => is_numeric( $atts[ 'margin' ] ) ? ( $atts[ 'margin' ] . 'px' ) : $atts[ 'margin' ]
            );
            $style       = '';
            foreach ( $style_array as $key => $value ) {
                $style .= $key . ':' . $value . ';';
            }

            return "<div class='yith-booking-shortcode__box-container' style='{$style}'>" . do_shortcode( $content ) . "<div style='clear: both'></div></div>";
        }

        public static function bk_box( $atts, $content ) {
            $defaults = array(
                'border_radius' => 4,
                'border'        => 1,
                'border_color'  => '#DBDBDB',
                'shadow'        => 'yes',
                'width'         => 100,
                'padding'       => 20,
                'responsive'    => 'yes',
                'margin_right'  => 20,
                'text_align'    => 'center'
            );
            $atts     = shortcode_atts( $defaults, $atts, __FUNCTION__ );

            $style_array = array(
                'display'       => 'block',
                'float'         => 'left',
                'border-radius' => is_numeric( $atts[ 'border_radius' ] ) ? ( $atts[ 'border_radius' ] . 'px' ) : $atts[ 'border_radius' ],
                'border'        => is_numeric( $atts[ 'border' ] ) ? ( $atts[ 'border' ] . 'px solid ' . $atts[ 'border_color' ] ) : $atts[ 'border' ],
                'box-shadow'    => 'yes' === $atts[ 'shadow' ] ? '0 1px 4px 0 rgba(0, 0, 0, 0.1)' : '',
                'width'         => is_numeric( $atts[ 'width' ] ) ? ( $atts[ 'width' ] . '%' ) : $atts[ 'width' ],
                'padding'       => is_numeric( $atts[ 'padding' ] ) ? ( $atts[ 'padding' ] . 'px' ) : $atts[ 'padding' ],
                'margin-right'  => is_numeric( $atts[ 'margin_right' ] ) ? ( $atts[ 'margin_right' ] . 'px' ) : $atts[ 'margin_right' ],
                'text-align'    => $atts[ 'text_align' ],
            );

            if ( $style_array[ 'margin-right' ] ) {
                $style_array[ 'width' ] = 'calc(' . $style_array[ 'width' ] . ' - ' . $style_array[ 'margin-right' ] . ')';
            }

            $style = '';
            foreach ( $style_array as $key => $value ) {
                $style .= $key . ':' . $value . ';';
            }
            $extra_class = 'yes' === $atts[ 'responsive' ] ? 'yith-booking-shortcode__box--responsive' : '';


            return "<div class='yith-booking-shortcode__box {$extra_class}' style='{$style}'>" . do_shortcode( $content ) . "</div>";
        }

        public static function bk_read_more( $atts, $content ) {
            $defaults = array(
                'label'      => __( 'Read More', 'yith-booking' ),
                'hide_label' => __( 'Hide', 'yith-booking' ),
                'show_icon'  => 'yes',
            );
            $atts     = shortcode_atts( $defaults, $atts, __FUNCTION__ );

            $arrow_down_icon = '<svg viewBox="0 0 18 18" role="presentation" aria-hidden="true" focusable="false" style="height: 13px; width: 13px; display: block; fill: currentcolor;"><path d="m16.29 4.3a1 1 0 1 1 1.41 1.42l-8 8a1 1 0 0 1 -1.41 0l-8-8a1 1 0 1 1 1.41-1.42l7.29 7.29z" fill-rule="evenodd"></path></svg>';

            $html = "<div class='yith-booking-shortcode__read-more__container'>";
            $html .= "<div class='yith-booking-shortcode__read-more__content'>";
            $html .= do_shortcode( $content );
            $html .= "</div>";
            $html .= "<div class='yith-booking-shortcode__read-more__label' data-label='{$atts['label']}' data-hide-label='{$atts['hide_label']}'>";
            $html .= "<span class='yith-booking-shortcode__read-more__label_text'>";
            $html .= $atts[ 'label' ];
            $html .= "</span>";
            $html .= 'yes' === $atts[ 'show_icon' ] ? "<span class='yith-booking-shortcode__read-more__icon'>{$arrow_down_icon}</span>" : '';
            $html .= "</div>";
            $html .= "</div>";

            return $html;
        }
    }

    YITH_Booking_Theme_Shortcodes::init();
}