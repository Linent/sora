<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Product_Extra_Cost_Custom' ) ) {
    /**
     * Class YITH_WCBK_Product_Extra_Cost_Custom
     *
     * @version 2.1.9
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Product_Extra_Cost_Custom extends YITH_WCBK_Product_Extra_Cost {

        protected $object_type = 'product_extra_cost_custom';

        /**
         * @return string|int
         */
        public function get_identifier() {
            return '_' . $this->get_slug();
        }

        /**
         * @return string
         */
        public function get_slug() {
            return sanitize_title( $this->get_name() );
        }

        /**
         * return the name of the Extra Cost
         *
         * @param string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_name( $context = 'view' ) {
            $name = $this->get_prop( 'name', $context );
            return 'view' === $context ? call_user_func( '__', $name, 'yith-booking-for-woocommerce' ) : $name;
        }


        /**
         * is valid?
         *
         * @return bool
         */
        public function is_valid() {
            return $this->get_name();
        }

        /**
         * is custom?
         *
         * @return bool
         */
        public function is_custom() {
            return true;
        }
    }
}