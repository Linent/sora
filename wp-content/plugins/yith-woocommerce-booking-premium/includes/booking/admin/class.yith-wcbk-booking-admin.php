<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_Admin' ) ) {
    /**
     * Class YITH_WCBK_Booking_Admin
     *
     * manages the booking CPT, calendar, creation and menu in admin side.
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Booking_Admin {

        /** @var \YITH_WCBK_Booking_Admin */
        protected static $_instance;

        /** @var YITH_WCBK_Booking_Calendar */
        public $calendar;

        /** @var YITH_WCBK_Booking_Post_Type_Admin */
        public $booking_post_type_admin;

        /** @var YITH_WCBK_Booking_Create */
        public $booking_create;

        /** @var YITH_WCBK_Booking_Metabox */
        public $booking_metabox;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Booking_Admin
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Booking_Admin constructor.
         */
        public function __construct() {
            $this->_include_files();

            add_action( 'admin_menu', array( $this, 'customize_admin_booking_menu' ), 20 );
        }


        /**
         * Includes files
         *
         * @access protected
         */
        protected function _include_files() {
            $this->booking_metabox         = include 'class.yith-wcbk-booking-metabox.php' ;
            $this->booking_create          = include 'class.yith-wcbk-booking-create.php' ;
            $this->calendar                = include 'class.yith-wcbk-booking-calendar.php' ;
            $this->booking_post_type_admin = include 'class.yith-wcbk-booking-post-type-admin.php' ;
        }

        /**
         * Customize the default booking admin menu ( remove add-booking and add further submenus )
         */
        public function customize_admin_booking_menu() {
            $booking_menu            = 'edit.php?post_type=' . YITH_WCBK_Post_Types::$booking;
            $add_new_booking_submenu = 'post-new.php?post_type=' . YITH_WCBK_Post_Types::$booking;
            remove_submenu_page( $booking_menu, $add_new_booking_submenu );

            $this->_add_booking_info_bubble_in_menu();
        }

        /**
         * Add booking info bubble in menu
         * for booking in status pending-confirm or unpaid
         */
        private function _add_booking_info_bubble_in_menu() {
            global $menu;
            $booking_menu     = 'edit.php?post_type=' . YITH_WCBK_Post_Types::$booking;
            $statuses_to_show = array(
                'pending-confirm',
                'unpaid'
            );

            $booking_counts = (array) wp_count_posts( YITH_WCBK_Post_Types::$booking );

            $booking_counters = '';
            foreach ( $statuses_to_show as $status ) {
                $current_counter = isset( $booking_counts[ 'bk-' . $status ] ) ? absint( $booking_counts[ 'bk-' . $status ] ) : 0;
                if ( $current_counter )
                    $booking_counters .= "<span class='yith-wcbk-booking-menu-bubble {$status}'>{$current_counter}</span>";
            }

            foreach ( $menu as $i => $item ) {
                if ( $booking_menu == $item[ 2 ] ) {
                    $menu[ $i ][ 0 ] .= $booking_counters;
                    break;
                }
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCBK_Booking_Admin class
 *
 * @return YITH_WCBK_Booking_Admin
 * @since 1.0.0
 * @deprecated 2.0.0 - use YITH_WCBK_Booking_Admin::get_instance() instead
 */
function YITH_WCBK_Booking_Admin() {
    return YITH_WCBK_Booking_Admin::get_instance();
}