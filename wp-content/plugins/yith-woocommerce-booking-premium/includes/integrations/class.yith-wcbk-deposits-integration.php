<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Deposits_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Deposits_Integration extends YITH_WCBK_Integration {
    /** @var YITH_WCBK_Deposits_Integration */
    protected static $_instance;

    /**
     * Constructor
     *
     * @param bool $plugin_active
     * @param bool $integration_active
     * @access protected
     */
    protected function __construct( $plugin_active, $integration_active ) {
        parent::__construct( $plugin_active, $integration_active );

        if ( $this->is_active() ) {
            add_filter( 'yith_wcdp_is_deposit_enabled_on_product', array( $this, 'disable_deposit_on_bookings_requiring_confirmation' ), 10, 2 );
            add_action( 'yith_wcdp_booking_add_to_cart', array( $this, 'add_deposit_to_booking' ) );
            add_filter( 'yith_wcbk_product_form_get_booking_data', array( $this, 'add_deposit_price_to_booking_data' ), 10, 2 );

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            add_action( 'woocommerce_order_status_cancelled', array( $this, 'set_booking_as_cancelled_when_balance_is_cancelled' ), 10, 2 );
        }
    }

    /**
     * Disable deposits on booking products that requires confirmation
     *
     * @param bool $enabled
     * @param int  $product_id
     * @return bool
     * @since 2.1
     */
    public function disable_deposit_on_bookings_requiring_confirmation( $enabled, $product_id ) {
        /** @var WC_Product_Booking $product */
        $product = wc_get_product( $product_id );
        if ( $product && yith_wcbk_is_booking_product( $product ) && $product->is_confirmation_required() ) {
            $enabled = false;
        }
        return $enabled;
    }

    /**
     * @param array              $booking_data
     * @param WC_Product_Booking $product
     * @return array
     */
    public function add_deposit_price_to_booking_data( $booking_data, $product ) {
        $price              = $product->calculate_price( $_POST );
        $deposit_price      = YITH_WCDP_Premium()->get_deposit( $product->get_id(), $price );
        $deposit_price_html = wc_price( $deposit_price );

        $booking_data[ 'deposit_price' ] = $deposit_price_html;

        return $booking_data;
    }

    /**
     * Add Deposits to Booking Products
     *
     * @param WC_Product_Booking $product
     */
    public function add_deposit_to_booking( $product ) {
        if ( !$product->is_confirmation_required() ) {
            add_action( 'woocommerce_before_add_to_cart_button', array( YITH_WCDP_Frontend_Premium(), 'print_single_add_deposit_to_cart_template' ) );
        }
    }

    /**
     * Returns post parent of a Balance order
     * If order is not a balance order, it will return false
     *
     * @param $order_id int|WC_Order Order id
     * @return int|bool If order is a balance order, and has post parent, returns parent ID; false otherwise
     */
    public function get_parent_order_id( $order_id ) {
        $order = wc_get_order( $order_id );

        return $order && $order->get_meta( '_has_full_payment' ) ? $order->get_parent_id() : false;

    }

    /**
     * Set Booking as cancelled when the balance is cancelled
     *
     * @param int      $order_id
     * @param WC_Order $order
     * @since 2.1.4
     */
    public function set_booking_as_cancelled_when_balance_is_cancelled( $order_id, $order ) {
        $parent_order_id = $this->get_parent_order_id( $order_id );
        $bookings        = $parent_order_id ? YITH_WCBK_Booking_Helper()->get_bookings_by_order( $parent_order_id ) : false;
        if ( !!$bookings ) {
            $order_number = $order ? $order->get_order_number() : $order_id;
            foreach ( $bookings as $booking ) {
                if ( $booking instanceof YITH_WCBK_Booking ) {
                    $additional_note = sprintf( __( 'Reason: balance order <a href="%s">#%s</a> has been cancelled.', 'yith-booking-for-woocommerce' ),
                                                admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
                                                $order_number );
                    $booking->update_status( 'cancelled', $additional_note );
                }
            }
        }
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        wp_register_script( 'yith-wcbk-integration-deposits-booking-form', YITH_WCBK_ASSETS_URL . '/js/integrations/deposits/deposits-booking-form' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

        wp_enqueue_script( 'yith-wcbk-integration-deposits-booking-form' );

    }

}