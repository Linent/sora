<?php
!defined( 'YITH_WCBK' ) && exit;

if ( !function_exists( 'yith_wcbk_get_duration_unit_label' ) ) {
    function yith_wcbk_get_duration_unit_label( $duration_unit, $plural_control = 1 ) {
        $units         = yith_wcbk_get_duration_units( $plural_control );
        $duration_unit = isset( $units[ $duration_unit ] ) ? $units[ $duration_unit ] : '';

        return $duration_unit;
    }
}

if ( !function_exists( 'yith_wcbk_format_duration' ) ) {
    /**
     * format the duration; example: "XX days"
     *
     * @param int    $duration
     * @param string $unit
     * @return string
     */
    function yith_wcbk_format_duration( $duration, $unit ) {
        $plural       = $duration > 1;
        $label_string = yith_wcbk_get_duration_label_string( $unit, $plural, false );
        $label        = !!$label_string ? sprintf( $label_string, $duration ) : '';

        return apply_filters( 'yith_wcbk_format_duration', $label, $duration, $unit );
    }
}

if ( !function_exists( 'yith_wcbk_get_booking_meta_label' ) ) {
    function yith_wcbk_get_booking_meta_label( $key ) {
        $booking_meta_labels = apply_filters( 'yith_wcbk_booking_meta_labels', array(
            'from'     => yith_wcbk_get_label( 'from' ),
            'to'       => yith_wcbk_get_label( 'to' ),
            'duration' => yith_wcbk_get_label( 'duration' ),
            'persons'  => yith_wcbk_get_label( 'people' ),
        ) );
        $label               = array_key_exists( $key, $booking_meta_labels ) ? $booking_meta_labels[ $key ] : $key;

        return apply_filters( 'yith_wcbk_get_booking_meta_label', $label, $key, $booking_meta_labels );
    }
}

if ( !function_exists( 'yith_wcbk_get_label' ) ) {
    function yith_wcbk_get_label( $key ) {
        return YITH_WCBK()->language->get_label( $key );
    }
}

if ( !function_exists( 'yith_wcbk_get_default_label' ) ) {
    function yith_wcbk_get_default_label( $key ) {
        return YITH_WCBK()->language->get_default_label( $key );
    }
}

if ( !function_exists( 'yith_wcbk_get_product_duration_label' ) ) {
    /**
     * @param string $duration
     * @param string $duration_unit
     * @param bool   $is_fixed_blocks
     * @return string
     */
    function yith_wcbk_get_product_duration_label( $duration, $duration_unit, $is_fixed_blocks ) {
        $qty_mode     = !$is_fixed_blocks;
        $plural       = $duration > 1;
        $label_string = yith_wcbk_get_duration_label_string( $duration_unit, $plural, $qty_mode );

        $label = !!$label_string ? sprintf( $label_string, $duration ) : '';

        return apply_filters( 'yith_wcbk_get_product_duration_label', $label, $duration, $duration_unit, $is_fixed_blocks );
    }
}

if ( !function_exists( 'yith_wcbk_get_duration_label_string' ) ) {
    /**
     * @param   string $duration_unit
     * @param bool     $plural
     * @param bool     $qty_mode
     * @since 2.1.0
     * @return string
     */
    function yith_wcbk_get_duration_label_string( $duration_unit, $plural = false, $qty_mode = false ) {
        $duration = !$plural ? 1 : 2;
        if ( !$qty_mode ) {
            $labels = array(
                'month'  => _n( '%s month', '%s months', $duration, 'yith-booking-for-woocommerce' ),
                'day'    => _n( '%s day', '%s days', $duration, 'yith-booking-for-woocommerce' ),
                'hour'   => _n( '%s hour', '%s hours', $duration, 'yith-booking-for-woocommerce' ),
                'minute' => _n( '%s minute', '%s minutes', $duration, 'yith-booking-for-woocommerce' ),
            );

        } else {
            $labels = array(
                'month'  => _n( 'month(s)', '&times; %s months', $duration, 'yith-booking-for-woocommerce' ),
                'day'    => _n( 'day(s)', '&times; %s days', $duration, 'yith-booking-for-woocommerce' ),
                'hour'   => _n( 'hour(s)', '&times; %s hours', $duration, 'yith-booking-for-woocommerce' ),
                'minute' => _n( 'minute(s)', '&times; %s minutes', $duration, 'yith-booking-for-woocommerce' ),
            );
        }

        $label = array_key_exists( $duration_unit, $labels ) ? $labels[ $duration_unit ] : '';

        return apply_filters( 'yith_wcbk_get_duration_label_string', $label, $duration_unit, $plural, $qty_mode );
    }
}