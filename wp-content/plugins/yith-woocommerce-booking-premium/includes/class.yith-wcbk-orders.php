<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Orders' ) ) {
	/**
	 * Class YITH_WCBK_Orders
	 * handle order processes for Booking products
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Orders {

		/** @var YITH_WCBK_Orders */
		private static $_instance;

		/** @var string Order item data prefix */
		public static $order_item_data_prefix = 'yith_booking_';

		/** @var string Order bookings meta */
		public static $order_bookings_meta = 'yith_bookings';

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Orders
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Orders constructor.
		 */
		private function __construct() {
			// Add booking data in order item meta
			add_filter( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 10, 4 );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'check_order_for_booking' ), 999, 2 );
			add_action( 'yith_wcbk_check_order_with_booking', array( $this, 'check_order_for_booking' ), 999, 2 );
			add_action( 'woocommerce_order_status_completed', array( $this, 'set_booking_as_paid' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'set_booking_as_paid' ) );
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'set_booking_as_cancelled' ), 10, 2 );

			add_action( 'woocommerce_resume_order', array( $this, 'cancel_bookings_before_resuming_order' ), 10, 1 );

			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'show_related_bookings' ) );

			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );

			add_action( 'wp_ajax_woocommerce_add_order_item', array( $this, 'prevent_adding_booking_products_in_orders' ), 5 );

			add_action( 'add_meta_boxes', array( $this, 'add_order_related_bookings_meta_box' ) );
		}

		/**
		 * Add the related bookings metabox in orders
		 *
		 * @param string $post_type
		 *
		 * @since 2.1.16
		 */
		public function add_order_related_bookings_meta_box( $post_type ) {
			if ( 'shop_order' === $post_type ) {
				add_meta_box( 'yith-wcbk-order-related-bookings',
							  __( 'Related Bookings', 'yith-booking-for-woocommerce' ),
							  array( $this, 'print_order_related_bookings_meta_box' ),
							  'shop_order',
							  'normal',
							  'default' );
			}
		}

		/**
		 * Print the related bookings metabox in orders
		 *
		 * @param WP_Post $post
		 *
		 * @since 2.1.16
		 */
		public function print_order_related_bookings_meta_box( $post ) {
			$order = wc_get_order( $post->ID );
			if ( $order ) {
				$bookings = YITH_WCBK()->booking_helper->get_bookings_by_order( $order->get_id() );
				$bookings = apply_filters( 'yith_wcbk_order_bookings_related_to_order', $bookings, $order );
				if ( ! ! $bookings ) {
					yith_wcbk_get_view( '/metaboxes/html-order-related-bookings.php', compact( 'bookings', 'order' ) );
				}
			}
		}

		/**
		 * when resuming orders the old bookings related to the order will be cancelled
		 * since the cart items will be re-created  by WooCommerce
		 * so also the bookings will be re-created
		 *
		 * @param $order_id
		 *
		 * @since 2.1.2
		 */
		public function cancel_bookings_before_resuming_order( $order_id ) {
			$bookings = YITH_WCBK_Booking_Helper()->get_bookings_by_order( $order_id );
			if ( ! ! $bookings ) {
				$order        = wc_get_order( $order_id );
				$order_number = $order ? $order->get_order_number() : $order_id;
				foreach ( $bookings as $booking ) {
					$booking = yith_get_booking( $booking );
					if ( $booking ) {
						$additional_note = sprintf( __( 'Reason: order <a href="%s">#%s</a> has been resumed (probably due to a failed payment).', 'yith-booking-for-woocommerce' ),
													admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
													$order_number );
						$booking->update_status( 'cancelled', $additional_note );
					}
				}
			}
		}

		/**
		 * don't allow adding booking to orders through "Add products" box in orders
		 *
		 * @since 2.0.7
		 */
		public function prevent_adding_booking_products_in_orders() {
			if ( isset( $_POST['data'] ) ) {
				$items_to_add = array_filter( wp_unslash( (array) $_POST['data'] ) );

				$booking_titles = array();
				foreach ( $items_to_add as $item ) {
					if ( ! isset( $item['id'], $item['qty'] ) || empty( $item['id'] ) ) {
						continue;
					}
					$product_id = absint( $item['id'] );
					if ( yith_wcbk_is_booking_product( $product_id ) ) {
						$product = wc_get_product( $product_id );
						if ( $product ) {
							$booking_titles[] = $product->get_formatted_name();
						}
					}
				}

				if ( $booking_titles ) {
					wp_send_json_error( array( 'error' => sprintf( __( 'You are trying to add the following Booking Products to the order: %s. You cannot add Booking products to orders through this box. To do it, please use the Create Booking page in Bookings menu', 'yith-booking-for-woocommerce' ), implode( ', ', $booking_titles ) ) ) );
				}
			}
		}


		/**
		 * hide order item meta
		 *
		 * @param $hidden
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function hide_order_item_meta( $hidden ) {
			$hidden[] = '_added-to-cart-timestamp';

			return $hidden;
		}

		/**
		 * show related bookings in order table
		 *
		 * @param WC_Order $order
		 */
		public function show_related_bookings( $order ) {
			$order_id = yit_get_prop( $order, 'id', true, 'edit' );
			$bookings = YITH_WCBK()->booking_helper->get_bookings_by_order( $order_id );
			$bookings = apply_filters( 'yith_wcbk_order_bookings_related_to_order', $bookings, $order );
			if ( ! ! $bookings ) {
				echo '<h2>' . apply_filters( 'yith_wcbk_related_booking_title', __( 'Related Bookings', 'yith-booking-for-woocommerce' ) ) . '</h2>';
			}
			do_action( 'yith_wcbk_show_bookings_table', $bookings );
		}

		/**
		 * add meta in order
		 *
		 * @param int          $item_id
		 * @param array|object $values
		 *
		 * @deprecated  since 2.0.0 use YITH_WCBK_Orders::woocommerce_checkout_create_order_line_item instead
		 */
		public function woocommerce_add_order_item_meta( $item_id, $values ) {
			//do nothing
		}

		/**
		 * add booking data to order items
		 *
		 * @param WC_Order_Item_Product $item
		 * @param string                $cart_item_key
		 * @param array                 $values
		 * @param WC_Order              $order
		 *
		 * @since 2.0.0
		 */
		public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
			$booking_data = false;

			if ( isset( $values['yith_booking_data'] ) && is_array( $values['yith_booking_data'] ) ) {
				$booking_data = $values['yith_booking_data'];
			}

			if ( $booking_data ) {
				$booking_data = $this->parse_booking_data( $booking_data );

				/* add booking data: will be hidden because it's array */
				$item->add_meta_data( 'yith_booking_data', $booking_data, true );

				// add booking id data if booking required confirmation and it's confirmed
				if ( isset( $booking_data['_booking_id'] ) ) {
					$item->add_meta_data( '_booking_id', $booking_data['_booking_id'], true );
				}

				// 'show-details-in-order-items' option removed since 2.1.16; kept for backward compatibility
				$show_details = 'yes' === YITH_WCBK()->settings->get( 'show-details-in-order-items', 'no' );
				$show_details = apply_filters( 'yith_wcbk_order_add_booking_details_in_order_item', $show_details, $item, $values, $order );

				if ( $show_details ) {
					/* Add booking data to display in order */
					$product_id = isset( $values['product_id'] ) ? $values['product_id'] : 0;
					$details    = $this->get_booking_order_item_details( $booking_data, $product_id );
					foreach ( $details as $detail ) {
						$item->add_meta_data( $detail['key'], $detail['value'], true );
					}
				}

			}
		}

		/**
		 * parse booking data to retrieve correct values from people, services and service quantities
		 *
		 * @param array $booking_data
		 *
		 * @return array
		 * @since 2.0.6
		 */
		public function parse_booking_data( $booking_data ) {
			if ( ! empty( $booking_data['person_types'] ) ) {
				$booking_data_person_types = array();
				foreach ( $booking_data['person_types'] as $person_type_id => $person_type_number ) {
					$person_type_title           = get_the_title( $person_type_id );
					$booking_data_person_types[] = array(
						'id'     => $person_type_id,
						'title'  => $person_type_title,
						'number' => $person_type_number,
					);
				}
				$booking_data['person_types'] = $booking_data_person_types;
			}

			if ( ! empty( $booking_data['booking_services'] ) ) {
				$booking_data_services = array();
				$service_quantities    = isset( $booking_data['booking_service_quantities'] ) ? $booking_data['booking_service_quantities'] : array();
				foreach ( $booking_data['booking_services'] as $service_id ) {
					$service = yith_get_booking_service( $service_id );
					if ( $service->is_valid() ) {
						$quantity                   = isset( $service_quantities[ $service_id ] ) ? $service_quantities[ $service_id ] : false;
						$booking_data_services[]    = array(
							'id'     => $service_id,
							'title'  => $service->get_name_with_quantity( $quantity ),
							'hidden' => $service->is_hidden(),
						);
						$booking_data['services'][] = $service_id;
					}
				}
				$booking_data['booking_services'] = $booking_data_services;
			}

			if ( ! empty( $booking_data['booking_service_quantities'] ) ) {
				$booking_data['service_quantities'] = $booking_data['booking_service_quantities'];
				unset( $booking_data['booking_service_quantities'] );
			}

			return apply_filters( 'yith_wcbk_order_parse_booking_data', $booking_data );
		}

		/**
		 * get booking details from booking data
		 *
		 * @param array $booking_data
		 * @param int   $product_id
		 *
		 * @return array
		 * @since 2.0.6
		 */
		public function get_booking_order_item_details( $booking_data, $product_id = 0 ) {
			$details = array();
			foreach ( $booking_data as $booking_data_key => $booking_data_value ) {
				$this_title = yith_wcbk_get_booking_meta_label( $booking_data_key );

				switch ( $booking_data_key ) {
					case 'person_types':
						if ( is_array( $booking_data_value ) ) {
							foreach ( $booking_data_value as $person_type ) {
								if ( ! empty( $person_type['number'] ) ) {
									$details[] = array(
										'key'   => $person_type['title'],
										'value' => $person_type['number'],
									);
								}

							}
						}
						break;
					case 'booking_services':
						if ( is_array( $booking_data_value ) ) {
							$booking_services        = array();
							$hidden_booking_services = array();
							foreach ( $booking_data_value as $service ) {
								if ( ! $service['hidden'] ) {
									$booking_services[] = $service['title'];
								} else {
									$hidden_booking_services[] = $service['title'];
								}
							}
							if ( ! ! $booking_services ) {
								$details[] = array(
									'key'   => yith_wcbk_get_label( 'booking-services' ),
									'value' => yith_wcbk_booking_services_html( $booking_services ),
								);
							}
							if ( ! ! $hidden_booking_services ) {
								$details[] = array(
									'key'   => '_hidden_booking_services',
									'value' => yith_wcbk_booking_services_html( $hidden_booking_services ),
								);
							}
						}
						break;

					case 'from':
					case 'to':
						/** @var WC_Product_Booking $product */
						$product     = wc_get_product( $product_id );
						$date_format = wc_date_format();
						if ( $product && $product->is_type( 'booking' ) && $product->has_time() ) {
							$date_format .= ' ' . wc_time_format();
						}
						$this_value = date_i18n( $date_format, $booking_data_value );
						$details[]  = array(
							'key'   => $this_title,
							'value' => $this_value,
						);
						break;
					case 'duration':
						$this_value = $booking_data_value;
						$product    = wc_get_product( $product_id );
						if ( $product && $product instanceof WC_Product_Booking ) {
							$duration_unit       = $product->get_duration_unit();
							$duration_unit_label = yith_wcbk_get_duration_unit_label( $duration_unit, absint( $booking_data_value ) );
							$this_value          .= ' ' . $duration_unit_label;
						}
						$details[] = array(
							'key'   => $this_title,
							'value' => $this_value,
						);
						break;
					default:
						$details[] = array(
							'key'   => $this_title,
							'value' => $booking_data_value,
						);
						break;
				}
			}

			return apply_filters( 'yith_wcbk_order_get_booking_order_item_details', $details, $booking_data, $product_id );
		}

		/**
		 * Check if order contains booking products.
		 * If it contains a booking product, it will create the booking
		 *
		 * @param int   $order_id
		 * @param array $posted
		 */
		public function check_order_for_booking( $order_id, $posted = array() ) {
			if ( ! apply_filters( 'yith_wcbk_order_check_order_for_booking', true, $order_id, $posted ) ) {
				return;
			}

			$order = wc_get_order( $order_id );
			/** @var WC_Order_item[] $order_items */
			$order_items = $order->get_items();

			if ( ! $order_items ) {
				return;
			}

			$bookings = $order->get_meta( self::$order_bookings_meta );
			$bookings = ! ! $bookings && is_array( $bookings ) ? $bookings : array();

			foreach ( $order_items as $order_item_id => $order_item ) {
				if ( $order_item->is_type( 'line_item' ) ) {
					/**
					 * @var WC_Order_Item_Product $order_item
					 * @var WC_Product_Booking    $product
					 */
					$product = $order_item->get_product();
					if ( ! $product || ! YITH_WCBK_Product_Post_Type_Admin::is_booking( $product ) ) {
						continue;
					}

					$args           = array();
					$booking_data   = $order_item->get_meta( 'yith_booking_data' );
					$the_booking_id = $order_item->get_meta( '_booking_id' );

					if ( ! $the_booking_id && ! ! $booking_data && isset( $booking_data['from'] ) ) {

						foreach ( $booking_data as $booking_data_key => $booking_data_value ) {
							$unserialized_value        = maybe_unserialize( $booking_data_value );
							$args[ $booking_data_key ] = $unserialized_value;
						}

						$product_id            = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $product->get_id() );
						$args['product_id']    = $product_id;
						$args['title']         = $product->get_title();
						$args['duration_unit'] = $product->get_duration_unit();
						$args['order_id']      = $order_id;
						$args['order_item_id'] = $order_item_id;
						$args['user_id']       = $order->get_user_id();

						/* ===== C R E A T E   B O O K I N G ===== */
						$booking = new YITH_WCBK_Booking( '', $args );

						if ( $booking->id ) {
							$order_item->add_meta_data( '_booking_id', $booking->id, true );
							$order_item->save_meta_data();

							$bookings[] = $booking->id;

							$order->add_order_note( sprintf( __( 'A new booking <a href="%s">#%s</a> has been created from this order', 'yith-booking-for-woocommerce' ), admin_url( 'post.php?post=' . $booking->id . '&action=edit' ), $booking->id ) );

							do_action( 'yith_wcbk_order_booking_created', $booking, $order, $order_item_id );
						}
					} elseif ( $the_booking_id ) {
						$booking = yith_get_booking( $the_booking_id );
						if ( $booking && $booking->is_valid() && $booking->has_status( 'confirmed' ) ) {
							$booking->set( 'order_id', $order_id );
							$booking->set( 'order_item_id', $order_item->get_id() );
							$booking_note = sprintf( __( 'Booking associated to order <a href="%s">#%s</a>', 'yith-booking-for-woocommerce' ), admin_url( 'post.php?post=' . $order_id . '&action=edit' ), $order->get_order_number() );
							$booking->add_note( 'new-order', $booking_note );
							$booking->update_status( 'unpaid' );
						}
					}
				}
			}

			$order->update_meta_data( self::$order_bookings_meta, array_unique( $bookings ) );
			$order->save_meta_data();
		}

		/**
		 * Set Booking as paid
		 *
		 * @param $order_id
		 */
		public function set_booking_as_paid( $order_id ) {
			$bookings = YITH_WCBK_Booking_Helper()->get_bookings_by_order( $order_id );
			if ( ! ! ( $bookings ) ) {
				foreach ( $bookings as $booking ) {
					if ( $booking instanceof YITH_WCBK_Booking && ! $booking->has_status( 'cancelled' ) ) {
						$booking->update_status( 'paid' );
					}
				}
			}
		}

		/**
		 * Set Booking as cancelled
		 *
		 * @param int      $order_id
		 * @param WC_Order $order
		 *
		 * @since 1.0.1
		 */
		public function set_booking_as_cancelled( $order_id, $order ) {
			$bookings = YITH_WCBK_Booking_Helper()->get_bookings_by_order( $order_id );
			if ( ! ! $bookings ) {
				$order_number = $order ? $order->get_order_number() : $order_id;
				foreach ( $bookings as $booking ) {
					if ( $booking instanceof YITH_WCBK_Booking ) {
						$additional_note = sprintf( __( 'Reason: order <a href="%s">#%s</a> has been cancelled.', 'yith-booking-for-woocommerce' ),
													admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
													$order_number );
						$booking->update_status( 'cancelled', $additional_note );
					}
				}
			}
		}
	}
}