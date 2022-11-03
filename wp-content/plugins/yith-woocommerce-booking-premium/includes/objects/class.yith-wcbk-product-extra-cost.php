<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Product_Extra_Cost' ) ) {
    /**
     * Class YITH_WCBK_Product_Extra_Cost
     *
     * @version 2.1.0
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Product_Extra_Cost extends YITH_WCBK_Simple_Object {

        /** @var array */
        protected $data = array(
            'id'                           => 0,
            'name'                         => '',
            'cost'                         => '',
            'multiply_by_number_of_people' => false,
            'multiply_by_duration'         => false,
        );

        protected $object_type = 'product_extra_cost';

        /**
         * return the ID
         *
         * @param string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_id( $context = 'view' ) {
            return $this->get_prop( 'id', $context );
        }

        /**
         * @return string|int
         */
        public function get_identifier() {
            return $this->get_id();
        }

        /**
         * @return string
         */
        public function get_slug() {
            return get_post_field( 'post_name', $this->get_id() );
        }

        /**
         * return the name of the Extra Cost
         *
         * @param string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_name( $context = 'view' ) {
            $name = get_the_title( $this->get_id() );
            return 'view' === $context ? apply_filters( $this->get_hook_prefix() . 'name', $name, $this->get_id() ) : $name;
        }

        /**
         * return the cost
         *
         * @param string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_cost( $context = 'view' ) {
            return $this->get_prop( 'cost', $context );
        }

        /**
         * return multiply by number of people
         *
         * @param string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function get_multiply_by_number_of_people( $context = 'view' ) {
            return $this->get_prop( 'multiply_by_number_of_people', $context );
        }

        /**
         * return multiply by duration
         *
         * @param string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function get_multiply_by_duration( $context = 'view' ) {
            return $this->get_prop( 'multiply_by_duration', $context );
        }

        /**
         * set ID
         *
         * @param int $id The ID of the Extra Cost
         */
        public function set_id( $id ) {
            $this->set_prop( 'id', absint( $id ) );
        }

        /**
         * set the cost
         *
         * @param string $cost The cost of the Extra Cost
         */
        public function set_cost( $cost ) {
            $this->set_prop( 'cost', wc_format_decimal( $cost ) );
        }

        /**
         * set multiply by number of people
         *
         * @param string $enabled is enabled?
         */
        public function set_multiply_by_number_of_people( $enabled ) {
            $this->set_prop( 'multiply_by_number_of_people', wc_string_to_bool( $enabled ) );
        }

        /**
         * set multiply by duration
         *
         * @param string $enabled is enabled?
         */
        public function set_multiply_by_duration( $enabled ) {
            $this->set_prop( 'multiply_by_duration', wc_string_to_bool( $enabled ) );
        }

        /**
         * set name
         *
         * @param string $name the name of the extra cost
         */
        public function set_name( $name ) {
            $this->set_prop( 'name', $name );
        }


        /**
         * has multiply by number of people enabled?
         *
         * @return bool
         */
        public function has_multiply_by_number_of_people_enabled() {
            return $this->get_multiply_by_number_of_people();
        }

        /**
         * has multiply by duration enabled?
         *
         * @return bool
         */
        public function has_multiply_by_duration_enabled() {
            return $this->get_multiply_by_duration();
        }

        /**
         * is valid?
         *
         * @return bool
         */
        public function is_valid() {
            return ( $this->is_custom() && $this->get_name() ) || ( 'publish' === get_post_status( $this->get_id() ) && $this->get_cost() );
        }

        /**
         * is custom?
         *
         * @return bool
         */
        public function is_custom() {
            return !$this->get_id();
        }

        /**
         * calculate the total cost
         *
         * @param int $duration
         * @param int $people
         * @return float
         */
        public function calculate_cost( $duration, $people ) {
            $cost = (float) $this->get_cost();
            if ( $this->has_multiply_by_duration_enabled() ) {
                $cost = $cost * $duration;
            }

            if ( $this->has_multiply_by_number_of_people_enabled() ) {
                $cost = $cost * $people;
            }
            return $cost;
        }

    }
}

if ( !function_exists( 'yith_wcbk_product_extra_cost' ) ) {
    function yith_wcbk_product_extra_cost( $args ) {
        // todo: improve with Factory
        $extra_cost = $args instanceof YITH_WCBK_Product_Extra_Cost ? $args : new YITH_WCBK_Product_Extra_Cost( $args );

        if ( $extra_cost->is_custom() && !$extra_cost instanceof YITH_WCBK_Product_Extra_Cost_Custom ) {
            $extra_cost = new YITH_WCBK_Product_Extra_Cost_Custom( $extra_cost->to_array() );
        }

        return $extra_cost;
    }
}