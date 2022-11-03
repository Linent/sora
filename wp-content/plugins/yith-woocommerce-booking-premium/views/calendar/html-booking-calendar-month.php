<?php
/**
 * Month Calendar page html
 *
 * @var array  $args
 * @var string $view
 * @var string $month
 * @var string $year
 * @var int    $start_timestamp
 * @var int    $end_timestamp
 */
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$date_helper    = YITH_WCBK_Date_Helper();
$show_externals = 'yes' === get_option( 'yith-wcbk-external-calendars-show-externals-in-calendar', 'no' );
$product_id     = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : false;
$_product       = ! ! $product_id ? wc_get_product( $product_id ) : false;
if ( $_product && ! $_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
	$_product = false;
}
?>

<div id="yith-wcbk-booking-calendar-wrap">
	<?php
	$this->print_action_bar( $args );
	?>
	<table class="yith-wcbk-booking-calendar yith-wcbk-booking-calendar--month-view">
		<thead>
		<tr>
			<th class="yith-wcbk-booking-calendar-expand-week"></th>
			<?php for ( $index = get_option( 'start_of_week', 1 ); $index < get_option( 'start_of_week', 1 ) + 7; $index ++ ) : ?>
				<th><?php echo date_i18n( 'D', strtotime( "next sunday +{$index} day" ) ); ?></th>
			<?php endfor; ?>
		</tr>
		</thead>

		<tbody>
		<tr>
			<td class="yith-wcbk-booking-calendar-expand-week"></td>
			<?php
			$current_day        = strtotime( 'midnight' );
			$timestamp          = $start_timestamp;
			$index              = 0;
			while ( $timestamp <= $end_timestamp ) :
				$day_class = date( 'n', $timestamp ) != absint( $month ) ? '' : 'current-month';
				$this_day       = date( 'd', $timestamp );
				$single_day_url = add_query_arg( array( 'view' => 'day', 'date' => date( 'Y-m-d', $timestamp ) ) );

				$day_class .= $timestamp === $current_day ? ' today' : '';

				$timestamp_tomorrow = strtotime( '+1 day', $timestamp );
				?>
				<td class="yith-wcbk-booking-calendar-day-container <?php echo $day_class; ?>">
					<div class="yith-wcbk-booking-calendar-day"><a href="<?php echo $single_day_url ?>"><?php echo $this_day; ?></a>
						<?php
						/** @var WC_Product_Booking $_product */
						if ( $_product && 'day' === $_product->get_duration_unit() ) {
							echo yith_wcbk_get_calendar_product_availability_per_units_html( $_product, $timestamp, $timestamp_tomorrow - 1 - 1, 'day' );
						}
						?>
					</div>
					<div class="bookings">
						<?php
						$bookings = YITH_WCBK_Booking_Helper()->get_bookings_in_time_range( $timestamp, $timestamp_tomorrow - 1, 'all', $show_externals, $product_id );
						include 'html-booking-calendar-booking-list.php';
						?>
					</div>
				</td>
				<?php
				$timestamp = strtotime( '+1 day', $timestamp );
				$index ++;

				if ( $index % 7 === 0 && $timestamp <= $end_timestamp ) {
					echo '</tr><tr>';
					echo '<td class="yith-wcbk-booking-calendar-expand-week"></td>';
				}
			endwhile;
			?>
		</tr>
		</tbody>
	</table>

</div>
