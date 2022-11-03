<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Background_Process_Google_Calendar_Sync' ) ) {
    /**
     * Class YITH_WCBK_Background_Process_Google_Calendar_Sync
     *
     * handle Google Calendar Sync in background
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Background_Process_Google_Calendar_Sync extends YITH_WCBK_Background_Process {
        /**
         * retrieve the action type
         *
         * @return string
         */
        public function get_action_type() {
            return 'google_calendar_sync';
        }

        /**
         * return a list of notices to show
         *
         * @return array
         */
        public function get_notices() {
            return array(
                'start'   => __( 'Google Calendar Sync - your booking products are being synchronized in the background.', 'yith-booking-for-woocommerce' ),
                'running' => __( 'Google Calendar Sync - your booking products are being synchronized in the background.', 'yith-booking-for-woocommerce' ),
                'complete' => __( 'Google Calendar Sync - synchronization completed!', 'yith-booking-for-woocommerce' ),
            );
        }
    }
}
