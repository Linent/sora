<?php
/**
 * Deprecated functions
 * Where functions come to die.
 *
 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
 */
!defined( 'YITH_WCBK' ) && exit;

if ( !function_exists( 'yith_wcbk_show_deprecated_notices' ) ) {
    function yith_wcbk_show_deprecated_notices() {
        return false;
    }
}

if ( !function_exists( 'yith_wcbk_deprecated_function' ) ) {
    /**
     * Wrapper for deprecated functions so we can apply some extra logic.
     *
     * @since 2.1
     * @param string $function    Function used.
     * @param string $version     Version the message was added in.
     * @param string $replacement Replacement for the called function.
     */
    function yith_wcbk_deprecated_function( $function, $version, $replacement = null ) {
        if ( yith_wcbk_show_deprecated_notices() ) {
            if ( is_ajax() ) {
                do_action( 'deprecated_function_run', $function, $replacement, $version );
                $log_string = "The {$function} function is deprecated since version {$version}.";
                $log_string .= $replacement ? " Replace with {$replacement}." : '';
                error_log( $log_string );
            } else {
                _deprecated_function( $function, $version, $replacement );
            }
        }
    }
}

if ( !function_exists( 'yith_wcbk_deprecated_hook' ) ) {
    /**
     * Wrapper for deprecated hook so we can apply some extra logic.
     *
     * @since 2.1
     * @param string $hook        The hook that was used.
     * @param string $version     The version of WordPress that deprecated the hook.
     * @param string $replacement The hook that should have been used.
     * @param string $message     A message regarding the change.
     */
    function yith_wcbk_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
        if ( yith_wcbk_show_deprecated_notices() ) {

            if ( is_ajax() ) {
                do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

                $message    = empty( $message ) ? '' : ' ' . $message;
                $log_string = "{$hook} is deprecated since version {$version}";
                $log_string .= $replacement ? "! Use {$replacement} instead." : ' with no alternative available.';

                error_log( $log_string . $message );
            } else {
                _deprecated_hook( $hook, $version, $replacement, $message );
            }
        }
    }
}

if ( !function_exists( 'yith_wcbk_deprecated_filter' ) ) {
    /**
     * Wrapper for deprecated filter hook so we can apply some extra logic.
     *
     * @since 2.1
     * @param string $hook        The hook that was used.
     * @param string $version     The version of WordPress that deprecated the hook.
     * @param string $replacement The hook that should have been used.
     * @param string $message     A message regarding the change.
     */
    function yith_wcbk_deprecated_filter( $hook, $version, $replacement = null, $message = null ) {
        if ( has_filter( $hook ) ) {
            yith_wcbk_deprecated_hook( $hook . ' filter', $version, $replacement, $message );
        }
    }
}

if ( !function_exists( 'yith_wcbk_deprecated_action' ) ) {
    /**
     * Wrapper for deprecated action hook so we can apply some extra logic.
     *
     * @since 2.1
     * @param string $hook        The hook that was used.
     * @param string $version     The version of WordPress that deprecated the hook.
     * @param string $replacement The hook that should have been used.
     * @param string $message     A message regarding the change.
     */
    function yith_wcbk_deprecated_action( $hook, $version, $replacement = null, $message = null ) {
        if ( has_action( $hook ) ) {
            yith_wcbk_deprecated_hook( $hook . ' action', $version, $replacement, $message );
        }
    }
}

/** ------------------------------------------------------------------------------
 * Deprecated functions
 */

if ( !function_exists( 'yith_wcbk_parse_booking_person_types_array' ) ) {
    /**
     * Parse booking person types
     *
     * @deprecated since 2.1 | use yith_wcbk_booking_person_types_to_list and yith_wcbk_booking_person_types_to_id_number_array instead
     */
    function yith_wcbk_parse_booking_person_types_array( $person_types, $reverse = false ) {
        yith_wcbk_deprecated_function( 'yith_wcbk_parse_booking_person_types_array', '2.1', 'yith_wcbk_booking_person_types_to_list and yith_wcbk_booking_person_types_to_id_number_array' );
        return !$reverse ? yith_wcbk_booking_person_types_to_list( $person_types ) : yith_wcbk_booking_person_types_to_id_number_array( $person_types );
    }
}