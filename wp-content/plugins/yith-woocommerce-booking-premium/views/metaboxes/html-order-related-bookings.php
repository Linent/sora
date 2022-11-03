<?php
/**
 * Order Related Bookings
 *
 * @var YITH_WCBK_Booking[] $bookings
 * @var WC_Order            $order
 * @author  YITH
 * @package YITH Booking and Appointment for WooCommerce Premium
 */
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly
?>

<?php foreach ( $bookings as $booking ): ?>
	<?php $product = $booking->get_product(); ?>
	<div class="yith-wcbk-order-related-booking yith-wcbk-order-related-booking--<?php echo $booking->get_status() ?>-status">
		<div class="yith-wcbk-order-related-booking__heading">
			<h3 class="yith-wcbk-order-related-booking__title">
				<a class="yith-wcbk-order-related-booking__title__booking-link" href="<?php echo $booking->get_edit_link() ?>"><?php echo $booking->get_name() ?></a>
				<?php if ( $product ): ?>
					<?php $product_link = get_edit_post_link( $product->get_id() ); ?>
					&ndash;
					<a class="yith-wcbk-order-related-booking__title__product-link" href="<?php echo $product_link ?>"><?php echo $product->get_title() ?></a>
				<?php endif; ?>
			</h3>
		</div>
		<div class="yith-wcbk-order-related-booking__details">
			<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__duration">
				<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Duration', 'yith-booking-for-woocommerce' ); ?></div>
				<div class="yith-wcbk-order-related-booking__field-value"><?php echo $booking->get_duration_html(); ?></div>
			</div>
			<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__dates">
				<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Dates', 'yith-booking-for-woocommerce' ); ?></div>
				<div class="yith-wcbk-order-related-booking__field-value"><?php echo sprintf( '%s - %s', $booking->get_formatted_date( 'from' ), $booking->get_formatted_date( 'to' ) ); ?></div>
			</div>
			<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__status yith-wcbk-order-related-booking__status--<?php echo $booking->get_status() ?>">
				<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Status', 'yith-booking-for-woocommerce' ); ?></div>
				<div class="yith-wcbk-order-related-booking__field-value"><?php echo $booking->get_status_text(); ?></div>
			</div>

			<?php if ( $booking->has_persons() ) : ?>

				<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__people">
					<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'People', 'yith-booking-for-woocommerce' ); ?></div>
					<div class="yith-wcbk-order-related-booking__field-value"><?php
						if ( $booking->has_person_types() ) {
							$persons = array();
							foreach ( $booking->person_types as $person_type ) {
								if ( ! $person_type['number'] ) {
									continue;
								}
								$person_type_id     = absint( $person_type['id'] );
								$person_type_title  = YITH_WCBK()->person_type_helper->get_person_type_title( $person_type_id );
								$person_type_title  = ! ! $person_type_title ? $person_type_title : $person_type['title'];
								$person_type_number = absint( $person_type['number'] );
								$persons[]          = sprintf( '%s %s', $person_type_number, $person_type_title );
							}
							echo implode( ', ', $persons );
						} else {
							echo $booking->get_persons();
						}
						?>
					</div>
				</div>
			<?php endif ?>

			<?php if ( $services = $booking->get_service_names( true ) ) : ?>
				<div class="yith-wcbk-order-related-booking__detail yith-wcbk-order-related-booking__services">
					<div class="yith-wcbk-order-related-booking__field-label"><?php esc_html_e( 'Services', 'yith-booking-for-woocommerce' ); ?></div>
					<div class="yith-wcbk-order-related-booking__field-value"><?php echo yith_wcbk_booking_services_html( $services ); ?></div>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
