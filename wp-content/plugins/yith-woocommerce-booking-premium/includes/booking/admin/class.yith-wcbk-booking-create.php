<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Booking_Create' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Create
	 * handle booking creating in backend
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Booking_Create {

		/** @var YITH_WCBK_Booking_Create */
		protected static $_instance;

		/** @var string screen id of the 'Create Booking' page */
		public static $screen_id = 'yith_booking_page_create_booking';

		/** @var string Create Page url */
		protected $_create_page = 'edit.php?post_type=yith_booking&page=create_booking';

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Booking_Create
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Booking_Create constructor.
		 */
		protected function __construct() {
			add_action( 'admin_menu', array( $this, 'add_create_in_booking_menu' ), 20 );
			add_action( 'admin_init', array( $this, 'redirect_to_create_page' ), 99 );
		}

		/**
		 * Add Create in Booking menu
		 */
		public function add_create_in_booking_menu() {
			$booking_page = 'edit.php?post_type=' . YITH_WCBK_Post_Types::$booking;

			add_submenu_page( $booking_page,
							  __( 'Create Booking', 'yith-booking-for-woocommerce' ),
							  __( 'Create Booking', 'yith-booking-for-woocommerce' ),
							  'yith_create_booking',
							  'create_booking',
							  array( $this, 'output' )
			);
		}

		/**
		 * handle create Booking
		 */
		public function create_booking() {
			if ( ! self::is_create_page() || ! isset( $_POST['create_booking'] ) ) {
				return;
			}

			$args                = $_POST;
			$args['add-to-cart'] = $args['product_id'];
			$args                = YITH_WCBK_Cart::get_booking_data_from_request( $args );

			$user_id    = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
			$product_id = isset( $_POST['product_id'] ) ? $_POST['product_id'] : 0;
			$order      = isset( $_POST['order'] ) ? $_POST['order'] : 'no';
			$order_id   = isset( $_POST['order_id'] ) ? $_POST['order_id'] : 0;

			/** @var WC_Product_Booking $product */
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {

				if ( $order === 'yes' ) {
					if ( ! $order_id ) {
						// Create a new order
						$order_data = array(
							'status'      => apply_filters( 'woocommerce_default_order_status', 'pending' ),
							'customer_id' => $user_id,
							'created_via' => 'yith_booking',
						);

						$order = wc_create_order( $order_data );
					} else {
						// Add booking in a order
						$order = wc_get_order( $order_id );
					}

					if ( is_wp_error( $order ) ) {
						wp_die( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 520 ) );
					} elseif ( false === $order ) {
						wp_die( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 521 ) );
					} else {
						$order_id = yit_get_prop( $order, 'id', true, 'edit' );
						do_action( 'woocommerce_new_order', $order_id );
					}

					$order_id = yit_get_prop( $order, 'id', true, 'edit' );

					$booking_args_for_cost = $args;
					if ( ! empty( $booking_args_for_cost['person_types'] ) ) {
						$person_types = array();
						foreach ( $booking_args_for_cost['person_types'] as $person_type_id => $person_type_number ) {
							$person_type_title = get_the_title( $person_type_id );
							$person_types[]    = array(
								'id'     => $person_type_id,
								'title'  => $person_type_title,
								'number' => $person_type_number,
							);
						}
						$booking_args_for_cost['person_types'] = $person_types;
					}

					$booking_cost = $product->calculate_price( $booking_args_for_cost );
					if ( wc_prices_include_tax() ) {
						$booking_cost = wc_get_price_excluding_tax( $product, array( 'price' => $booking_cost ) );
					}

					$item_id = $order->add_product( $product, 1, apply_filters( 'yith_wcbk_create_booking_order_item_data', array(
						'variation' => '',
						'totals'    => array(
							'subtotal' => $booking_cost,
							'total'    => $booking_cost,
						),
					), $product ) );

					if ( ! $item_id ) {
						wp_die( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 525 ) );
					}

					$values = array( 'yith_booking_data' => $args );

					/** @var WC_Order_Item_Product $item */
					$item = $order->get_item( $item_id );
					YITH_WCBK()->orders->woocommerce_checkout_create_order_line_item( $item, '', $values, $order );
					$item->save_meta_data();

					// Allow plugins to add order item meta
					do_action( 'woocommerce_new_order_item', $item->get_id(), $item, $item->get_order_id() );

					$order->calculate_totals();

					// Fire action to check if order has booking and create Bookings
					do_action( 'yith_wcbk_check_order_with_booking', $order_id, array() );

					wp_safe_redirect( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) );
					exit();

				} elseif ( $order === 'no' ) {
					// Not create order, create only booking
					$args['title']      = $product->get_title();
					$args['user_id']    = $user_id;
					$args['product_id'] = $product_id;

					$args['duration_unit'] = $product->get_duration_unit();

					if ( ! empty( $args['person_types'] ) ) {
						$person_types = array();
						foreach ( $args['person_types'] as $person_type_id => $person_type_number ) {
							$person_type_title = get_the_title( $person_type_id );
							$person_types[]    = array(
								'id'     => $person_type_id,
								'title'  => $person_type_title,
								'number' => $person_type_number,
							);
						}
						$args['person_types'] = $person_types;
					}

					if ( isset( $args['booking_services'] ) ) {
						$args['services'] = $args['booking_services'];
						unset( $args['booking_services'] );
					}

					$booking = new YITH_WCBK_Booking( 0, $args );

					if ( $booking->is_valid() ) {
						wp_safe_redirect( admin_url( 'post.php?post=' . $booking->id . '&action=edit' ) );
						exit();
					}

				}
			}
			wp_die( __( 'Error in creating booking', 'yith-booking-for-woocommerce' ) );
		}

		/**
		 * return true if this is Create Page
		 *
		 * @return bool
		 */
		public static function is_create_page() {
			return isset( $_GET['post_type'] ) && $_GET['post_type'] === YITH_WCBK_Post_Types::$booking && isset( $_GET['page'] ) && $_GET['page'] === 'create_booking';
		}

		/**
		 * Print section
		 */
		public function output() {
			if ( ! self::is_create_page() ) {
				return;
			}

			include YITH_WCBK_VIEWS_PATH . 'html-booking-create.php';
		}

		/**
		 * Redirect to custom page when go to add new booking
		 */
		public function redirect_to_create_page() {
			global $pagenow;
			$booking_page = 'edit.php?post_type=' . YITH_WCBK_Post_Types::$booking;

			if ( 'post-new.php' == $pagenow && isset( $_REQUEST['post_type'] ) && YITH_WCBK_Post_Types::$booking === $_REQUEST['post_type'] ) {
				wp_redirect( admin_url( $booking_page ) );
				exit();
			} elseif ( $this->is_create_page() && isset( $_POST['create_booking'] ) ) {
				$this->create_booking();
				exit();
			}
		}
	}
}

return YITH_WCBK_Booking_Create::get_instance();