<?php

if ( !function_exists( 'yith_booking_page_option_prefix' ) ) {
    function yith_booking_page_option_prefix() {
        return '_yith_bk_th_page_option_';
    }
}

if ( !function_exists( 'yith_booking_get_page_option_defaults' ) ) {
    function yith_booking_get_page_option_defaults() {
        return array(
            'hide_title' => 'no'
        );
    }
}

if ( !function_exists( 'yith_booking_get_page_option' ) ) {
    function yith_booking_get_page_option( $id, $option ) {
        $prefix   = yith_booking_page_option_prefix();
        $defaults = yith_booking_get_page_option_defaults();
        $key      = $prefix . $option;
        if ( metadata_exists( 'post', $id, $key ) ) {
            return get_post_meta( $id, $key, true );
        } elseif ( array_key_exists( $option, $defaults ) ) {
            return $defaults[ $option ];
        }
        return false;
    }
}
