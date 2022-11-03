<?php
!defined( 'YITH_WCBK' ) && exit;


if ( !function_exists( 'yith_wcbk_get_duration_units' ) ) {
    function yith_wcbk_get_duration_units( $plural_control = 1 ) {
        $duration_units = array(
            'month'  => _n( 'month', 'months', $plural_control, 'yith-booking-for-woocommerce' ),
            'day'    => _n( 'day', 'days', $plural_control, 'yith-booking-for-woocommerce' ),
            'hour'   => _n( 'hour', 'hours', $plural_control, 'yith-booking-for-woocommerce' ),
            'minute' => _n( 'minute', 'minutes', $plural_control, 'yith-booking-for-woocommerce' ),
        );

        return apply_filters( 'yith_wcbk_get_duration_units', $duration_units, $plural_control );
    }
}

if ( !function_exists( 'yith_wcbk_booking_admin_screen_ids' ) ) {

    /**
     * Return booking screen ids
     *
     * @return array
     */
    function yith_wcbk_booking_admin_screen_ids() {
        $booking_post_type = YITH_WCBK_Post_Types::$booking;
        $screen_ids        = array(
            YITH_WCBK_Post_Types::$booking,
            'edit-' . YITH_WCBK_Post_Types::$booking,
            YITH_WCBK_Post_Types::$search_form,
            'edit-' . YITH_WCBK_Post_Types::$search_form,
            'edit-' . YITH_WCBK_Post_Types::$service_tax,
            'product',
            'yith_booking_page_create_booking',
            'yith_booking_page_yith-wcbk-booking-calendar',
        );

        return apply_filters( 'yith_wcbk_booking_admin_screen_ids', $screen_ids );
    }
}

if ( !function_exists( 'yith_wcbk_add_product_class' ) ) {

    function yith_wcbk_add_product_class( $classes ) {
        $classes[] = 'product';

        return $classes;
    }
}

if ( !function_exists( 'yith_wcbk_get_minimum_minute_increment' ) ) {
    /**
     * get the minimum minute increment: default 15
     *
     * @since 2.0.5
     * @return string
     */
    function yith_wcbk_get_minimum_minute_increment() {
        return apply_filters( 'yith_wcbk_get_minimum_minute_increment', 15 );
    }
}

if ( !function_exists( 'yith_wcbk_get_max_months_to_load' ) ) {
    function yith_wcbk_get_max_months_to_load( $unit = 'day' ) {
        $months_to_load = 12;
        if ( 'hour' === $unit ) {
            $months_to_load = 3;
        } elseif ( 'minute' === $unit ) {
            $months_to_load = 1;
        }

        return apply_filters( 'yith_wcbk_get_max_months_to_load', $months_to_load, $unit );
    }
}

if ( !function_exists( 'yith_wcbk_array_add' ) ) {
    /**
     * add key and value after a specific key in array
     *
     * @param array  $array  the array
     * @param string $search the key to search for
     * @param string $key    the key to add
     * @param mixed  $value  the value to add
     * @param bool   $after  the value to add
     */
    function yith_wcbk_array_add( &$array, $search, $key, $value, $after = true ) {
        $position = array_search( $search, array_keys( $array ) );
        if ( $position !== false ) {
            $position = $after ? $position + 1 : $position;
            $first    = array_slice( $array, 0, $position, true );
            $current  = array( $key => $value );
            $last     = array_slice( $array, $position, count( $array ), true );
            $array    = array_merge( $first, $current, $last );
        } else {
            $array = array_merge( $array, array( $key => $value ) );
        }
    }
}

if ( !function_exists( 'yith_wcbk_array_add_after' ) ) {
    /**
     * add key and value after a specific key in array
     *
     * @param array  $array  the array
     * @param string $search the key to search for
     * @param string $key    the key to add
     * @param mixed  $value  the value to add
     */
    function yith_wcbk_array_add_after( &$array, $search, $key, $value ) {
        yith_wcbk_array_add( $array, $search, $key, $value, true );
    }
}

if ( !function_exists( 'yith_wcbk_array_add_before' ) ) {
    /**
     * add key and value after a specific key in array
     *
     * @param array  $array  the array
     * @param string $search the key to search for
     * @param string $key    the key to add
     * @param mixed  $value  the value to add
     */
    function yith_wcbk_array_add_before( &$array, $search, $key, $value ) {
        yith_wcbk_array_add( $array, $search, $key, $value, false );
    }
}

if ( !function_exists( 'yith_wcbk_format_decimals_with_variables' ) ) {
    function yith_wcbk_format_decimals_with_variables( $price ) {
        if ( strpos( $price, '*' ) ) {
            list( $_price, $variable ) = explode( '*', $price, 2 );
            $price = wc_format_decimal( $_price ) . '*' . $variable;
        } elseif ( strpos( $price, '/' ) ) {
            list( $_price, $variable ) = explode( '/', $price, 2 );
            $price = wc_format_decimal( $_price ) . '/' . $variable;
        } else {
            $price = wc_format_decimal( $price );
        }
        return $price;
    }
}

if ( !function_exists( 'yith_wcbk_booking_person_types_to_list' ) ) {
    function yith_wcbk_booking_person_types_to_list( $person_types ) {
        if ( $person_types && is_array( $person_types ) ) {
            $new_person_types = array();
            $is_a_list        = is_array( current( $person_types ) );

            if ( !$is_a_list ) {
                foreach ( $person_types as $person_type_id => $person_type_number ) {
                    $person_type_title  = get_the_title( $person_type_id );
                    $new_person_types[] = array(
                        'id'     => $person_type_id,
                        'title'  => $person_type_title,
                        'number' => $person_type_number,
                    );
                }
            } else {
                $new_person_types = $person_types;
            }
            return $new_person_types;
        }

        return array();
    }
}

if ( !function_exists( 'yith_wcbk_booking_person_types_to_id_number_array' ) ) {
    function yith_wcbk_booking_person_types_to_id_number_array( $person_types ) {
        if ( $person_types && is_array( $person_types ) ) {
            $new_person_types      = array();
            $is_an_id_number_array = !is_array( current( $person_types ) );

            if ( !$is_an_id_number_array ) {
                foreach ( $person_types as $person_type ) {
                    $new_person_types[ $person_type[ 'id' ] ] = $person_type[ 'number' ];
                }
            } else {
                $new_person_types = $person_types;
            }

            return $new_person_types;
        }

        return array();
    }
}


if ( !function_exists( 'yith_wcbk_get_person_type_title' ) ) {
    /**
     * @param int $person_type_id
     * @return string
     */
    function yith_wcbk_get_person_type_title( $person_type_id ) {
        return YITH_WCBK()->person_type_helper->get_person_type_title( $person_type_id );
    }
}

/**
 * Conditionals
 * --------------------------------------------------
 */
if ( !function_exists( 'yith_wcbk_is_debug' ) ) {
    /**
     * return true if debug is active
     *
     * @return bool
     */
    function yith_wcbk_is_debug() {
        return 'yes' === get_option( 'yith-wcbk-debug', 'no' );
    }
}

if ( !function_exists( 'yith_wcbk_is_in_search_form_result' ) ) {

    function yith_wcbk_is_in_search_form_result() {
        return defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) && YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS;
    }
}


/**
 * Print fields and templates functions
 * --------------------------------------------------
 */
if ( !function_exists( 'yith_wcbk_print_field' ) ) {
    function yith_wcbk_print_field( $args = array(), $echo = true ) {
        if ( !$echo )
            ob_start();

        YITH_WCBK_Printer()->print_field( $args );

        if ( !$echo )
            return ob_get_clean();

        return '';
    }
}

if ( !function_exists( 'yith_wcbk_print_svg' ) ) {
    function yith_wcbk_print_svg( $svg, $echo = true ) {
        return yith_wcbk_print_field( array( 'type' => 'svg', 'svg' => $svg ), $echo );
    }
}

if ( !function_exists( 'yith_wcbk_print_fields' ) ) {
    function yith_wcbk_print_fields( $args = array() ) {
        YITH_WCBK_Printer()->print_fields( $args );
    }
}

if ( !function_exists( 'yith_wcbk_print_notice' ) ) {
    function yith_wcbk_print_notice( $notice, $type = 'info', $dismissible = false, $key = '' ) {
        $class = "yith-wcbk-admin-notice notice notice-{$type}";
        $class .= !!$dismissible ? ' is-dismissible' : '';

        if ( !$key ) {
            $key = md5( $notice . '_' . $type );
        }
        $key    = sanitize_key( $key );
        $cookie = 'yith_wcbk_notice_dismiss_' . $key;
        $id     = 'yith-wcbk-notice-' . $key;

        if ( $dismissible && !empty( $_COOKIE[ $cookie ] ) )
            return;

        echo "<div id='{$id}' class='{$class}'><p>{$notice}</p></div>";

        if ( $dismissible ) {
            ?>
            <script>
                jQuery( '#<?php echo $id ?>' ).on( 'click', '.notice-dismiss', function () {
                    document.cookie = "<?php echo $cookie ?>=1";
                } );
            </script>
            <?php
        }
    }
}

if ( !function_exists( 'yith_wcbk_get_view' ) ) {
    /**
     * print a view
     *
     * @param string $view
     * @param array  $args
     */
    function yith_wcbk_get_view( $view, $args = array() ) {
        $view_path = trailingslashit( YITH_WCBK_VIEWS_PATH ) . $view;
        extract( $args );
        if ( file_exists( $view_path ) ) {
            include $view_path;
        }
    }
}

if ( !function_exists( 'yith_wcbk_print_login_form' ) ) {
    /**
     * print the WooCommerce login form
     *
     * @param bool $check_logged_in
     * @param bool $add_woocommerce_container
     * @since 1.0.5
     */
    function yith_wcbk_print_login_form( $check_logged_in = false, $add_woocommerce_container = true ) {
        if ( !$check_logged_in || !is_user_logged_in() ) {
            echo !!$add_woocommerce_container ? '<div class="woocommerce">' : '';
            wc_get_template( 'myaccount/form-login.php' );
            echo !!$add_woocommerce_container ? '</div>' : '';
        }
    }
}

if ( !function_exists( 'yith_wcbk_create_date_field' ) ) {
    /**
     * create date field with time
     *
     * @param string $unit
     * @param array  $args
     * @since 2.0.0
     * @return string
     */
    function yith_wcbk_create_date_field( $unit, $args = array() ) {
        $value = isset( $args[ 'value' ] ) ? $args[ 'value' ] : '';
        $id    = isset( $args[ 'id' ] ) ? $args[ 'id' ] : '';
        $name  = isset( $args[ 'name' ] ) ? $args[ 'name' ] : '';
        $admin = isset( $args[ 'admin' ] ) ? !!$args[ 'admin' ] : true;

        $datepicker_class = $admin ? 'yith-wcbk-admin-date-picker' : 'yith-wcbk-date-picker';

        if ( !in_array( $unit, array( 'hour', 'minute' ) ) ) {
            $current_value = date_i18n( 'Y-m-d', $value );
            $field         = "<input type='text' class='$datepicker_class' id='$id' name='$name' maxlength='10' value='$current_value' pattern='[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])'/>";
        } else {
            $current_value = date_i18n( 'Y-m-d H:i', $value );
            $date_value    = date_i18n( 'Y-m-d', $value );
            $time_value    = date_i18n( 'H:i', $value );

            $field = "<input type='hidden' class='yith-wcbk-date-time-field' name='$name' data-date='#$id-date' data-time='#$id-time' value='$current_value' />";
            $field .= "<input type='text' class='$datepicker_class' id='$id-date'  maxlength='10' value='$date_value' pattern='[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])'/>";
            $field .= "<span class='yith-wcbk-date-time-field-time'>" . yith_wcbk_print_field( array( 'id' => "$id-time", 'type' => 'time-select', 'value' => $time_value ), false ) . "</span>";
        }

        return $field;
    }
}

if ( !function_exists( 'yith_wcbk_get_order_awaiting_payment' ) ) {
    /**
     * get the id of the order awaiting payment
     *
     * @return int
     */
    function yith_wcbk_get_order_awaiting_payment() {
        $cart = WC()->cart->get_cart_for_session();
        if ( $cart ) {
            $order_id  = absint( WC()->session->get( 'order_awaiting_payment' ) );
            $cart_hash = is_callable( array( WC()->cart, 'get_cart_hash' ) ) ? WC()->cart->get_cart_hash() : md5( wp_json_encode( wc_clean( $cart ) ) . WC()->cart->total ); // get_cart_hash was included in WooCommerce 3.6
            $order     = $order_id ? wc_get_order( $order_id ) : null;

            $resuming_order = $order && $order->has_cart_hash( $cart_hash ) && $order->has_status( array( 'pending', 'failed' ) );

            if ( $resuming_order ) {
                return $order_id;
            }
        }

        return 0;
    }
}