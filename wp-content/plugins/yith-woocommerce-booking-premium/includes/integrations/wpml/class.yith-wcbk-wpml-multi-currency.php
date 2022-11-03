<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Multi_Currency
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   2.0.3
 */
class YITH_WCBK_Wpml_Multi_Currency {
    /** @var YITH_WCBK_Wpml_Multi_Currency */
    private static $_instance;

    /** @var YITH_WCBK_Wpml_Integration */
    public $wpml_integration;

    /**
     * Singleton implementation
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     * @return YITH_WCBK_Wpml_Multi_Currency
     */
    public static function get_instance( $wpml_integration ) {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new static( $wpml_integration );
    }

    /**
     * Constructor
     *
     * @access private
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     */
    private function __construct( $wpml_integration ) {
        $this->wpml_integration = $wpml_integration;

        add_filter( 'yith_wcbk_booking_product_get_price', array( $this, 'multi_currency_price' ) );
        add_filter( 'yith_wcbk_get_price_to_display', array( $this, 'multi_currency_price' ) ); // for totals
        add_filter( 'yith_wcbk_booking_service_get_pricing_html_price', array( $this, 'multi_currency_price' ) ); // for service prices in tooltip
        add_action( 'yith_wcbk_booking_form_start', array( $this, 'add_currency_hidden_input_in_booking_form' ), 20 );

        add_action( 'wp_ajax_yith_wcbk_get_booking_data', array( $this, 'set_currency_and_filters' ), 9 );
        add_action( 'wp_ajax_nopriv_yith_wcbk_get_booking_data', array( $this, 'set_currency_and_filters' ), 9 );

        add_filter( 'wcml_price_custom_fields_filtered', array( $this, 'remove_price_key_for_booking_products' ), 10, 2 );
    }

    /**
     * return true if the current version of WPML Multi Currency has the right classes and methods
     *
     * @return bool
     */
    public function check_wpml_classes() {
        global $woocommerce_wpml;

        return $woocommerce_wpml && isset( $woocommerce_wpml->multi_currency )
               && isset( $woocommerce_wpml->multi_currency->prices )
               && is_callable( array( $woocommerce_wpml->multi_currency, 'set_client_currency' ) )
               && is_callable( array( $woocommerce_wpml->multi_currency, 'get_client_currency' ) );
    }

    public function set_currency_and_filters() {
        global $woocommerce_wpml;
        if ( !$this->check_wpml_classes() || empty( $_REQUEST[ 'yith_wcbk_wpml_currency' ] ) )
            return;

        // Set the currency
        $currency = $_REQUEST[ 'yith_wcbk_wpml_currency' ];
        $woocommerce_wpml->multi_currency->set_client_currency( $currency );

        // Add filters
        add_filter( 'woocommerce_currency', array( $this, 'currency_filter' ) );
        add_filter( 'yith_wcbk_get_calculated_price_html_price', array( $this, 'multi_currency_price' ), 10, 3 );
    }

    /**
     * remove the _price key from multi currency filtered keys to prevent double price filtering
     * the $object_id parameter will be added in WooCommerce Multi Currency > 4.4.2.1
     *
     * @param  array $price_keys
     * @param int    $object_id
     * @return mixed
     */
    public function remove_price_key_for_booking_products( $price_keys, $object_id = 0 ) {
        if ( $object_id && is_numeric( $object_id ) && yith_wcbk_is_booking_product( $object_id ) && false !== ( $key = array_search( '_price', $price_keys ) ) ) {
            unset( $price_keys[ $key ] );
        }

        return $price_keys;
    }

    /**
     * filter the currency
     *
     * @param $currency
     * @return string
     */
    public function currency_filter( $currency ) {
        global $woocommerce_wpml;

        return $woocommerce_wpml->multi_currency->get_client_currency();
    }

    /**
     * change price based on currency
     *
     * @param                    $price
     * @return float
     */
    function multi_currency_price( $price ) {
        $price = apply_filters( 'wcml_raw_price_amount', $price );
        return $price;
    }

    /**
     * add an hidden input field in booking form
     */
    public function add_currency_hidden_input_in_booking_form() {
        global $woocommerce_wpml;
        if ( !$this->check_wpml_classes() )
            return;

        $client_currency = $woocommerce_wpml->multi_currency->get_client_currency();
        echo "<input type='hidden' class='yith-wcbk-booking-form-additional-data' name='yith_wcbk_wpml_currency' value='$client_currency' />";
    }
}