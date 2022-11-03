<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Notes' ) ) {
    /**
     * Class YITH_WCBK_Notes
     * handle Booking notes
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Notes {

        /** @var YITH_WCBK_Notes */
        private static $_instance;

        /** @var string DB table name */
        public $table_name = '';

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Notes
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Notes constructor.
         */
        private function __construct() {
            global $wpdb;
            $this->table_name = $wpdb->prefix . YITH_WCBK_DB::$booking_notes_table;
        }

        /**
         * add booking note
         *
         * @param int    $booking_id
         * @param string $type
         * @param string $note
         * @return false|int
         */
        public function add_booking_note( $booking_id, $type, $note = '' ) {
            global $wpdb;

            $is_customer_note = 'customer' === $type;
            if ( $is_customer_note ) {
                WC()->mailer();
                do_action( 'yith_wcbk_new_customer_note', array( 'booking_id' => $booking_id, 'note' => $note ) );
            }

            $insert_query = "INSERT INTO $this->table_name (`booking_id`, `type`, `description`, `note_date`) VALUES ('" . $booking_id . "', '" . $type . "', '" . $note . "' , '" . current_time( 'mysql', true ) . "' )";

            return $wpdb->query( $insert_query );
        }

        /**
         * get booking notes
         *
         * @param int $booking_id
         * @return array|null|object
         */
        public function get_booking_notes( $booking_id ) {
            global $wpdb;

            $query   = $wpdb->prepare( "SELECT * FROM $this->table_name WHERE booking_id = %d ORDER by note_date DESC", $booking_id );
            $results = $wpdb->get_results( $query );

            return $results;
        }

        /**
         * delete booking note
         *
         * @param int $note_id
         * @return false|int
         */
        public function delete_booking_note( $note_id ) {
            global $wpdb;

            $note_id = absint( $note_id );

            return $wpdb->delete( $this->table_name, array( 'id' => $note_id ), array( '%d' ) );
        }
    }
}


/**
 * Unique access to instance of YITH_WCBK_Notes class
 *
 * @return YITH_WCBK_Notes
 */
function YITH_WCBK_Notes() {
    return YITH_WCBK_Notes::get_instance();
}

if ( !function_exists( 'yith_wcbk_delete_booking_note' ) ) {
    /**
     * delete a booking note
     *
     * @param int $note_id
     */
    function yith_wcbk_delete_booking_note( $note_id ) {
        YITH_WCBK_Notes()->delete_booking_note( $note_id );
    }
}