<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Price_Rule' ) ) {
    /**
     * Class YITH_WCBK_Price_Rule
     *
     * @since   2.1.0
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Price_Rule extends YITH_WCBK_Simple_Object {

		protected $object_type = 'price_rule';

		/** @var array */
        protected $data = array(
            'name'                => '',
            'enabled'             => 'yes',
            'conditions'          => array(
                array(
                    'type' => 'custom',
                    'from' => '',
                    'to'   => '',
                )
            ),
            'base_price_operator' => 'add',
            'base_price'          => 0,
            'base_fee_operator'   => 'add',
            'base_fee'            => 0
        );

        /**
         * YITH_WCBK_Price_Rule constructor.
         *
         * @param array|object $args
         */
        public function __construct( $args = array() ) {
            if ( is_object( $args ) ) {
                $args = get_object_vars( $args );
            }

            $args = $this->_map_args( $args );

            parent::__construct( $args );
        }

        /**
         * map arguments for backward compatibility
         * if the rule was created before 2.1 version
         *
         * @param $args
         * @return mixed
         */
        protected function _map_args( $args ) {
            if ( !isset( $args[ 'conditions' ] ) && isset( $args[ 'type' ] ) && isset( $args[ 'from' ] ) && isset( $args[ 'to' ] ) ) {
                $args[ 'conditions' ] = array( array( 'type' => $args[ 'type' ], 'from' => $args[ 'from' ], 'to' => $args[ 'to' ] ) );

                unset( $args[ 'type' ] );
                unset( $args[ 'from' ] );
                unset( $args[ 'to' ] );
            }

            $key_mapper = array(
                'base_cost'           => 'base_fee',
                'base_cost_operator'  => 'base_fee_operator',
                'block_cost'          => 'base_price',
                'block_cost_operator' => 'base_price_operator',
            );

            foreach ( $key_mapper as $old => $new ) {
                if ( !isset( $args[ $new ] ) && isset( $args[ $old ] ) ) {
                    $args[ $new ] = $args[ $old ];
                }

                if ( isset( $args[ $old ] ) ) {
                    unset( $args[ $old ] );
                }
            }

            return $args;
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
         * get the conditions
         *
         * @param string $context
         * @return string
         */
        public function get_conditions( $context = 'view' ) {
            return $this->get_prop( 'conditions', $context );
        }

        /**
         * get the base price operator
         *
         * @param string $context
         * @return string
         */
        public function get_base_price_operator( $context = 'view' ) {
            return $this->get_prop( 'base_price_operator', $context );
        }

        /**
         * get the base price
         *
         * @param string $context
         * @return float
         */
        public function get_base_price( $context = 'view' ) {
            return $this->get_prop( 'base_price', $context );
        }


        /**
         * get the base fee operator
         *
         * @param string $context
         * @return string
         */
        public function get_base_fee_operator( $context = 'view' ) {
            return $this->get_prop( 'base_fee_operator', $context );
        }

        /**
         * get the base fee
         *
         * @param string $context
         * @return float
         */
        public function get_base_fee( $context = 'view' ) {
            return $this->get_prop( 'base_fee', $context );
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
         * set conditions
         *
         * @param array $conditions
         */
        public function set_conditions( $conditions ) {
            $this->set_prop( 'conditions', (array) $conditions );
        }

        /**
         * set base price operator
         *
         * @param string $operator
         */
        public function set_base_price_operator( $operator ) {
            $this->set_prop( 'base_price_operator', $operator );
        }

        /**
         * set base price
         *
         * @param string $price
         */
        public function set_base_price( $price ) {
            $this->set_prop( 'base_price', wc_format_decimal( $price ) );
        }

        /**
         * set base fee operator
         *
         * @param string $operator
         */
        public function set_base_fee_operator( $operator ) {
            $this->set_prop( 'base_fee_operator', $operator );
        }

        /**
         * set base fee
         *
         * @param string $price
         */
        public function set_base_fee( $price ) {
            $this->set_prop( 'base_fee', wc_format_decimal( $price ) );
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
    }
}

if ( !function_exists( 'yith_wcbk_price_rule' ) ) {
    function yith_wcbk_price_rule( $args ) {
        return $args instanceof YITH_WCBK_Price_Rule ? $args : new YITH_WCBK_Price_Rule( $args );
    }
}