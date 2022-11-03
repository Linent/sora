<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_ICS_Parser' ) ) {
    /**
     * Class YITH_WCBK_ICS_Parser
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  2.0.0
     */
    class YITH_WCBK_ICS_Parser {
        /** @var string */
        private $_ics = '';

        /** @var array */
        private $_extra_props = array();

        /** @var array */
        private $_lines = array();

        /** @var  string */
        private $_prod_id;

        /** @var YITH_WCBK_Booking_External[] */
        private $_events = array();

        /** @var YITH_WCBK_Booking_External */
        private $_current_event;

        /** @var array */
        private $_counter_begin_end = array();

        /**
         * YITH_WCBK_ICS_Parser constructor.
         *
         * @param string $ics
         * @param array  $extra_props
         */
        public function __construct( $ics, $extra_props = array() ) {
            $this->_ics         = $ics;
            $this->_extra_props = $extra_props;

            $this->_unfold();

            if ( !$this->_lines )
                $this->_error( 101 );

            if ( rtrim( current( $this->_lines ) ) !== 'BEGIN:VCALENDAR' )
                $this->_error( 102 );

            $this->parse();

            $this->_check_errors();
        }

        /**
         * Unfolds an iCal file in lines before parsing
         */
        private function _unfold() {
            $string       = str_replace( '\n', '\\n', $this->_ics );
            $string       = preg_replace( '/' . PHP_EOL . '[ \t]/', '', $string );
            $this->_lines = explode( PHP_EOL, $string );
        }


        /**
         * throw a new exception for error
         *
         * @param int $err_no
         *
         * @throws Exception
         */
        private function _error( $err_no ) {
            $err_no  = absint( $err_no );
            $errors  = array(
                100 => __( 'Generic Error', 'ICS Parser Error', 'yith-booking-for-woocommerce' ),
                101 => __( 'ICS file seems to be empty', 'ICS Parser Error', 'yith-booking-for-woocommerce' ),
                102 => __( 'Malformed ICS', 'ICS Parser Error', 'yith-booking-for-woocommerce' ),
            );
            $err_msg = array_key_exists( $err_no, $errors ) ? $errors[ $err_no ] : $errors[ 100 ];

            $error = sprintf( __( 'Error %1$s: %2$s', 'ICS Parser Error', 'yith-booking-for-woocommerce' ), $err_no, $err_msg );

            throw new Exception( $error );
        }

        /**
         * check for errors
         */
        private function _check_errors() {
            if ( $this->_counter_begin_end ) {
                $unique_values = array_unique( array_values( $this->_counter_begin_end ) );
                if ( $unique_values !== array( 0 ) )
                    $this->_error( 102 );
            }
        }

        /**
         * set property for the current event
         *
         * @param string $key
         * @param string $value
         */
        private function _set_prop_to_current_event( $key, $value ) {
            if ( !is_null( $this->_current_event ) ) {
                $this->_current_event->set( $key, $value );
            }
        }

        /**
         * set default props for the current event
         */
        private function _set_default_props_in_current_event() {
            $this->_set_prop_to_current_event( 'source', $this->get_source() );
            $this->_set_prop_to_current_event( 'date', time() );

            $this->_set_extra_props_in_current_event();
        }

        /**
         * set extra props for the current event
         */
        private function _set_extra_props_in_current_event() {
            foreach ( $this->_extra_props as $key => $value ) {
                $this->_set_prop_to_current_event( $key, $value );
            }
        }

        /**
         * retrieve key value from string
         *
         * example if text = "BEGIN:VCALENDAR" it will return array( 'BEGIN', 'VCALENDAR')
         *
         * @param string $text
         *
         * @return array|bool
         */
        private function _key_value_from_string( $text ) {
            preg_match( "/([^:]+)[:]([\w\W]*)/", $text, $matches );
            if ( count( $matches ) == 0 ) {
                return false;
            }
            $matches = array_splice( $matches, 1, 2 );

            return $matches;
        }

        /**
         * retrieve key-params from a key
         *
         * example if key = "DTEND;VALUE=DATE" it will return array( 'DTEND', array( 'VALUE' => 'DATE' ))
         *
         * @param string $key
         *
         * @return array|bool
         */
        private function _key_params_from_key( $key ) {
            $params = array();
            if ( strpos( $key, ';' ) !== false ) {
                list( $key, $string_params ) = explode( ';', $key, 2 );

                if ( strpos( $string_params, '=' ) !== false ) {
                    $temp_params = explode( '=', $string_params, 2 );
                    $params      = array( $temp_params[ 0 ] => $temp_params[ 1 ] );
                }
            }

            return array( $key, $params );
        }


        /**
         * retrieve the timestamp from an iCal date
         *
         * @param string $ical_date
         *
         * @return bool|false|int
         */
        public function ical_date_to_timestamp( $ical_date ) {
            $is_utc_time = 'Z' === substr( $ical_date, -1 );
            $ical_date   = str_replace( 'T', '', $ical_date );
            $ical_date   = str_replace( 'Z', '', $ical_date );
            $pattern     = '/([0-9]{4})';      // 1 YYYY
            $pattern     .= '([0-9]{2})';      // 2 MM
            $pattern     .= '([0-9]{2})';      // 3 DD
            $pattern     .= '([0-9]{0,2})';    // 4 HH
            $pattern     .= '([0-9]{0,2})';    // 5 MM
            $pattern     .= '([0-9]{0,2})/';   // 6 SS
            preg_match( $pattern, $ical_date, $date );

            // Unix timestamp can't represent dates before 1970
            if ( $date[ 1 ] < 1970 ) {
                return false;
            }

            // Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow if 32 bit integers are used.
            $timestamp = mktime( (int)$date[ 4 ],
                                 (int)$date[ 5 ],
                                 (int)$date[ 6 ],
                                 (int)$date[ 2 ],
                                 (int)$date[ 3 ],
                                 (int)$date[ 1 ] );
            if ( $is_utc_time ) {
                $timezone_offset = get_option( 'gmt_offset' );
                $timestamp       += $timezone_offset * HOUR_IN_SECONDS;
            }

            return $timestamp;
        }

        /**
         * Let's start the parsing
         */
        public function parse() {
            foreach ( $this->_lines as $line_number => $line ) {
                $line      = trim( $line );
                $key_value = $this->_key_value_from_string( $line );
                if ( $key_value ) {
                    list( $complete_key, $value ) = $key_value;
                    list( $key, $params ) = $this->_key_params_from_key( $complete_key );

                    switch ( $key ) {
                        case 'BEGIN':
                            if ( isset( $this->_counter_begin_end[ $value ] ) ) {
                                $this->_counter_begin_end[ $value ]++;
                            } else {
                                $this->_counter_begin_end[ $value ] = 1;
                            }

                            if ( 'VEVENT' === $value ) {
                                if ( is_null( $this->_current_event ) ) {
                                    $this->_current_event = new YITH_WCBK_Booking_External();
                                    $this->_set_default_props_in_current_event();
                                } else {
                                    $this->_error( 102 );
                                }
                            }

                            break;

                        case 'END':
                            if ( isset( $this->_counter_begin_end[ $value ] ) ) {
                                $this->_counter_begin_end[ $value ]--;
                            } else {
                                $this->_counter_begin_end[ $value ] = -1;
                            }

                            if ( 'VEVENT' === $value ) {
                                if ( is_null( $this->_current_event ) ) {
                                    $this->_error( 102 );
                                } else {
                                    $this->_events[]      = $this->_current_event;
                                    $this->_current_event = null;
                                }
                            }
                            break;

                        case 'PRODID':
                            $this->set_prod_id( $value );
                            break;

                        case 'DTSTART':
                            $this->_set_prop_to_current_event( 'from', $this->ical_date_to_timestamp( $value ) );
                            break;

                        case 'DTEND':
                            $this->_set_prop_to_current_event( 'to', $this->ical_date_to_timestamp( $value ) );
                            break;

                        case 'DESCRIPTION':
                        case 'UID':
                        case 'SUMMARY':
                        case 'LOCATION':
                            $this->_set_prop_to_current_event( strtolower( $key ), $value );
                            break;

                    }
                }
            }
        }



        /*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        */

        /**
         * return events
         *
         * @return array
         */
        public function get_events() {
            return $this->_events;
        }

        /**
         * get the source from prod_id
         *
         * @return string
         */
        public function get_source() {
            $prod_id = $this->get_prod_id();
            $source  = $prod_id;
            preg_match( "/([^-\/\/]+)[\/\/]*/", $prod_id, $matches );
            if ( count( $matches ) >= 2 && !empty( $matches[ 1 ] ) ) {
                $source = $matches[ 1 ];
            }

            return $source;
        }

        /**
         * return lines
         *
         * @return array
         */
        public function get_lines() {
            return $this->_lines;
        }

        /**
         * return the prod_id
         *
         * @return string
         */
        public function get_prod_id() {
            return $this->_prod_id;
        }


        /*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        */

        /**
         * set the prod_id if not set
         *
         * @param $prod_id
         */
        public function set_prod_id( $prod_id ) {
            if ( is_null( $this->_prod_id ) ) {
                $this->_prod_id = $prod_id;
            }
        }
    }
}