<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Exporter' ) ) {
    /**
     * Class YITH_WCBK_Exporter
     * manages exporting to csv, pdf, ics
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Exporter {

        /** @var YITH_WCBK_Exporter */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Exporter
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Exporter constructor.
         */
        private function __construct() {
            add_action( 'init', array( $this, 'export_action_handler' ) );
        }

        /**
         * handle the export actions
         *
         * @since 2.0.0
         */
        public function export_action_handler() {
            if ( !empty( $_REQUEST[ 'yith_wcbk_exporter_action' ] ) ) {
                switch ( $_REQUEST[ 'yith_wcbk_exporter_action' ] ) {
                    case 'export_ics':
                        if ( !empty( $_REQUEST[ 'yith_wcbk_exporter_nonce' ] ) &&
                             wp_verify_nonce( $_REQUEST[ 'yith_wcbk_exporter_nonce' ], 'export' ) &&
                             !empty( $_REQUEST[ 'product_id' ] ) ) {
                            $booking_ids = YITH_WCBK_Booking_Helper()->get_bookings_by_product( absint( $_REQUEST[ 'product_id' ] ), 'ids' );
                            $this->download_ics( $booking_ids, 'yith-booking-' . absint( $_REQUEST[ 'product_id' ] ) . '-' . date( 'Y-m-d_H-i-s' ) . '.ics' );
                        }
                        break;
                    case 'export_future_ics':
                        if ( !empty( $_REQUEST[ 'product_id' ] ) ) {
                            /** @var WC_Product_Booking $product */
                            $product = wc_get_product( $_REQUEST[ 'product_id' ] );
                            if ( $product ) {
                                if ( isset( $_REQUEST[ 'key' ] ) && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) && $product->is_valid_external_calendars_key( $_REQUEST[ 'key' ] ) ) {
                                    $booking_ids = YITH_WCBK_Booking_Helper()->get_future_bookings_by_product( absint( $_REQUEST[ 'product_id' ] ), 'ids' );
                                    $this->download_ics( $booking_ids, 'yith-booking-' . absint( $_REQUEST[ 'product_id' ] ) . '-' . date( 'Y-m-d_H-i-s' ) . '.ics' );
                                }
                            }
                        }
                        wp_die( __( 'Something went wrong.', 'yith-booking-for-woocommerce' ) );
                        break;
                    default:
                        break;
                }

            }
        }

        /**
         * transform an array to CSV file
         *
         * @param        $array
         * @param string $filename
         * @param string $delimiter
         */
        private function _array_to_csv_download( $array, $filename = 'export.csv', $delimiter = ',' ) {
            self::download_headers( $filename );
            $f = fopen( 'php://output', 'w' );

            foreach ( $array as $line ) {
                fputcsv( $f, $line, $delimiter );
            }
            exit;
        }

        private static function download_headers( $filename ) {
            $filename_array = explode( '.', $filename );
            $content_type   = strpos( $filename, '.' ) > 0 ? end( $filename_array ) : 'txt';

            if ( 'ics' === $content_type ) {
                $content_type = 'text/calendar';
            }

            header( "X-Robots-Tag: noindex, nofollow", true );
            header( "Content-Type: " . $content_type . "; charset=" . get_option( 'blog_charset' ), true );
            header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
        }

        /**
         * Generate the template
         *
         * @param int  $booking_id
         * @param bool $is_admin
         */
        public function generate_pdf( $booking_id, $is_admin = true ) {
            // disable error reporting to prevent issues when generating PDF
            error_reporting( 0 );

            $filename = apply_filters( 'yith_wcbk_pdf_file_name', "booking_$booking_id.pdf", $booking_id, $is_admin );
            $booking  = yith_get_booking( $booking_id );

            ob_start();
            wc_get_template( 'booking/pdf/booking.php', array( 'booking' => $booking, 'booking_id' => $booking_id, 'is_admin' => $is_admin ), '', YITH_WCBK_TEMPLATE_PATH );
            $html = ob_get_clean();

            require_once YITH_WCBK_DOMPDF_DIR . 'autoload.inc.php';
            $pdf = new Dompdf\Dompdf();
            $pdf->loadHtml( $html );
            $pdf->render();

            $pdf_options = array( 'Attachment' => 0 ); // 0 -> open | 1 -> download

            $pdf->stream( $filename, $pdf_options );
            die();
        }

        /**
         * Download bookings in a file
         *
         * @param array $post_ids array of booking ids
         */
        public function download_csv( $post_ids ) {
            $post_ids = !!$post_ids ? (array) $post_ids : array();

            $booking_fields = apply_filters( 'yith_wcbk_csv_fields', array(
                'booking_id',
                'product_id',
                'product_name',
                'date',
                'status',
                'order_id',
                'user_id',
                'username',
                'duration',
                'from',
                'to',
                'services',
                'persons',
                'person_types',
            ) );

            $csv_array = array( $booking_fields );

            foreach ( $post_ids as $post_id ) {
                $booking = yith_get_booking( $post_id );
                if ( $booking->is_valid() ) {
                    $current_booking = array();
                    foreach ( $booking_fields as $booking_field ) {
                        $val = '';
                        switch ( $booking_field ) {
                            case 'booking_id':
                                $val = $booking->id;
                                break;
                            case 'product_id':
                                $val = !empty( $booking->product_id ) ? $booking->product_id : '';
                                break;
                            case 'product_name':
                                $val = get_the_title( $booking->product_id );
                                break;
                            case 'date':
                                $val = date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $booking->post->post_date ) );
                                break;
                            case 'status':
                                $val = $booking->get_status_text();
                                break;
                            case 'order_id':
                                $val = !empty( $booking->order_id ) ? $booking->order_id : '';
                                break;
                            case 'user_id':
                                $val = !empty( $booking->user_id ) ? $booking->user_id : '';
                                break;
                            case 'username':
                                $val = '';
                                if ( !empty( $booking->user_id ) ) {
                                    $user_id = absint( $booking->user_id );
                                    $user    = get_user_by( 'id', $user_id );
                                    $val     = $user instanceof WP_User ? $user->nickname : '';
                                }
                                break;
                            case 'duration':
                                $val = $booking->get_duration_html();
                                break;
                            case 'from':
                                $val = $booking->get_formatted_date( 'from' );
                                break;
                            case 'to':
                                $val = $booking->get_formatted_date( 'to' );
                                break;
                            case 'services':
                                $services = $booking->get_service_names();

                                $val = !!$services ? implode( ', ', $services ) : '';
                                break;

                            case 'persons':
                                $val = !empty( $booking->persons ) ? $booking->persons : '';
                                break;

                            case 'person_types':
                                $val = '';
                                if ( $booking->has_person_types() ) {
                                    foreach ( $booking->person_types as $person_type ) {
                                        $id     = isset( $person_type[ 'id' ] ) ? $person_type[ 'id' ] : false;
                                        $title  = !!( $person_type[ 'title' ] ) ? $person_type[ 'title' ] : false;
                                        $number = isset( $person_type[ 'number' ] ) ? $person_type[ 'number' ] : false;

                                        if ( $id === false || $title === false || !$number )
                                            continue;

                                        $person_type_title = get_the_title( $id );
                                        $title             = !!$person_type_title ? $person_type_title : $title;
                                        $val               .= "$title($number) ";
                                    }
                                }
                                break;

                        }

                        $hook              = 'yith_wcbk_csv_field_' . $booking_field;
                        $field_value       = apply_filters( $hook, $val, $booking );
                        $current_booking[] = apply_filters( 'yith_wcbk_csv_field_value', $field_value, $booking_field, $booking );
                    }

                    $csv_array[] = $current_booking;
                }

            }

            $delimiter = apply_filters( 'yith_wcbk_csv_delimiter', ',' );
            $filename  = apply_filters( 'yith_wcbk_csv_file_name', 'yith-bookings-' . date( 'Y-m-d' ) . '.csv' );

            $this->_array_to_csv_download( $csv_array, $filename, $delimiter );
        }

        /**
         * @param             $post_ids
         * @param bool|string $filename
         */
        public function download_ics( $post_ids, $filename = false ) {
            $filename = $filename !== false ? $filename : 'yith-bookings-' . date( 'Y-m-d_H-i-s' ) . '.ics';
            $filename = apply_filters( 'yith_wcbk_ics_file_name', $filename );

            empty( $_GET[ 'yith_wcbk_exporter_debug' ] ) && self::download_headers( $filename );
            echo $this->get_ics( $post_ids, current_user_can( 'yith_manage_bookings' ) );

            exit;
        }

        /**
         * Download bookings in a ics file
         *
         * @param int|array $post_ids array of booking ids
         * @param bool      $is_admin
         * @param bool      $force_time
         * @return string
         */
        public function get_ics( $post_ids, $is_admin = false, $force_time = false ) {
            $post_ids         = !!$post_ids ? (array) $post_ids : array();
            $blog_name        = get_bloginfo( 'name' );
            $date_format      = 'Ymd';
            $date_time_format = 'Ymd\THis';
            $home_url         = $this->get_home_url();
            $home_url         = str_replace( '/', '_', $home_url );
            $timezone_offset  = get_option( 'gmt_offset' );
            $timezone_string  = get_option( 'timezone_string' );

            $rows = array(
                'BEGIN:VCALENDAR',
                'VERSION:2.0',
                "PRODID:-//$blog_name, by YITH Booking and Appointment for WooCommerce//NONSGML v1.0",
                'METHOD:REQUEST',
                'CALSCALE:GREGORIAN',
            );
            if ( $timezone_string ) {
                $rows[] = "X-WR-TIMEZONE:$timezone_string";
            }

            foreach ( $post_ids as $id ) {
                $booking = yith_get_booking( $id );
                if ( $booking->is_valid() ) {
                    $rows[] = 'BEGIN:VEVENT';
                    $rows[] = 'DTSTAMP:' . date( $date_time_format, strtotime( 'now' ) );

                    if ( !$booking->has_time() ) {
                        $to = $booking->to;
                        if ( $booking->is_all_day() ) {
                            $to = YITH_WCBK_Date_Helper()->get_time_sum( $to, 1, 'day' );
                        }

                        $from_row = 'DTSTART;VALUE=DATE:' . date( $date_format, $booking->from );
                        $to_row   = 'DTEND;VALUE=DATE:' . date( $date_format, $to );

                        if ( $force_time && ( $product = $booking->get_product() ) && $product->get_check_in() && $product->get_check_out() ) {
                            $check_in  = str_replace( ':', '', yith_wcbk_string_to_time_slot( $product->get_check_in() ) );
                            $check_out = str_replace( ':', '', yith_wcbk_string_to_time_slot( $product->get_check_out() ) );
                            if ( $check_in && $check_out ) {
                                $from_row = 'DTSTART:' . date( $date_format, $booking->from ) . "T{$check_in}00";
                                $to_row   = 'DTEND:' . date( $date_format, $to ) . "T{$check_out}00";
                            }
                        }
                    } else {
                        $from_row = 'DTSTART:' . date( $date_time_format, $booking->from );
                        $to_row   = 'DTEND:' . date( $date_time_format, $booking->to );
                    }

                    $rows[] = $from_row;
                    $rows[] = $to_row;


                    $timezone_offset_sign = $timezone_offset >= 0 ? '+' : '-';
                    $timezone_offset_value = absint( $timezone_offset );
                    $timezone_offset_value = $timezone_offset_value < 10 ? ('0' . $timezone_offset_value) : $timezone_offset_value;

                    $rows[] = 'TZOFFSETTO:' . sprintf( '%s%s00', $timezone_offset_sign, $timezone_offset_value );


                    if ( !empty( $booking->location ) ) {
                        $rows[] = 'LOCATION:' . $booking->location;
                    }

                    $rows[] = 'UID:' . 'booking_' . $id . '@' . $home_url;

                    $summary = $booking->get_title();
                    if ( $is_admin && $booking->user_id ) {
                        $user_id   = absint( $booking->user_id );
                        $user      = get_user_by( 'id', $user_id );
                        $user_info = '';
                        if ( $user ) {
                            $user_info = ' - ' . esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')';
                        }
                        $summary .= $user_info;
                    }
                    $summary = apply_filters( 'yith_wcbk_ics_event_summary', $summary, $booking, $is_admin );
                    $rows[]  = 'SUMMARY:' . $summary;

                    $description_data = array(
                        'FROM'   => $booking->get_formatted_date( 'from' ),
                        'TO'     => $booking->get_formatted_date( 'to' ),
                        'STATUS' => $booking->get_status_text(),
                        'PEOPLE' => $booking->persons,
                    );

                    if ( $services = $booking->get_service_names( $is_admin ) ) {
                        $description_data[ 'SERVICES' ] = implode( ', ', $services );
                    }

                    $description_data = apply_filters( 'yith_wcbk_ics_event_description_data', $description_data, $booking, $is_admin );
                    $description_rows = array();
                    foreach ( $description_data as $label => $data ) {
                        $description_rows[] .= "{$label}: $data";
                    }
                    $description = implode( '\n', $description_rows );
                    $description = apply_filters( 'yith_wcbk_ics_event_description', $description, $booking, $is_admin );

                    $rows[] = 'DESCRIPTION:' . $description;

                    $rows[] = 'END:VEVENT';
                }
            }

            $rows[] = 'END:VCALENDAR';
            $ics    = implode( "\r\n", $rows );

            return $ics;
        }

        /**
         * get the home url
         *
         * @return string
         */
        private function get_home_url() {
            $home_url = home_url();
            $schemes  = apply_filters( 'yith_wcbk_exporter_home_url_schemes', array( 'https://', 'http://', 'www.' ) );

            foreach ( $schemes as $scheme ) {
                $home_url = str_replace( $scheme, '', $home_url );
            }

            if ( strpos( $home_url, '?' ) !== false ) {
                list( $base, $query ) = explode( '?', $home_url, 2 );
                $home_url = $base;
            }

            $home_url = untrailingslashit( $home_url );

            return apply_filters( 'yith_wcbk_exporter_get_home_url', $home_url );
        }
    }
}
