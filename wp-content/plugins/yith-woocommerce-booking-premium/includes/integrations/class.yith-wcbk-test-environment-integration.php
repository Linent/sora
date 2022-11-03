<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Test_Environment_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   2.0.7
 */
class YITH_WCBK_Test_Environment_Integration extends YITH_WCBK_Integration {
    /** @var YITH_WCBK_Test_Environment_Integration */
    protected static $_instance;

    /**
     * Constructor
     *
     * @param bool $plugin_active
     * @param bool $integration_active
     *
     * @access protected
     */
    protected function __construct( $plugin_active, $integration_active ) {
        parent::__construct( $plugin_active, $integration_active );

        if ( $this->is_active() ) {
            add_filter( 'ywtenv_run_replace_tables_list', array( $this, 'exclude_booking_tables' ), 10, 2 );
        }
    }

    /**
     * exclude booking tables for searching and replacing site URL
     *
     * @param array  $tables_list
     * @param string $target_prefix
     * @return array
     */
    public function exclude_booking_tables( $tables_list, $target_prefix ) {
        $table_to_exclude = array(
            $target_prefix . YITH_WCBK_DB::$booking_notes_table,
            $target_prefix . YITH_WCBK_DB::$external_bookings_table,
            $target_prefix . YITH_WCBK_DB::$log_table,
        );

        foreach ( $tables_list as $key => $value ) {
            if ( is_array( $value ) ) {
                $table_name = current( $value );
                if ( is_string( $table_name ) && in_array( $table_name, $table_to_exclude ) ) {
                    unset( $tables_list[ $key ] );
                }
            }
        }

        return $tables_list;
    }

}