<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Frontend_Action_Handler' ) ) {
	/**
	 * Class YITH_WCBK_Frontend_Action_Handler
	 * handle all Frontend Actions (as request-confirmation)
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Frontend_Action_Handler {

		/**
		 * Hook in methods.
		 */
		public static function init() {
			add_action( 'wp_loaded', array( __CLASS__, 'request_confirmation_action' ), 90 );

			add_action( 'wp_loaded', array( __CLASS__, 'cancel_booking_action' ), 90 );

			add_action( 'wp_loaded', array( __CLASS__, 'pay_confirmed_booking' ), 90 );

			add_filter( 'woocommerce_login_redirect', array( __CLASS__, 'login_redirect' ), 10, 1 );

			add_filter( 'woocommerce_registration_redirect', array( __CLASS__, 'login_redirect' ), 10, 1 );
		}

		/**
		 * Request confirm action.
		 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
		 */
		public static function request_confirmation_action() {
			if ( empty( $_REQUEST['booking-request-confirmation'] ) || ! is_numeric( $_REQUEST['booking-request-confirmation'] ) ) {
				return;
			}

			$product_id = apply_filters( 'yith_wcbk_request_confirmation_product_id', absint( $_REQUEST['booking-request-confirmation'] ) );
			/**  @var WC_Product_Booking $product */
			$product = wc_get_product( $product_id );

			if ( ! $product || ! YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				return;
			}

			if ( apply_filters( 'yith_wcbk_request_confirmation_login_required', true ) && ! is_user_logged_in() ) {
				$notice = apply_filters( 'yith_wcbk_notice_for_request_confirmation_login_required', __( 'You must log in before asking for a booking confirmation', 'yith-booking-for-woocommerce' ) );
				if ( wc_get_page_id( 'myaccount' ) ) {
					$link_url = add_query_arg( array( 'yith-wcbk-product-redirect' => $product->get_id() ), wc_get_page_permalink( 'myaccount' ) );
					$text     = apply_filters( 'yith_wcbk_button_text_for_request_confirmation_login_required', __( 'Log in', 'yith-booking-for-woocommerce' ) );
					$notice   .= "<a class='button' href='{$link_url}'>" . $text . "</a>";
				}

				$notice = apply_filters( 'yith_wcbk_request_confirmation_login_required_notice', $notice, $product );

				wc_add_notice( $notice, 'error' );

				return;
			}

			$args                = $_REQUEST;
			$args['add-to-cart'] = $product_id;

			$booking_data                  = YITH_WCBK_Cart::get_booking_data_from_request( $args );
			$booking_data['title']         = $product->get_title();
			$booking_data['product_id']    = $product_id;
			$booking_data['status']        = 'bk-pending-confirm';
			$booking_data['duration_unit'] = $product->get_duration_unit();

			if ( is_user_logged_in() ) {
				$booking_data['user_id'] = get_current_user_id();
			}

			if ( isset( $booking_data['person_types'] ) ) {
				$booking_data['person_types'] = yith_wcbk_booking_person_types_to_list( $booking_data['person_types'] );
			}

			if ( isset( $booking_data['booking_services'] ) ) {
				$booking_data['services'] = $booking_data['booking_services'];
				unset( $booking_data['booking_services'] );
			}

			if ( isset( $booking_data['booking_service_quantities'] ) ) {
				$booking_data['service_quantities'] = $booking_data['booking_service_quantities'];
				unset( $booking_data['booking_service_quantities'] );
			}

			$booking = new YITH_WCBK_Booking( 0, $booking_data );
			$success = $booking->is_valid();

			if ( $success ) {
				wc_add_notice( __( 'Request for booking confirmation sent', 'yith-booking-for-woocommerce' ) );
			} else {
				wc_add_notice( __( 'Error in requesting booking confirmation', 'yith-booking-for-woocommerce' ), 'error' );
			}

			do_action( 'yith_wcbk_after_request_confirmation_action', $success, $booking, $product, $booking_data, $args );

			$redirect = apply_filters( 'yith_wcbk_redirect_after_request_confirmation_action', false, $success, $booking, $product, $booking_data, $args );
			if ( $redirect ) {
				$redirect = esc_url_raw( $redirect );
				wp_redirect( $redirect );
				exit;
			}
		}

		/**
		 * Cancel Booking
		 */
		public static function cancel_booking_action() {
			if ( empty( $_REQUEST['cancel-booking'] ) || ! is_numeric( $_REQUEST['cancel-booking'] ) || ! is_user_logged_in() ) {
				return;
			}
			$booking_id = absint( $_REQUEST['cancel-booking'] );
			$booking    = yith_get_booking( $booking_id );
			if ( ! $booking || ! $booking->is_valid() || $booking->user_id != get_current_user_id() || ! $booking->can_be( 'cancelled_by_user' ) ) {
				wc_add_notice( __( 'This booking cannot be cancelled. Contact us for more info', 'yith-booking-for-woocommerce' ), 'error' );

				return;
			}

			$booking->update_status( 'cancelled_by_user' );

			if ( $booking->has_status( 'cancelled' ) ) {
				wc_add_notice( sprintf( __( 'Booking %s cancelled', 'yith-booking-for-woocommerce' ), $booking->get_title() ) );
			} else {
				wc_add_notice( sprintf( __( 'Booking %s not cancelled', 'yith-booking-for-woocommerce' ), $booking->get_title() ), 'error' );
			}

			wp_safe_redirect( $booking->get_view_booking_url() );
			exit;
		}

		/**
		 * Pay confirmed Booking
		 */
		public static function pay_confirmed_booking() {
			if ( empty( $_REQUEST['pay-confirmed-booking'] ) || ! is_numeric( $_REQUEST['pay-confirmed-booking'] ) || ! is_user_logged_in() ) {
				return;
			}
			$error      = false;
			$booking_id = absint( $_REQUEST['pay-confirmed-booking'] );
			$booking    = yith_get_booking( $booking_id );

			if ( ! $booking || ! $booking->is_valid() || $booking->user_id != get_current_user_id() ) {
				$error = true;
			}

			$product_id = $booking->product_id;
			$product    = wc_get_product( $booking->product_id );
			if ( ! $product ) {
				$error = true;
			}

			if ( $error ) {
				wc_add_notice( __( 'This booking cannot be paid. Contact us for more info', 'yith-booking-for-woocommerce' ), 'error' );

				return;
			}

			$cart = WC()->cart;
			$cart->empty_cart();

			$cart_item_data = array(
                'yith_booking_data' => apply_filters( 'yith_booking_cart_item_data', array(
                    'from'                       => $booking->from,
                    'to'                         => $booking->to,
                    'duration'                   => $booking->get_duration(),
                    'persons'                    => $booking->persons,
                    'person_types'               => yith_wcbk_booking_person_types_to_id_number_array( $booking->person_types ),
                    'booking_services'           => $booking->services,
                    'booking_service_quantities' => $booking->get_service_quantities(),
                    '_booking_id'                => $booking_id,
                    '_added-to-cart-timestamp'   => strtotime( 'now' )
                ), $booking )
            );

			$cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );

			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}

		/**
		 * Filter login redirect to allow custom redirect
		 *
		 * @param string $redirect
		 *
		 * @return string
		 * @since 2.1.18
		 */
		public static function login_redirect( $redirect ) {
			if ( isset( $_REQUEST['yith-wcbk-product-redirect'] ) ) {
				$product_id = absint( $_REQUEST['yith-wcbk-product-redirect'] );
				$product    = wc_get_product( $product_id );
				if ( $product ) {
					$redirect = $product->get_permalink();
				}
			}

			return $redirect;
		}
	}
}