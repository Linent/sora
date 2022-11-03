<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Booking_Helper
 * helper class: retrieve bookings and booking info
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 */
class YITH_WCBK_Booking_Helper {

    /** @var YITH_WCBK_Booking_Helper */
    protected static $_instance;

    /** @var string the booking post type name */
    public $post_type_name;

    /**
     * Singleton implementation
     *
     * @return YITH_WCBK_Booking_Helper
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * YITH_WCBK_Booking_Helper constructor.
     */
    public function __construct() {
        $this->post_type_name = YITH_WCBK_Post_Types::$booking;
    }

    /**
     * Count the number of booked booking of this product
     * in a specific period
     *
     * @param array $args
     * @return int
     */
    public function count_booked_bookings_in_period( $args = array() ) {
        $defaults = array(
            'product_id'                => false,
            'from'                      => false,
            'to'                        => false,
            'include_externals'         => true,
            'count_persons_as_bookings' => false,
            'exclude_order_id'          => 0,
            'get_post_args'             => array(),
        );
        $args     = wp_parse_args( $args, $defaults );
        /**
         * @var int   $product_id
         * @var int   $from
         * @var int   $to
         * @var bool  $include_externals
         * @var bool  $count_persons_as_bookings
         * @var int   $exclude_order_id
         * @var array $get_post_args
         */
        extract( $args );

        $_get_post_args = array(
            'post_status' => yith_wcbk_get_booked_statuses(),
            'meta_query'  => array(
                'relation' => 'AND',
                array(
                    'key'   => '_product_id',
                    'value' => $product_id,
                ),
                array(
                    'key'     => '_from',
                    'value'   => $to,
                    'compare' => '<'
                ),
                array(
                    'key'     => '_to',
                    'value'   => $from,
                    'compare' => '>'
                ),
            ),
            'fields'      => 'ids'
        );
        if ( $exclude_order_id ) {
            $_get_post_args[ 'meta_query' ][] = array(
                'key'     => '_order_id',
                'value'   => $exclude_order_id,
                'compare' => '!='
            );
        }

        $_get_post_args = wp_parse_args( $get_post_args, $_get_post_args );
        $_get_post_args = apply_filters( 'yith_wck_booking_helper_count_booked_bookings_in_period_get_post_args', $_get_post_args, $args );
        $bookings       = $this->get_bookings( $_get_post_args );

        $count = 0;

        if ( $count_persons_as_bookings ) {
            /** @var YITH_WCBK_Booking[] $bookings */
            $bookings = array_filter( array_map( 'yith_get_booking', $bookings ) );
            foreach ( $bookings as $_booking ) {
                $count += $_booking->get_persons();
            }
        } else {
            $count = !!$bookings ? count( $bookings ) : 0;
        }


        if ( $include_externals ) {
            /** @var WC_Product_Booking $product */
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) && $product->has_external_calendars() ) {
                $product->maybe_load_externals();
                $count += YITH_WCBK_Booking_Externals()->count_externals_in_period( $from, $to, $product_id );
            }
        }

        return apply_filters( 'yith_wck_booking_helper_count_booked_bookings_in_period', $count, $args );
    }

    /**
     * return the max booked bookings per unit in one period
     *
     * @param array $args
     * @return int|mixed
     */
    public function count_max_booked_bookings_per_unit_in_period( $args = array() ) {
        $date_helper = YITH_WCBK_Date_Helper();
        $count       = 0;
        $defaults    = array(
            'product_id'                => false,
            'from'                      => false,
            'to'                        => false,
            'unit'                      => 'day',
            'include_externals'         => true,
            'count_persons_as_bookings' => false,
            'exclude_order_id'          => 0,
            'get_post_args'             => array(),
            'return'                    => 'max_by_unit'
        );
        $args        = wp_parse_args( $args, $defaults );
        // let's filter args
        $args        = apply_filters( 'yith_wcbk_count_booked_booking_in_period_args', $args );

        /**
         * @var int    $product_id
         * @var int    $from
         * @var int    $to
         * @var string $unit
         * @var bool   $include_externals
         * @var bool   $count_persons_as_bookings
         * @var int    $exclude_order_id
         * @var array  $get_post_args
         * @var string $return
         */
        extract( $args );

        /** @var WC_Product_Booking $product */
        if ( $product_id && $from && $to && in_array( $unit, array( 'month', 'day', 'hour', 'minute' ) ) ) {

            if ( 'max_by_unit' === $return ) {
                $counter        = array();
                $current_time   = $from;
                $unit_increment = 'minute' === $unit ? yith_wcbk_get_minimum_minute_increment() : 1;

                while ( $current_time < $to ) {
                    $current_to  = $date_helper->get_time_sum( $current_time, $unit_increment, $unit );
                    $_count_args = array(
                        'product_id'                => $product_id,
                        'from'                      => $current_time,
                        'to'                        => $current_to,
                        'include_externals'         => $include_externals,
                        'count_persons_as_bookings' => $count_persons_as_bookings,
                        'exclude_order_id'          => $exclude_order_id,
                        'get_post_args'             => $get_post_args,
                    );
                    $counter[]   = $this->count_booked_bookings_in_period( $_count_args );

                    $current_time = $current_to;
                }

                if ( $counter ) {
                    $count = max( $counter );
                }
            } else {
                // return the Total
                $count = $this->count_booked_bookings_in_period( $args );
            }
        }

        return $count;

    }

    /**
     * return count of bookings with specific status
     *
     * @param string|array $status
     * @return int
     */
    public function count_booking_with_status( $status ) {
        $counter = 0;
        if ( !is_array( $status ) ) {
            $status = array( $status );
        }
        $counts = (array) wp_count_posts( $this->post_type_name );
        foreach ( $status as $s ) {
            if ( yith_wcbk_is_a_booking_status( $s ) ) {
                $counter += isset( $counts[ 'bk-' . $s ] ) ? absint( $counts[ 'bk-' . $s ] ) : 0;
            }
        }

        return $counter;
    }

    /**
     * Get all bookings by arguments
     *
     * @param array  $args   argument for get_posts
     * @param string $return the object type returned
     * @return YITH_WCBK_Booking[]
     */
    public function get_bookings( $args = array(), $return = 'bookings' ) {
        $all_booking_statuses = array_keys( yith_wcbk_get_booking_statuses() );

        foreach ( $all_booking_statuses as $key => $value ) {
            $all_booking_statuses[ $key ] = 'bk-' . $value;
        }

        $default_args = array(
            'post_type'      => $this->post_type_name,
            'post_status'    => $all_booking_statuses,
            'posts_per_page' => -1,
        );
        $args         = wp_parse_args( $args, $default_args );

        if ( $return !== 'posts' ) {
            $args[ 'fields' ] = 'ids';
        }

        $bookings = get_posts( $args );

        switch ( $return ) {
            case 'posts':
            case 'ids':
                return !!$bookings ? $bookings : array();
                break;
            case 'bookings':
            default:
                return array_filter( array_map( 'yith_get_booking', $bookings ) );
        }
    }

    /**
     * Get all bookings of a user
     *
     * @param int    $user_id the id of the user
     * @param string $return
     * @return YITH_WCBK_Booking[]
     */
    public function get_bookings_by_user( $user_id, $return = 'bookings' ) {
        if ( !$user_id ) {
            return array();
        }
        $args = array(
            'meta_key'   => '_user_id',
            'meta_value' => $user_id,
        );

        return $this->get_bookings( $args, $return );
    }

    /**
     * Get all bookings of a booking product
     *
     * @param int    $product_id the id of the product
     * @param string $return
     * @return YITH_WCBK_Booking[]
     */
    public function get_bookings_by_product( $product_id, $return = 'bookings' ) {
        if ( !$product_id ) {
            return array();
        }

        $args = array(
            'meta_key'   => '_product_id',
            'meta_value' => $product_id,
        );

        return $this->get_bookings( $args, $return );
    }

    /**
     * Get all future bookings of a booking product
     *
     * @param int    $product_id the id of the product
     * @param string $return
     * @return YITH_WCBK_Booking[]
     * @since  2.0.0
     */
    public function get_future_bookings_by_product( $product_id, $return = 'bookings' ) {
        if ( !$product_id ) {
            return array();
        }

        $today = strtotime( 'now midnight' );
        $args  = apply_filters( 'yith_wcbk_get_future_bookings_by_product_args', array(
            'post_status' => yith_wcbk_get_booked_statuses(),
            'meta_query'  => array(
                'relation' => 'AND',
                array(
                    'key'   => '_product_id',
                    'value' => absint( $product_id ),
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_from',
                        'value'   => $today,
                        'compare' => '>='
                    ),
                    array(
                        'key'     => '_to',
                        'value'   => $today,
                        'compare' => '>='
                    ),
                )
            )
        ), $product_id );

        return $this->get_bookings( $args, $return );
    }

    /**
     * Get all bookings by meta_query
     *
     * @param array  $meta_query the meta query
     * @param string $return     the object type returned
     * @return YITH_WCBK_Booking[]
     */
    public function get_bookings_by_meta( $meta_query, $return = 'bookings' ) {
        $args = array(
            'meta_query' => $meta_query,
        );

        return $this->get_bookings( $args, $return );
    }

    /**
     * @param int          $from
     * @param int          $to
     * @param array|string $duration_unit
     * @param bool         $include_externals
     * @param bool|int     $product_id
     * @return YITH_WCBK_Booking_Abstract[]
     */
    public function get_bookings_in_time_range( $from, $to, $duration_unit = 'all', $include_externals = true, $product_id = false ) {
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => '_from',
                'value'   => $to,
                'compare' => '<='
            ),
            array(
                'key'     => '_to',
                'value'   => $from,
                'compare' => '>'
            ),
        );

        if ( $product_id ) {
            $meta_query[] = array(
                'key'   => '_product_id',
                'value' => $product_id,
            );
        }

        if ( $duration_unit != 'all' ) {
            $duration_unit = is_array( $duration_unit ) ? $duration_unit : array( $duration_unit );
            $meta_query[]  = array(
                'key'     => '_duration_unit',
                'value'   => $duration_unit,
                'compare' => 'IN'
            );
        }

        $args = array(
            'meta_query' => $meta_query,
            'meta_key'   => '_from',
            'orderby'    => 'meta_value',
            'order'      => 'ASC'
        );

        $bookings = $this->get_bookings( $args, 'bookings' );

        if ( $include_externals ) {
            YITH_WCBK_Booking_Externals()->maybe_load_all_externals();
            $externals = YITH_WCBK_Booking_Externals()->get_externals_in_period( $from, $to, $product_id );

            $duration_unit = is_array( $duration_unit ) ? $duration_unit : array( $duration_unit );
            if ( in_array( 'hour', $duration_unit ) || in_array( 'minute', $duration_unit ) ) {
                $externals = array_filter( $externals, function ( $external ) {
                    /** @var YITH_WCBK_Booking_External $external */
                    return $external->has_time();
                } );
            }

            $bookings = array_merge( $bookings, $externals );
        }

        return $bookings;
    }

    /**
     * get bookings by order
     *
     * @param int        $order_id      the id of the order
     * @param int|string $order_item_id the id of the order item
     * @return YITH_WCBK_Booking[]
     */
    public function get_bookings_by_order( $order_id, $order_item_id = '' ) {
        if ( !$order_id )
            return array();

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'   => '_order_id',
                'value' => $order_id,
            )
        );

        if ( !empty( $order_item_id ) ) {
            $meta_query[] = array(
                'key'   => '_order_item_id',
                'value' => $order_item_id,
            );
        }

        return $this->get_bookings_by_meta( $meta_query );
    }

    /**
     * parse posts and return array of YITH_WCBK_Booking
     *
     * @param WP_Post[]|int[] $bookings the posts or ids
     * @deprecated since 2.0.0 use yith_get_booking to parse to booking objects
     * @return YITH_WCBK_Booking[]
     */
    public function parse_bookings_from_posts( $bookings ) {
        return array_filter( array_map( 'yith_get_booking', $bookings ) );
    }

}

/**
 * Unique access to instance of YITH_WCBK_Booking_Helper class
 *
 * @return YITH_WCBK_Booking_Helper
 */
function YITH_WCBK_Booking_Helper() {
    return YITH_WCBK_Booking_Helper::get_instance();
}
