<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Availability_Rule' ) ) {
    /**
     * Class YITH_WCBK_Availability_Rule
     *
     * @version 2.1.0
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Availability_Rule extends YITH_WCBK_Simple_Object {
        /** @var array */
        private $_allowed_types = array( 'month', 'custom' );

		protected $object_type = 'availability_rule';

        /** @var array */
        protected $data = array(
            'name'          => '',
            'enabled'       => 'yes',
            'type'          => 'month',
            'from'          => '',
            'to'            => '',
            'bookable'      => 'yes',
            'days_enabled'  => 'no',
            'days'          => array(
                '1' => 'yes',
                '2' => 'yes',
                '3' => 'yes',
                '4' => 'yes',
                '5' => 'yes',
                '6' => 'yes',
                '7' => 'yes'
            ),
            'times_enabled' => 'no',
            'day_time_from' => array(),
            'day_time_to'   => array()
        );

        /**
         * Magic Method __get
         * for backward compatibility
         *
         * @param $key
         * @return mixed|null
         */
        public function __get( $key ) {
            $getter = 'get_' . $key;
            $value  = is_callable( array( $this, $getter ) ) ? $this->$getter : $this->get_prop( $key );
            if ( $value !== null ) {
                $this->$key = $value;
            }

            return $value;
        }

        /*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        |
        | Methods for getting data from the object.
        */

        /**
         * get the name of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_name( $context = 'view' ) {
            return $this->get_prop( 'name', $context );
        }

        /**
         * get the enabled value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_enabled( $context = 'view' ) {
            return $this->get_prop( 'enabled', $context );
        }

        /**
         * get the type of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_type( $context = 'view' ) {
            return $this->get_prop( 'type', $context );
        }

        /**
         * get the from value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_from( $context = 'view' ) {
            return $this->get_prop( 'from', $context );
        }

        /**
         * get the to value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_to( $context = 'view' ) {
            return $this->get_prop( 'to', $context );
        }

        /**
         * get the bookable value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_bookable( $context = 'view' ) {
            return $this->get_prop( 'bookable', $context );
        }

        /**
         * get the days enabled value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_days_enabled( $context = 'view' ) {
            return $this->get_prop( 'days_enabled', $context );
        }

        /**
         * get the days value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_days( $context = 'view' ) {
            return $this->get_prop( 'days', $context );
        }

        /**
         * get the times_enabled value of the rule
         *
         * @param string $context
         * @return string
         */
        public function get_times_enabled( $context = 'view' ) {
            return $this->get_prop( 'times_enabled', $context );
        }

        /**
         * get the day_time_from value of the rule
         *
         * @param string $context
         * @return array
         */
        public function get_day_time_from( $context = 'view' ) {
            return $this->get_prop( 'day_time_from', $context );
        }

        /**
         * get the day_time_to value of the rule
         *
         * @param string $context
         * @return array
         */
        public function get_day_time_to( $context = 'view' ) {
            return $this->get_prop( 'day_time_to', $context );
        }

        /*
       |--------------------------------------------------------------------------
       | Setters
       |--------------------------------------------------------------------------
       |
       */

        /**
         * set name
         *
         * @param string $name The name of the rule
         */
        public function set_name( $name ) {
            $this->set_prop( 'name', $name );
        }

        /**
         * set enabled
         *
         * @param string|bool $enabled
         */
        public function set_enabled( $enabled ) {
            $this->set_prop( 'enabled', wc_bool_to_string( $enabled ) );
        }

        /**
         * set type
         *
         * @param string $type
         */
        public function set_type( $type ) {
            $type = in_array( $type, $this->_allowed_types ) ? $type : 'month';
            $this->set_prop( 'type', $type );
        }


        /**
         * set from
         *
         * @param string $from
         */
        public function set_from( $from ) {
            $this->set_prop( 'from', $from );
        }

        /**
         * set to
         *
         * @param string $to
         */
        public function set_to( $to ) {
            $this->set_prop( 'to', $to );
        }

        /**
         * set bookable
         *
         * @param string|bool $bookable
         */
        public function set_bookable( $bookable ) {
            $this->set_prop( 'bookable', wc_bool_to_string( $bookable ) );
        }

        /**
         * set days_enabled
         *
         * @param string|bool $days_enabled
         */
        public function set_days_enabled( $days_enabled ) {
            $this->set_prop( 'days_enabled', wc_bool_to_string( $days_enabled ) );
        }

        /**
         * set days
         *
         * @param array $days
         */
        public function set_days( $days ) {
            $this->set_prop( 'days', (array) $days );
        }


        /**
         * set times_enabled
         *
         * @param string|bool $times_enabled
         */
        public function set_times_enabled( $times_enabled ) {
            $this->set_prop( 'times_enabled', wc_bool_to_string( $times_enabled ) );
        }

        /**
         * set day_time_from
         *
         * @param array $day_time_from
         */
        public function set_day_time_from( $day_time_from ) {
            $this->set_prop( 'day_time_from', (array) $day_time_from );
        }

        /**
         * set day_time_to
         *
         * @param array $day_time_to
         */
        public function set_day_time_to( $day_time_to ) {
            $this->set_prop( 'day_time_to', (array) $day_time_to );
        }


        /*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        |
        */

        /**
         * is the rule enabled?
         *
         * @return bool
         */
        public function is_enabled() {
            return 'yes' === $this->get_enabled();
        }

        /**
         * is a valid rule?
         *
         * @return bool
         */
        public function is_valid() {
            return $this->get_from() && $this->get_to();
        }

        /**
         * return true if has days enabled
         *
         * @return bool
         */
        public function has_days_enabled() {
            return 'yes' === $this->get_days_enabled();
        }

        /**
         * return true if has days enabled
         *
         * @return bool
         */
        public function has_times_enabled() {
            return 'yes' === $this->get_times_enabled();
        }


        /*
        |--------------------------------------------------------------------------
        | Non-crud Getters
        |--------------------------------------------------------------------------
        |
        */

        /**
         * return day time from
         *
         * @param int $day_number
         * @return string
         */
        public function get_day_time_from_by_day( $day_number ) {
            $values = $this->get_day_time_from();
            return isset( $values[ $day_number ] ) ? $values[ $day_number ] : '00:00';
        }

        /**
         * return day time to
         *
         * @param int $day_number
         * @return string
         */

        public function get_day_time_to_by_day( $day_number ) {
            $values = $this->get_day_time_to();
            return isset( $values[ $day_number ] ) ? $values[ $day_number ] : '00:00';
        }
    }
}

if ( !function_exists( 'yith_wcbk_availability_range' ) ) {
    /**
     * @param $args
     * @return YITH_WCBK_Availability_Rule
     * @deprecated since 2.1 | use yith_wcbk_availability_rule instead
     */
    function yith_wcbk_availability_range( $args ) {
        return yith_wcbk_availability_rule( $args );
    }
}

if ( !function_exists( 'yith_wcbk_availability_rule' ) ) {
    function yith_wcbk_availability_rule( $args ) {
        return $args instanceof YITH_WCBK_Availability_Rule ? $args : new YITH_WCBK_Availability_Rule( $args );
    }
}