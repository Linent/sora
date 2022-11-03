<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Date_Helper' ) ) {
    /**
     * Class YITH_WCBK_Date_Helper
     * do you need help with dates? Use me!
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Date_Helper {

        /** @var YITH_WCBK_Date_Helper */
        private static $_instance;

        /** @var array Day names */
        private $_days = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

        /** @var array Month names */
        private $_months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Date_Helper
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Date_Helper constructor.
         */
        private function __construct() {
            $this->_init();
        }

        /**
         * init attributes
         */
        private function _init() {

        }

        /**
         * check if one date range intersects other date range
         *
         * @param int $start1 timestamp
         * @param int $end1   timestamp
         * @param int $start2 timestamp
         * @param int $end2   timestamp
         * @return bool
         */
        public function check_intersect_dates( $start1, $end1, $start2, $end2 ) {
            return $start1 <= $end2 && $end1 >= $start2;
        }


        /**
         * Get array of days or months
         *
         * @param string $type  allowed types are "days" and "months"
         * @param bool   $short set true to crop the name to 3 letters (Jan, Feb, Mar, ...); set to false to not crop
         * @return array
         */
        public function get_names_array( $type, $short = false ) {
            $allowed = array( 'days', 'months' );
            if ( !in_array( $type, $allowed ) )
                return array();

            $ret = 'days' === $type ? $this->_days : $this->_months;

            if ( $short ) {
                $length       = 3;
                $array_count  = count( $ret );
                $zero_array   = array_fill( 0, $array_count, 0 );
                $lenght_array = array_fill( 0, $array_count, $length );
                $ret          = array_map( 'substr', $ret, $zero_array, $lenght_array );
            }


            return $ret;
        }

        /**
         * @param int  $value the number of the day 1: Monday | 2:Tuesday
         * @param bool $short set true to crop the name to 3 letters (Mon, Tue, Wed, ...); set to false to not crop
         * @return string
         */
        public function get_day_name( $value, $short = false ) {
            $value = absint( $value - 1 ) % 7;
            $days  = $this->get_names_array( 'days', $short );

            return isset( $days[ $value ] ) ? $days[ $value ] : '';
        }

        /**
         * @param int  $value the number of the day 1: January | 2:February
         * @param bool $short set true to crop the name to 3 letters (Jan, Feb, Mar, ...); set to false to not crop
         * @return string
         */
        public function get_month_name( $value, $short = false ) {
            $value  = absint( $value - 1 ) % 12;
            $months = $this->get_names_array( 'months', $short );

            return isset( $months[ $value ] ) ? $months[ $value ] : '';
        }

        /**
         * get timestamp of first day of month searched
         *
         * @param int        $timestamp
         * @param int|string $month
         * @param bool       $include_current_month
         * @return int
         */
        public function get_first_month_from_date( $timestamp, $month, $include_current_month = false ) {
            if ( is_numeric( $month ) ) {
                $month = $this->get_month_name( $month );
            }

            if ( !$include_current_month ) {
                $timestamp = strtotime( '+1 month', $timestamp );
            }
            $first_of_searched_month = strtotime( 'first day of ' . $month, $timestamp );
            $first_day_of_timestamp  = strtotime( 'first day', $timestamp );

            if ( $first_day_of_timestamp > $first_of_searched_month )
                $first_of_searched_month = strtotime( '+ 1 year', $first_of_searched_month );

            return $first_of_searched_month;
        }

        /**
         * get timestamp of first day searched
         * for example can be used to search the next monday since a date
         *
         * @param int        $timestamp
         * @param int|string $day
         * @param bool       $include_current_day
         * @return int
         */
        public function get_first_day_from_date( $timestamp, $day, $include_current_day = false ) {
            if ( is_numeric( $day ) ) {
                $day = $this->get_day_name( $day );
            }
            if ( $include_current_day ) {
                $timestamp -= DAY_IN_SECONDS;
            }

            $first_day = strtotime( 'next ' . $day, $timestamp );

            return $first_day;
        }


        /**
         * get timestamp for searched day in this week
         *
         * @param int $timestamp
         * @param int $day
         * @return int
         */
        public function get_day_on_this_week( $timestamp, $day ) {
            $day_number     = date( 'N', $timestamp );
            $day_difference = $day - $day_number;

            return strtotime( $day_difference . ' days midnight', $timestamp );
        }

        /**
         * get timestamp of the next number week of the year
         *
         * @param int  $timestamp
         * @param int  $week_number
         * @param bool $include_current_week
         * @return int
         */
        public function get_first_week_from_date( $timestamp, $week_number, $include_current_week = false ) {
            $current_week_number         = absint( date( 'W', $timestamp ) );
            $year_of_current_week_number = absint( date( 'o', $timestamp ) );

            if ( $current_week_number > $week_number || ( $current_week_number == $week_number && !$include_current_week ) ) {
                $year_of_current_week_number++;
            }

            if ( $week_number < 10 ) {
                $week_number = '0' . $week_number;
            }

            $operator = $year_of_current_week_number . 'W' . $week_number;
            $first    = strtotime( $operator );

            return $first;
        }

        /**
         * Retrieve the time sum
         *
         * @param int        $time the timestamp
         * @param int        $number
         * @param string     $unit
         * @param bool|false $midnight
         * @return int
         */
        public function get_time_sum( $time, $number = 0, $unit = 'day', $midnight = false ) {
            $sum = $time;

            $params = $midnight ? 'midnight' : '';

            $operator   = $number >= 0 ? '+' : '-';
            $abs_number = abs( $number );

            switch ( $unit ) {
                case 'month':
                    $sum = strtotime( $operator . $abs_number . ' months ' . $params, $time );
                    break;
                case 'day':
                    $sum = strtotime( $operator . $abs_number . ' days ' . $params, $time );
                    break;
                case 'hour':
                    $sum = $time + ( $number * 60 * 60 );
                    break;
                case 'minute':
                    $sum = $time + ( $number * 60 );
                    break;
                case 'seconds':
                    $sum = $time + $number;
                    break;
            }

            return $sum;
        }

        /**
         * retrieve the time difference
         *
         * @param int    $timestamp1
         * @param int    $timestamp2
         * @param string $return
         * @return bool|DateInterval|int|mixed
         */
        public function get_time_diff( $timestamp1, $timestamp2, $return = 'interval' ) {
            $date1 = new DateTime();
            $date2 = new DateTime();
            $date1->setTimestamp( $timestamp1 );
            $date2->setTimestamp( $timestamp2 );

            $interval = date_diff( $date1, $date2 );

            switch ( $return ) {
                case 'year':
                case 'y':
                    return $interval->y;
                case 'month':
                case 'm':
                    return $interval->y * 12 + $interval->m;
                case 'day':
                case 'd':
                    return $interval->days;
                case 'hour':
                case 'h':
                    return $interval->days * 24 + $interval->h;
                case 'minute':
                case 'i':
                    return $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                case 'seconds':
                case 's':
                    return $interval->days * DAY_IN_SECONDS + $interval->h * HOUR_IN_SECONDS + $interval->i * MINUTE_IN_SECONDS + $interval->s;
                default:
                    // interval
                    return $interval;
            }
        }

        /**
         * create a numeric range array
         *
         * @param int $from
         * @param int $to
         * @param int $min
         * @param int $max
         * @return array
         */
        public function create_numeric_range( $from, $to, $max = 0, $min = 0 ) {
            if ( $max == 0 ) {
                $from = min( $from, $to );
                $to   = max( $from, $to );
                $from = max( $from, $min );

                return range( $from, $to );
            } else {
                if ( $from <= $to ) {
                    $from = max( $from, $min );
                    $to   = min( $to, $max );

                    return range( $from, $to );
                } else {
                    $range1 = range( $from, $max );
                    $range2 = range( $min, $to );

                    return array_unique( array_merge( $range1, $range2 ) );
                }
            }
        }

        /**
         * check a date inclusion in a time range
         *
         * @param string    $range_type
         * @param string    $range_from
         * @param string    $range_to
         * @param int       $date_from
         * @param int       $date_to
         * @param bool|true $intersect
         * @return bool
         */
        public function check_date_inclusion_in_range( $range_type, $range_from, $range_to, $date_from, $date_to, $intersect = true ) {
            switch ( $range_type ) {
                case 'custom':
                    $range_from = strtotime( $range_from );
                    $range_to   = strtotime( $range_to . ' + 1 day' ) - 1;

                    if ( $intersect ) {
                        if ( $this->check_intersect_dates( $range_from, $range_to, $date_from, $date_to ) ) {
                            return true;
                        }
                    } else {
                        if ( $range_from <= $date_from && $range_to >= $date_to ) {
                            return true;
                        }
                    }
                    break;
                case 'month':
                    $range_from = absint( $range_from );
                    $range_to   = absint( $range_to );

                    $date_from = strtotime( date( 'Y-m-01', $date_from ) );
                    $date_to   = strtotime( date( 'Y-m-01', $date_to ) );

                    if ( $this->get_time_diff( $date_from, $date_to, 'y' ) > 0 ) {
                        $months_request_range = range( 1, 12 );
                    } else {
                        $request_month_from   = date( 'm', $date_from );
                        $request_month_to     = date( 'm', $date_to );
                        $months_request_range = $this->create_numeric_range( $request_month_from, $request_month_to, 12, 1 );
                    }

                    $months_bookable_range = $this->create_numeric_range( $range_from, $range_to, 12, 1 );
                    $months_intersect      = array_intersect( $months_request_range, $months_bookable_range );

                    $is_included  = count( $months_intersect ) == count( $months_request_range );
                    $is_intersect = count( $months_intersect ) > 0;

                    return $intersect ? $is_intersect : $is_included;

                    break;
                case 'week':
                    // there are 53 weeks in one year
                    $range_from = absint( $range_from );
                    $range_to   = absint( $range_to );

                    $date_from = $this->get_day_on_this_week( $date_from, 1 );
                    $date_to   = $this->get_day_on_this_week( $date_to, 1 );

                    if ( $this->get_time_diff( $date_from, $date_to, 'y' ) > 0 ) {
                        $weeks_request_range = range( 1, 53 );
                    } else {
                        $request_week_from   = date( 'W', $date_from );
                        $request_week_to     = date( 'W', $date_to );
                        $weeks_request_range = $this->create_numeric_range( $request_week_from, $request_week_to, 53, 1 );
                    }

                    $weeks_bookable_range = $this->create_numeric_range( $range_from, $range_to, 53, 1 );
                    $weeks_intersect      = array_intersect( $weeks_request_range, $weeks_bookable_range );

                    $is_included  = count( $weeks_intersect ) == count( $weeks_request_range );
                    $is_intersect = count( $weeks_intersect ) > 0;

                    return $intersect ? $is_intersect : $is_included;

                    break;
                case 'day':
                    $range_from = absint( $range_from );
                    $range_to   = absint( $range_to );

                    $date_from = strtotime( 'midnight', $date_from );
                    $date_to   = strtotime( 'midnight', $date_to );

                    if ( $this->get_time_diff( $date_from, $date_to, 'day' ) > 6 ) {
                        $days_request_range = range( 1, 7 );
                    } else {
                        $request_day_from   = date( 'N', $date_from );
                        $request_day_to     = date( 'N', $date_to );
                        $days_request_range = $this->create_numeric_range( $request_day_from, $request_day_to, 7, 1 );
                    }

                    $days_bookable_range = $this->create_numeric_range( $range_from, $range_to, 7, 1 );
                    $days_intersect      = array_intersect( $days_request_range, $days_bookable_range );

                    $is_included  = count( $days_intersect ) == count( $days_request_range );
                    $is_intersect = count( $days_intersect ) > 0;

                    return $intersect ? $is_intersect : $is_included;

                    break;
                case 'time':
                    if ( '00:00' === $range_to ) {
                        $range_to = "24:00";
                    }
                    $range_from = strtotime( $range_from, $date_from );
                    $range_to   = strtotime( $range_to, $date_to ) - 1;

                    if ( $range_to < $range_from && ( $range_to + DAY_IN_SECONDS ) > $range_from ) {
                        // Example from 17:00 to 08:00
                        $range_to_tomorrow    = $range_to + DAY_IN_SECONDS;
                        $range_from_yesterday = $range_from - DAY_IN_SECONDS;

                        if ( $intersect ) {
                            return $this->check_intersect_dates( $range_from, $range_to_tomorrow, $date_from, $date_to ) ||
                                   $this->check_intersect_dates( $range_from_yesterday, $range_to, $date_from, $date_to );
                        } else {
                            return $range_from <= $date_from && $range_to_tomorrow >= $date_to ||
                                   $range_from_yesterday <= $date_from && $range_to >= $date_to;
                        }
                    } else {
                        if ( $intersect ) {
                            return $this->check_intersect_dates( $range_from, $range_to, $date_from, $date_to );
                        } else {
                            return $range_from <= $date_from && $range_to >= $date_to;
                        }
                    }
                    break;
            }

            return false;
        }

    }
}

/**
 * Unique access to instance of YITH_WCBK_Date_Helper class
 *
 * @return YITH_WCBK_Date_Helper
 */
function YITH_WCBK_Date_Helper() {
    return YITH_WCBK_Date_Helper::get_instance();
}

if ( !function_exists( 'yith_wcbk_get_time_sum' ) ) {
    function yith_wcbk_get_time_sum( $time, $number = 0, $unit = 'day', $midnight = false ) {
        return YITH_WCBK_Date_Helper()->get_time_sum( $time, $number, $unit, $midnight );
    }
}

if ( !function_exists( 'yith_wcbk_get_time_diff' ) ) {
    function yith_wcbk_get_time_diff( $timestamp1, $timestamp2, $unit = '' ) {
        return YITH_WCBK_Date_Helper()->get_time_diff( $timestamp1, $timestamp2, $unit );
    }
}