<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_Externals' ) ) {
    /**
     * Class YITH_WCBK_Booking_Externals
     *
     * handle externals in DB
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  2.0.0
     */
    class YITH_WCBK_Booking_Externals {

        /** @var YITH_WCBK_Booking_Externals */
        protected static $_instance;

        /** @var string */
        private $_table_name = '';

        /** @var bool */
        private $_all_externals_loaded = false;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Booking_Externals
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Booking_Externals constructor.
         */
        private function __construct() {
            global $wpdb;
            $this->_table_name = $wpdb->prefix . YITH_WCBK_DB::$external_bookings_table;
        }

        /**
         * add an external in DB
         *
         * @param YITH_WCBK_Booking_External $external
         * @param bool                       $future
         *
         * @return false|int
         */
        public function add_external( $external, $future = true ) {
            global $wpdb;
            $defaults = YITH_WCBK_Booking_External::get_defaults();

            if ( $future && $external->is_completed() ) {
                return 1;
            }

            $keys   = array_keys( $defaults );
            $keys   = array_diff( $keys, array( 'id', 'date' ) );
            $values = array();
            foreach ( $keys as $key ) {
                $getter   = 'get_' . $key;
                $values[] = $external->$getter();
            }

            $query = "INSERT INTO $this->_table_name ";
            $query .= "(`" . implode( "`, `", $keys ) . "`, `date`)";
            $query .= " VALUES ";
            $query .= "('" . implode( "', '", array_map( 'esc_sql', $values ) ) . "', CURRENT_TIMESTAMP() )";

            return $wpdb->query( $query );
        }

        /**
         * add externals in DB
         *
         * @param array $externals
         * @param bool  $future
         */
        public function add_externals( $externals, $future = true ) {
            foreach ( $externals as $external ) {
                $this->add_external( $external, $future );
            }
        }


        /**
         * return an SQL where query from array
         *
         * @param array $_where
         *
         * @return string
         */
        public static function get_sql_where( $_where = array() ) {
            global $wpdb;

            $where = '';
            if ( $_where ) {
                $relation       = isset( $_where[ 'relation' ] ) && in_array( $_where[ 'relation' ], array( 'AND', 'OR' ) ) ? $_where[ 'relation' ] : 'AND';
                $where_defaults = array( 'key' => '', 'value' => '', 'compare' => '=', 'type' => 'CHAR' );
                $where_types    = array( 'CHAR' => '%s', 'NUMBER' => '%d' );
                $where_list     = array();
                foreach ( $_where as $current_where ) {
                    if ( !is_array( $current_where ) || empty( $current_where[ 'key' ] ) )
                        continue;

                    $current_where = wp_parse_args( $current_where, $where_defaults );
                    /**
                     * @var string $key
                     * @var string $value
                     * @var string $compare
                     * @var string $type
                     */
                    extract( $current_where );
                    $type        = array_key_exists( $type, $where_types ) ? $type : 'CHAR';
                    $placeholder = $where_types[ $type ];

                    $where_list[] = $wpdb->prepare( "externals.{$key} {$compare} $placeholder", $value );
                }

                if ( $where_list ) {
                    $where = "WHERE " . implode( " {$relation} ", $where_list );
                }
            }

            return $where;
        }

        /**
         * retrieve an array of externals stored in externals table
         *
         * @param array $args
         *
         * @return YITH_WCBK_Booking_External[]
         */
        public function get_externals( $args = array() ) {
            global $wpdb;

            $query = "SELECT * FROM {$this->_table_name} as externals";

            if ( isset( $args[ 'where' ] ) ) {
                $query .= ' ' . self::get_sql_where( $args[ 'where' ] );
            }

            $results = $wpdb->get_results( $query );

            if ( $results && is_array( $results ) ) {
                $results = array_map( 'yith_wcbk_booking_external', $results );
            } else {
                $results = array();
            }

            return $results;
        }

        /**
         * count externals stored in externals table
         *
         * @param array $args
         *
         * @return int
         */
        public function count_externals( $args = array() ) {
            global $wpdb;

            $query = "SELECT COUNT(*) as count FROM {$this->_table_name} as externals";

            if ( isset( $args[ 'where' ] ) ) {
                $query .= ' ' . self::get_sql_where( $args[ 'where' ] );
            }

            $count = $wpdb->get_var( $query );

            return $count;
        }


        /**
         * get externals related to a specific product id
         *
         * @param int $product_id
         *
         * @return array|null|object
         */
        public function get_externals_from_product_id( $product_id ) {
            $results = array();
            if ( $product_id = absint( $product_id ) ) {
                $results = $this->get_externals( array(
                                                     array(
                                                         'key'   => 'product_id',
                                                         'value' => $product_id
                                                     )
                                                 ) );
            }

            return $results;
        }

        /**
         * count externals in period
         *
         * @param int $from
         * @param int $to
         * @param int $product_id
         *
         * @return int
         */
        public function count_externals_in_period( $from, $to, $product_id = 0 ) {
            $where = array(
                array(
                    'key'     => 'from',
                    'value'   => $to,
                    'compare' => '<'
                ),
                array(
                    'key'     => 'to',
                    'value'   => $from,
                    'compare' => '>'
                ),
            );

            if ( $product_id = absint( $product_id ) ) {
                $where[] = array(
                    'key'   => 'product_id',
                    'value' => $product_id
                );
            }

            return absint( $this->count_externals( array( 'where' => $where ) ) );
        }

        /**
         * get externals in period
         *
         * @param int $from
         * @param int $to
         * @param int $product_id
         *
         * @return array
         */
        public function get_externals_in_period( $from, $to, $product_id = 0 ) {
            $where = array(
                array(
                    'key'     => 'from',
                    'value'   => $to,
                    'compare' => '<'
                ),
                array(
                    'key'     => 'to',
                    'value'   => $from,
                    'compare' => '>'
                ),
            );

            if ( $product_id = absint( $product_id ) ) {
                $where[] = array(
                    'key'   => 'product_id',
                    'value' => $product_id
                );
            }

            return $this->get_externals( array( 'where' => $where ) );
        }

        /**
         * return product ids of products with externals to sync
         *
         * searches for products with externals expired
         *
         * @return WC_Product_Booking[]
         */
        public function get_products_with_externals_to_sync() {
            $expiring_time = get_option( 'yith-wcbk-external-calendars-sync-expiration', 6 * HOUR_IN_SECONDS );
            $now           = time();

            $args = array(
                'posts_per_page' => -1,
                'post_type'      => 'product',
                'fields'         => 'ids',
                'meta_query'     => array(
                    'relation' => 'AND',
                    array(
                        'key'     => '_yith_booking_external_calendars',
                        'value'   => '',
                        'compare' => '!='
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key'     => '_yith_booking_external_calendars_loaded',
                            'value'   => '',
                            'compare' => '='
                        ),
                        array(
                            'key'     => '_yith_booking_external_calendars_loaded',
                            'value'   => $now - $expiring_time,
                            'compare' => '<'
                        ),
                    )
                ),
                'tax_query'      => array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
                )
            );

            $ids = get_posts( $args );
            $ids = !!$ids ? $ids : array();

            return array_filter( array_map( 'wc_get_product', $ids ) );
        }

        /**
         * Load all externals and store values in DB table
         */
        public function maybe_load_all_externals() {
            if ( !$this->_all_externals_loaded ) {
                $products = $this->get_products_with_externals_to_sync();
                foreach ( $products as $product ) {
                    $product->maybe_load_externals();
                }

                $this->_all_externals_loaded = true;
            }
        }


        /**
         * @param int $product_id
         *
         * @return false|int
         */
        public function delete_externals_from_product_id( $product_id ) {
            global $wpdb;

            $product_id = absint( $product_id );

            return $wpdb->delete( $this->_table_name, array( 'product_id' => $product_id ), array( '%d' ) );
        }

        /**
         * return an array of sync expiration times
         *
         * @return array
         */
        public static function get_sync_expiration_times() {
            $options = array(
                30 * MINUTE_IN_SECONDS => __( '30 minutes', 'yith-booking-for-woocommerce' ),
                HOUR_IN_SECONDS        => __( '1 hour', 'yith-booking-for-woocommerce' ),
                2 * HOUR_IN_SECONDS    => __( '2 hours', 'yith-booking-for-woocommerce' ),
                6 * HOUR_IN_SECONDS    => __( '6 hours', 'yith-booking-for-woocommerce' ),
                12 * HOUR_IN_SECONDS   => __( '12 hours', 'yith-booking-for-woocommerce' ),
                DAY_IN_SECONDS         => __( '1 day', 'yith-booking-for-woocommerce' ),
                2 * DAY_IN_SECONDS     => __( '2 days', 'yith-booking-for-woocommerce' ),
                7 * DAY_IN_SECONDS     => __( '7 days', 'yith-booking-for-woocommerce' ),
                MONTH_IN_SECONDS       => __( '1 month', 'yith-booking-for-woocommerce' ),
            );

            return apply_filters( 'yith_wcbk_externals_get_sync_expiration_times', $options );
        }
    }
}

/**
 * Unique access to instance of YITH_WCBK_Booking_Externals class
 *
 * @return YITH_WCBK_Booking_Externals
 * @since 2.0.0
 */
function YITH_WCBK_Booking_Externals() {
    return YITH_WCBK_Booking_Externals::get_instance();
}