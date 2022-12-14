<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Service' ) ) {
    /**
     * Class YITH_WCBK_Service
     * the Service
     *
     * @property    string $name
     * @property    string $slug
     * @property    string $description
     * @property    string $price
     * @property    string $optional
     * @property    string $hidden
     * @property    string $hidden_in_search_forms
     * @property    string $multiply_per_blocks
     * @property    string $multiply_per_persons
     * @property    array  $price_for_person_types
     * @property    string $quantity_enabled
     * @property    string $min_quantity
     * @property    string $max_quantity
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Service {

        /** @var int the id of the term */
        public $id;

        /** @var WP_Term */
        public $term;

        /** @var string the service taxonomy name */
        public $taxonomy_name;

        /**
         * YITH_WCBK_Service constructor.
         *
         * @param int     $term_id
         * @param WP_Term $term
         */
        public function __construct( $term_id, $term = null ) {
            $this->taxonomy_name = YITH_WCBK_Post_Types::$service_tax;
            $this->id            = absint( $term_id );

            $this->_populate( $term );
        }


        /**
         * __get function.
         *
         * @param string $key
         * @return mixed
         */
        public function __get( $key ) {
            $value = apply_filters( 'yith_wcbk_booking_service_get', null, $key, $this );
            if ( is_null( $value ) )
                $value = get_term_meta( $this->id, $key, true );

            if ( !empty( $value ) ) {
                $this->$key = $value;
            }

            return $value;
        }

        /**
         * __isset function.
         *
         * @param string $key
         * @return mixed
         */
        public function __isset( $key ) {
            return metadata_exists( 'term', $this->id, $key );
        }

        /**
         * __set function.
         *
         * @param string $property
         * @param mixed  $value
         * @return bool|int
         */
        public function set( $property, $value ) {
            if ( 'price' === $property && $value ) {
                $value = wc_format_decimal( $value );
            }

            if ( 'price_for_person_types' === $property && is_array( $value ) ) {
                foreach ( $value as $k => $v ) {
                    $value[ $k ] = wc_format_decimal( $v );
                }
            }

            $this->$property = $value;

            return update_term_meta( $this->id, $property, wc_clean( $value ) );
        }

        /**
         * Get data of the service
         *
         * @param WP_Term $term
         */
        private function _populate( $term = null ) {
            if ( empty( $term ) ) {
                $this->term = get_term( $this->id, $this->taxonomy_name );
            } else {
                $this->term = $term;
            }
            if ( $this->is_valid() ) {
                $this->name        = $this->term->name;
                $this->description = $this->term->description;
                $this->slug        = $this->term->slug;

                foreach ( $this->get_meta() as $key => $value ) {
                    $this->$key = $value;
                }

                do_action( 'yith_wcbk_booking_service_loaded', $this );
            }
        }

        /**
         * check if the service is valid
         *
         * @return bool
         */
        public function is_valid() {
            return !empty( $this->term ) && !empty( $this->id ) && $this->term->term_id == $this->id;
        }


        /**
         * check if the service is hidden
         *
         * @return bool
         */
        public function is_hidden() {
            return 'yes' === $this->hidden;
        }

        /**
         * check if the service is hidden in search forms
         *
         * @return bool
         */
        public function is_hidden_in_search_forms() {
            return 'yes' === $this->hidden_in_search_forms || $this->is_hidden();
        }

        /**
         * check if the service has multiply per blocks enabled
         *
         * @return bool
         */
        public function is_multiply_per_blocks() {
            return 'yes' === $this->multiply_per_blocks;
        }

        /**
         * check if the service has multiply per persons enabled
         *
         * @return bool
         */
        public function is_multiply_per_persons() {
            return 'yes' === $this->multiply_per_persons;
        }

        /**
         * check if the service is optional
         *
         * @return bool
         */
        public function is_optional() {
            return 'yes' === $this->optional;
        }

        /**
         * check if the service has quantity enabled
         *
         * @since 2.0.5
         * @return bool
         */
        public function is_quantity_enabled() {
            return 'yes' === $this->quantity_enabled;
        }

        /**
         * get the min quantity
         *
         * @return int
         */
        public function get_min_quantity() {
            return max( 0, absint( $this->min_quantity ) );
        }

        /**
         * get the max quantity
         *
         * @return int
         */
        public function get_max_quantity() {
            return absint( $this->max_quantity );
        }


        /**
         * get the price of the current service
         *
         * @param int $person_type
         * @return string
         */
        public function get_price_for_person_type( $person_type ) {
            $price = '';
            if ( $person_type ) {
                $price_for_person_types = $this->price_for_person_types;
                if ( isset( $price_for_person_types[ $person_type ] ) ) {
                    $price = $price_for_person_types[ $person_type ];
                }
            }

            return $price;
        }

        /**
         * get the price of the current service
         *
         * @param int $person_type
         * @return string
         */
        public function get_price( $person_type = 0 ) {
            $price = $this->price;
            if ( $person_type ) {
                $price_for_person_type = $this->get_price_for_person_type( $person_type );
                if ( $price_for_person_type != '' ) {
                    $price = $price_for_person_type;
                }
            }

            return apply_filters( 'yith_wcbk_service_price', floatval( $price ) );
        }

        /**
         * return the name of the service
         *
         * @return string
         */
        public function get_name() {
            return apply_filters( 'yith_wcbk_get_service_name', $this->name, $this );
        }

        /**
         * get the service name including quantity
         *
         * @param bool|int $quantity
         * @since 2.0.5
         * @return string
         */
        public function get_name_with_quantity( $quantity = false ) {
            if ( $this->is_quantity_enabled() && $quantity !== false ) {
                $name = sprintf( '%s (x %s)', $this->get_name(), $quantity );
            } else {
                $name = $this->get_name();
            }
            return apply_filters( 'yith_wcbk_get_name_with_quantity', $name, $this );
        }

        /**
         * get the price HTML of the current service
         *
         * @param int $person_type
         * @return string
         */
        public function get_price_html( $person_type = 0 ) {
            return wc_price( $this->get_price( $person_type ) );
        }

        /**
         * Fill the default metadata with the post meta stored in db
         *
         * @return array
         */
        public function get_meta() {
            $meta = array();
            foreach ( self::get_default_meta_data() as $key => $value ) {
                $meta[ $key ] = $this->$key;
            }

            return $meta;
        }

        /**
         * Return an array of all custom fields
         *
         * @return array
         */
        public static function get_default_meta_data() {
            return array(
                'price'                  => '',
                'optional'               => 'no',
                'hidden'                 => 'no',
                'hidden_in_search_forms' => 'no',
                'multiply_per_blocks'    => 'no',
                'multiply_per_persons'   => 'no',
                'quantity_enabled'       => 'no',
                'min_quantity'           => '',
                'max_quantity'           => '',
                'price_for_person_types' => array(),
            );
        }

        /**
         * get the pricing for the services
         *
         * @param WC_Product_Booking $product
         * @return string
         */
        public function get_pricing_html( $product ) {
            $html = '';
            if ( $this->is_multiply_per_persons() && $product->has_people_types_enabled() ) {
                foreach ( $product->get_enabled_people_types() as $person_type ) {
                    /**
                     * @var bool $enabled
                     * @var int  $id
                     * @var int  $min
                     * @var int  $max
                     * @var int  $base_cost
                     * @var int  $block_cost
                     */
                    extract( $person_type );

                    $person_type_id = absint( $person_type[ 'id' ] );
                    $price          = apply_filters( 'yith_wcbk_booking_service_get_pricing_html_price', $this->get_price( $person_type_id ), $this, $product );
                    if ( !$price ) {
                        $price = apply_filters( 'yith_wcbk_service_free_text', __( 'Free', 'yith-booking-for-woocommerce' ) );
                    } else {
                        $price = strip_tags( wc_price( $price ) );
                        if ( $this->is_multiply_per_blocks() ) {
                            $price .= ' / ' . $product->get_block_duration_html();
                        }
                    }

                    $html .= YITH_WCBK()->person_type_helper->get_person_type_title( $person_type_id ) . ': ' . $price . '<br />';
                }
            } else {
                $price = apply_filters( 'yith_wcbk_booking_service_get_pricing_html_price', $this->get_price(), $this, $product );
                if ( !$price ) {
                    $price = apply_filters( 'yith_wcbk_service_free_text', __( 'Free', 'yith-booking-for-woocommerce' ) );
                } else {
                    $price = strip_tags( wc_price( $price ) );
                    if ( $this->is_multiply_per_blocks() ) {
                        $price .= ' / ' . $product->get_block_duration_html();
                    }
                }
                $html .= $price;
            }

            return $html;
        }


        /**
         * get the service description
         *
         * @since 2.0.0
         * @return string
         */
        public function get_description() {
            return apply_filters( 'yith_wcbk_booking_service_get_description', wp_kses_post( $this->description ), $this );
        }


        /**
         * get information to show in help_tip
         *
         * @param WC_Product_Booking $product
         * @since 2.0.0
         * @return string
         */
        public function get_info( $product ) {
            $info = '';

            if ( YITH_WCBK()->settings->get( 'show-service-descriptions', 'no' ) === 'yes' ) {
                if ( $description = $this->get_description() ) {
                    $info .= "<div class='yith-wcbk-booking-service__description'>{$description}</div>";
                }
            }

            if ( YITH_WCBK()->settings->get( 'show-service-prices', 'no' ) === 'yes' ) {
                $pricing = $this->get_pricing_html( $product );
                $info    .= "<div class='yith-wcbk-booking-service__pricing'>{$pricing}</div>";
            }

            return apply_filters( 'yith_wcbk_booking_service_get_info', $info, $this, $product );
        }

		/**
		 * Return a valid quantity
		 * @param $qty
		 *
		 * @return int
		 */
		public function validate_quantity( $qty ) {
			$qty = absint( $qty );
			$qty = max( $qty, $this->get_min_quantity() );
			if ( $this->get_max_quantity() ) {
				$qty = min( $qty, $this->get_max_quantity() );
			}

			return $qty;
		}
    }
}

if ( !function_exists( 'yith_get_booking_service' ) ) {
    /**
     * get the booking service
     *
     * @param int|YITH_WCBK_Service $service
     * @param WP_Term               $term
     * @return YITH_WCBK_Service
     */
    function yith_get_booking_service( $service, $term = null ) {

        if ( $service instanceof YITH_WCBK_Service ) {
            $_service = $service;
        } else {
            $_service = new YITH_WCBK_Service( $service, $term );
        }
        return $_service;
    }
}