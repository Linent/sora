<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Simple_Object' ) ) {
    /**
     * Class YITH_WCBK_Simple_Object
     *
     * @version 2.1.0
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    abstract class YITH_WCBK_Simple_Object {

        /** @var array */
        protected $data = array();

        protected $object_type = 'simple_object';

        /**
         * YITH_WCBK_Simple_Object constructor.
         *
         * @param array|object $args
         */
        public function __construct( $args = array() ) {
            if ( is_object( $args ) ) {
                $args = get_object_vars( $args );
            }

            /** @since 2.1.13 */
			$this->data = apply_filters( 'yith_wcbk_' . $this->object_type . '_object_default_data', $this->data, $this );

            $this->populate_props( $args );
        }

        /**
         * Prefix for action and filter hooks on data.
         *
         * @return string
         */
        protected function get_hook_prefix() {
            return 'yith_wcbk_' . $this->object_type . '_get_';
        }

        /**
         * Prefix for action and filter hooks on data.
         *
         * @return string
         */
        protected function get_hook() {
            return 'yith_wcbk_' . $this->object_type . '_get';
        }

        /**
         * get object properties
         *
         * @param string $prop
         * @param string $context What the value is for. Valid values are view and edit.
         * @return mixed
         */
        protected function get_prop( $prop, $context = 'view' ) {
            $value = null;

            if ( array_key_exists( $prop, $this->data ) ) {
                $value = $this->data[ $prop ];

                if ( 'view' === $context ) {
                    $value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
                    $value = apply_filters( $this->get_hook(), $value, $prop, $this );
                }
            }

            return $value;
        }

        protected function populate_props( $args = array() ) {
            !is_array( $args ) && ( $args = array() );

            foreach ( $args as $prop => $value ) {
                $setter = "set_{$prop}";
                if ( is_callable( array( $this, $setter ) ) ) {
                    $this->{$setter}( $value );
                } else {
                    $this->set_prop( $prop, $value );
                }
            }
        }

        /**
         * set object properties
         *
         * @param string $prop
         * @param mixed  $value the value
         */
        public function set_prop( $prop, $value ) {
            if ( array_key_exists( $prop, $this->data ) ) {
                $this->data[ $prop ] = $value;
            }
        }

        /**
         * return an array of data
         *
         * @return array
         */
        public function to_array() {
            return $this->data;
        }


    }
}