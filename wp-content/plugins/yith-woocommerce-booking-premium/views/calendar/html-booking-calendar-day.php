<?php
/**
 * Day Calendar page html
 *
 *
 * @var array  $args
 * @var string $view
 * @var string $date
 * @var string $time_step
 * @var string $start_time
 */
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$show_externals = 'yes' === get_option( 'yith-wcbk-external-calendars-show-externals-in-calendar', 'no' );

$product_id = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : false;
$_product   = ! ! $product_id ? wc_get_product( $product_id ) : false;
if ( $_product && ! $_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
	$_product = false;
}

// translate the time step
$strtotime_time_step = str_replace( 'h', ' hours', $time_step );
$strtotime_time_step = str_replace( 'm', ' minutes', $strtotime_time_step );
$strtotime_time_step = '+' . $strtotime_time_step;

if ( $_product ) {
	$daily_start_time = $_product->get_daily_start_time();
	if ( $daily_start_time > $start_time ) {
		$start_time = $daily_start_time;
	}
}
?>

<div id="yith-wcbk-booking-calendar-wrap">
	<?php
	$this->print_action_bar( $args );
	?>
	<table class="yith-wcbk-booking-calendar yith-wcbk-booking-calendar--day-view">
		<thead>
		<tr>
			<th class="yith-wcbk-booking-calendar-day-time"></th>
			<th></th>
		</tr>
		</thead>

		<tbody>
		<?php
		$timestamp     = strtotime( "$date {$start_time}" );
		$end_timestamp = strtotime( "+1 day midnight", $timestamp ) - 1;

		// All Day Bookings
		?>
		<tr>
			<td class="yith-wcbk-booking-calendar-day-time"><?php _e( 'All Day', 'yith-booking-for-woocommerce' ) ?>
				<?php
				if ( $_product && 'day' === $_product->get_duration_unit() ) {
					echo yith_wcbk_get_calendar_product_availability_per_units_html( $_product, $timestamp, $end_timestamp, 'day' );
				}
				?>
			</td>
			<td class="yith-wcbk-booking-calendar-day-container">
				<div class="bookings">
					<?php
					$bookings = YITH_WCBK_Booking_Helper()->get_bookings_in_time_range( $timestamp, $end_timestamp, array( 'month', 'day' ), $show_externals, $product_id );
					include 'html-booking-calendar-booking-list.php';
					?>
				</div>
			</td>
		</tr>
		<?php

		// Hourly Bookings
		$index              = 0;
		while ( $timestamp <= $end_timestamp ) :
			$hour_html = date( 'H:i', $timestamp );
			$next_timestamp = strtotime( $strtotime_time_step, $timestamp );
			$index ++;
			?>
			<tr>
				<td class="yith-wcbk-booking-calendar-day-time"><?php echo $hour_html ?>
					<?php
					/** @var WC_Product_Booking $_product */
					$_step = '1h' === $time_step ? 'hour' : 'minute';
					if ( $_product && $_product->has_time() ) {
						echo yith_wcbk_get_calendar_product_availability_per_units_html( $_product, $timestamp, $next_timestamp - 1, $_step );
					}
					?>
				</td>
				<td class="yith-wcbk-booking-calendar-day-container">
					<div class="bookings">
						<?php
						$bookings = YITH_WCBK_Booking_Helper()->get_bookings_in_time_range( $timestamp, $next_timestamp - 1, array( 'hour', 'minute' ), $show_externals, $product_id );
						include 'html-booking-calendar-booking-list.php';
						?>
					</div>
				</td>
			</tr>
			<?php $timestamp = $next_timestamp; ?>
		<?php endwhile; ?>
		</tbody>
	</table>

</div>
