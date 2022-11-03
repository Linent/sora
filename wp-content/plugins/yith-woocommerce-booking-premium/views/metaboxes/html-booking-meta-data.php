<?php
/**
 * Booking Data Metabox
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */
if ( ! defined( 'YITH_WCBK' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var YITH_WCBK_Booking $booking
 */

$post = $booking->post;
?>
<div id="booking-data" class="panel">

	<h2><?php printf( _x( '%s details', 'Booking #123 details', 'yith-booking-for-woocommerce' ), $booking->get_name() ) ?>
		<span class="yith-booking-status <?php echo $booking->get_status() ?>"><?php echo $booking->get_status_text() ?></span>
	</h2>

	<div class="booking_data_column_container">
		<div class="booking_data_column">
			<h4><?php _e( 'General Details', 'yith-booking-for-woocommerce' ) ?></h4>

			<p class="form-field form-field-wide">
				<label for="yith_booking_date"><?php _e( 'Booking creation date:', 'yith-booking-for-woocommerce' ) ?></label>
				<input type="text" class="date-picker" name="yith_booking_date" id="yith-booking-date" maxlength="10" value="<?php echo date_i18n( 'Y-m-d', strtotime( $post->post_date ) ); ?>"
						pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>@<input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'woocommerce' ) ?>"
						name="yith_booking_date_hour" id="yith-booking-date-hour" min="0" max="23" step="1"
						value="<?php echo date_i18n( 'H', strtotime( $post->post_date ) ); ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})"/>:<input
						type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'woocommerce' ) ?>" name="yith_booking_date_minute" id="yith-booking-date-minute" min="0" max="59" step="1"
						value="<?php echo date_i18n( 'i', strtotime( $post->post_date ) ); ?>" pattern="[0-5]{1}[0-9]{1}"/>
			</p>

			<p class="form-field form-field-wide">
				<label for="yith_booking_status"><?php _e( 'Booking status:', 'yith-booking-for-woocommerce' ) ?></label>
				<select id="yith_booking_status" name="yith_booking_status" class="wc-enhanced-select" style="width:100%">
					<?php
					$statuses = yith_wcbk_get_booking_statuses();
					foreach ( $statuses as $status => $status_name ) {
						echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, $booking->get_status(), false ) . '>' . esc_html( $status_name ) . '</option>';
					}
					?>
				</select>
			</p>

			<p class="form-field form-field-wide yith-booking-product">
				<?php
				$product_id = ! empty( $booking->product_id ) ? $booking->product_id : false;
				$product    = wc_get_product( $product_id );

				if ( $product ) {
					$product_name = $product->get_formatted_name();
				} elseif ( $product_id ) {
					$product_name = sprintf( __( 'Deleted Product #%s', 'yith-booking-for-woocommerce' ), $product_id );
				} else {
					$product_name = '';
				}

				?>
				<label for="yith_booking_product"><?php _e( 'Booking Product:', 'yith-booking-for-woocommerce' ) ?>
					<?php
					if ( $product ) {
						$product_edit_link = get_edit_post_link( $product_id );
						printf( '<a href="%s">%s &rarr;</a>', $product_edit_link, __( 'View product', 'yith-booking-for-woocommerce' ) );
					}
					?>
				</label>
				<?php echo "<p>$product_name</p>"; ?>
			</p>

			<p class="form-field form-field-wide yith-booking-order">
				<label for="yith_booking_order"><?php _e( 'Order:', 'yith-booking-for-woocommerce' ) ?>
					<?php
					if ( ! empty( $booking->order_id ) ) {
						$order_id   = absint( $booking->order_id );
						$order_link = get_edit_post_link( $order_id );
						printf( '<a href="%s">%s &rarr;</a>', $order_link, __( 'View order', 'yith-booking-for-woocommerce' ) );
					}
					?>
				</label>
				<?php
				$order_string  = '';
				$order_id      = '';
				$data_selected = array();

				if ( ! empty( $booking->order_id ) ) {
					$order_id                   = $booking->order_id;
					$order_string               = '#' . absint( $order_id ) . ' &ndash; ' . esc_html( get_the_title( $order_id ) );
					$data_selected[ $order_id ] = $order_string;
				}

				if ( current_user_can( 'yith_manage_bookings' ) ) {
					yit_add_select2_fields( array(
												'class'            => 'yith-wcbk-order-search',
												'id'               => 'yith_booking_order',
												'name'             => 'yith_booking_order',
												'data-placeholder' => __( 'N.D.', 'yith-booking-for-woocommerce' ),
												'data-allow_clear' => true,
												'data-multiple'    => false,
												'value'            => $order_id,
												'data-selected'    => $data_selected,
												'style'            => 'width:100%',
											) );

				} else {
					echo $order_string;
				} ?>
			</p>

			<p class="form-field form-field-wide yith-booking-user">
				<label for="yith_booking_user"><?php _e( 'User:', 'yith-booking-for-woocommerce' ) ?>
					<?php if ( current_user_can( 'yith_manage_bookings' ) ) : ?>
						<?php
						if ( ! empty( $booking->user_id ) ) {
							$user_id   = absint( $booking->user_id );
							$edit_link = get_edit_user_link( $user_id );
							printf( '<a href="%s">%s &rarr;</a>', $edit_link, __( 'View user', 'yith-booking-for-woocommerce' ) );
						}
						?>
					<?php endif; ?>

				</label>
				<?php
				$user_string   = '';
				$user_id       = '';
				$data_selected = array();

				if ( ! empty( $booking->user_id ) ) {
					$user_id     = absint( $booking->user_id );
					$user        = get_user_by( 'id', $user_id );
					$user_string = '#' . $user_id;
					if ( $user ) {
						$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
					}
					$data_selected[ $user_id ] = $user_string;
				}
				?>
				<?php if ( current_user_can( 'yith_manage_bookings' ) ) {

					yit_add_select2_fields( array(
												'class'            => 'wc-customer-search',
												'id'               => 'yith_booking_user',
												'name'             => 'yith_booking_user',
												'data-placeholder' => __( 'N.D.', 'yith-booking-for-woocommerce' ),
												'data-allow_clear' => true,
												'data-multiple'    => false,
												'value'            => $user_id,
												'data-selected'    => $data_selected,
												'style'            => 'width:100%',
											) );
				} else {
					echo $user_string;
				} ?>
			</p>

			<?php do_action( 'yith_wcbk_booking_metabox_info_after_first_column', $booking ); ?>
		</div>

		<div class="booking_data_column">
			<h4><?php _e( 'Booking Date', 'yith-booking-for-woocommerce' ) ?></h4>

			<p class="form-field form-field-wide"><label><?php _e( 'Duration', 'yith-booking-for-woocommerce' ) ?>
					:</label>
				<?php echo $booking->get_duration_html(); ?>
			</p>

			<div class="booking_data_half">
				<p class="form-field form-field-wide"><label><?php _e( 'From', 'yith-booking-for-woocommerce' ) ?>
						:</label>
					<?php
					echo yith_wcbk_create_date_field( $booking->duration_unit, array(
						'id'    => 'yith-booking-from',
						'name'  => 'yith_booking_from',
						'value' => $booking->from,
					) );
					?>
				</p>
			</div>
			<div class="booking_data_half">

				<p class="form-field form-field-wide"><label><?php _e( 'To', 'yith-booking-for-woocommerce' ) ?>
						:</label>
					<?php
					echo yith_wcbk_create_date_field( $booking->duration_unit, array(
						'id'    => 'yith-booking-to',
						'name'  => 'yith_booking_to',
						'value' => $booking->to,
					) );
					?>
				</p>
			</div>

			<div class="clear"></div>

			<?php if ( $booking->is_all_day() ): ?>
				<p class="form-field form-field-wide yith-wcbk-booking-all-day-mark__container">
					<span class="yith-wcbk-booking-all-day-mark"><?php _e( 'All Day', 'yith-booking-for-woocommerce' ); ?></span>
				</p>
			<?php endif ?>

			<?php do_action( 'yith_wcbk_booking_metabox_info_after_second_column', $booking ); ?>

		</div>
		<div class="booking_data_column">
			<?php if ( $booking->has_persons() ): ?>

				<h4><?php _e( 'Booking People', 'yith-booking-for-woocommerce' ) ?></h4>

				<p class="form-field form-field-wide"><label><?php _e( 'People', 'yith-booking-for-woocommerce' ) ?>
						:</label>
					<?php
					if ( $booking->has_person_types() ) {
						echo $booking->persons;
					} else {
						?>
						<input type="number" name="yith_booking_persons" id="yith_booking_persons" maxlength="10" value="<?php echo $booking->persons; ?>"/>
						<?php
					}
					?>
				</p>

				<?php if ( $booking->has_person_types() ) : ?>
					<?php foreach ( $booking->person_types as $person_type ) : ?>
						<?php
						$person_type_id     = absint( $person_type['id'] );
						$person_type_title  = get_the_title( $person_type_id );
						$person_type_title  = ! ! $person_type_title ? $person_type_title : $person_type['title'];
						$person_type_number = absint( $person_type['number'] );
						?>
						<p class="form-field form-field-wide"><label><?php echo $person_type_title ?>:</label>
							<input type="number" class="half-width" name="yith_booking_person_type[<?php echo $person_type_id; ?>]" maxlength="10" value="<?php echo $person_type_number; ?>"/>
						</p>
					<?php endforeach; ?>
				<?php endif; ?>

			<?php endif; ?>

			<?php if ( $booking->services ): ?>
				<h4><?php _e( 'Booking Services', 'yith-booking-for-woocommerce' ) ?></h4>
				<table class="yith-wcbk-booking-services-table widefat striped">
					<?php foreach ( $booking->services as $service_id ) :
						$service = yith_get_booking_service( $service_id );
						if ( $service->is_valid() ) : ?>
							<tr class="yith-wcbk-booking-services-table__row">
								<td class="yith-wcbk-booking-services-table__row__label">
									<label class="yith-wcbk-service-quantity__label"><?php echo $service->get_name() ?></label>
								</td>
								<td class="yith-wcbk-booking-services-table__row__value">
									<?php if ( $service->is_quantity_enabled() ) :
										$quantity = $booking->get_service_quantity( $service_id );
										?>
										<input type="number" class="yith-wcbk-service-quantity" name="yith_booking_service_quantities[<?php echo $service_id; ?>]" value="<?php echo $quantity; ?>"/>
									<?php endif ?>
								</td>
							</tr>

						<?php endif ?>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>

			<?php do_action( 'yith_wcbk_booking_metabox_info_after_third_column', $booking ); ?>
		</div>
	</div>
</div>