<?php
 ! defined( 'YITH_WCBK' ) && exit();
$logger = yith_wcbk_logger();

if ( ! empty( $_REQUEST['yith-wcbk-logs-action'] ) ) {
	switch ( $_REQUEST['yith-wcbk-logs-action'] ) {
		case 'delete-logs':
			if ( isset( $_REQUEST['yith-wcbk-logs-nonce'] ) && wp_verify_nonce( $_REQUEST['yith-wcbk-logs-nonce'], 'yith_wcbk_delete_logs' ) ) {
				$logger->delete_logs();
				wp_safe_redirect( remove_query_arg( array( 'yith-wcbk-logs-action', 'yith-wcbk-logs-nonce' ) ) );
				exit;
			}
			break;
	}
}

$order_by_options = array(
	'type'  => __( 'Type', 'yith-booking-for-woocommerce' ),
	'group' => __( 'Group', 'yith-booking-for-woocommerce' ),
	'date'  => __( 'Date', 'yith-booking-for-woocommerce' ),
);

$order_options = array(
	'ASC'  => __( 'Asc', 'yith-booking-for-woocommerce' ),
	'DESC' => __( 'Desc', 'yith-booking-for-woocommerce' ),
);

$default_args = array(
	'order_by' => 'date',
	'order'    => 'DESC',
	'limit'    => 20,
	'paged'    => '1',
);

$request = ! empty( $_REQUEST ) ? $_REQUEST : array();
$request = wp_parse_args( $request, $default_args );

$logs       = $logger->get_logs( $request );
$total_logs = $logger->count_logs( $request );
$groups     = $logger->get_groups();
$types      = $logger->get_types();

$pagination               = new StdClass();
$pagination->totals       = $total_logs;
$pagination->per_page     = $request['limit'];
$pagination->current_page = max( 1, absint( $request['paged'] ) );
$pagination->pages        = ceil( $total_logs / $request['limit'] );

$delete_log_url = add_query_arg(
	array(
		'yith-wcbk-logs-action' => 'delete-logs',
		'yith-wcbk-logs-nonce'  => wp_create_nonce( 'yith_wcbk_delete_logs' ),
	)
);
if ( ! $logger->is_enabled() ) {
	yith_wcbk_print_notice( __( 'Warning: Booking Logger is not enabled', 'yith-booking-for-woocommerce' ), 'warning' );
}
?>
<div id="yith-wcbk-settings-tab-wrapper" class="logs yith-plugin-fw">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php _ex( 'Logs', 'Settings tab title', 'yith-booking-for-woocommerce' ); ?></h3>
			<div id="yith-wcbk-logs-page-actions">
				<a class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--red yith-wcbk-admin-button--icon-trash" href="<?php echo $delete_log_url; ?>"><?php _e( 'Delete Logs', 'yith-booking-for-woocommerce' ); ?></a>
			</div>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div id="yith-wcbk-logs-tab-wrapper">
				<div id="yith-wcbk-logs-tab-actions" class="clearfix">

					<div class="alignleft actions">
						<form method="post">
							<input type="hidden" name="paged" value="1"/>

							<label><?php _ex( 'Limit', 'Label in Logs tab', 'yith-booking-for-woocommerce' ); ?></label><input type="number" min="1" name="limit" value="<?php echo $request['limit']; ?>"/>

							<label><?php _ex( 'Group', 'Label in Logs tab','yith-booking-for-woocommerce' ); ?></label><select name="group">
								<option value=""><?php _e( 'Any', 'yith-booking-for-woocommerce' ); ?></option>
								<?php
								foreach ( $groups as $key ) {
									$name     = YITH_WCBK_Logger_Groups::get_label( $key );
									$selected = selected( ! empty( $request['group'] ) && $request['group'] === $key, true, false );
									echo "<option value='{$key}' {$selected}>{$name}</option>";
								}
								?>
							</select>

							<label><?php _ex( 'Type', 'Label in Logs tab','yith-booking-for-woocommerce' ); ?></label><select name="type">
								<option value=""><?php _e( 'Any', 'yith-booking-for-woocommerce' ); ?></option>
								<?php
								foreach ( $types as $key ) {
									$name     = $key;
									$selected = selected( ! empty( $request['type'] ) && $request['type'] === $key, true, false );
									echo "<option value='{$key}' {$selected}>{$name}</option>";
								}
								?>
							</select>

							<label><?php _ex( 'Order by', 'Label in Logs tab','yith-booking-for-woocommerce' ); ?></label>
							<select name="order_by">
								<?php
								foreach ( $order_by_options as $key => $name ) {
									$selected = selected( $request['order_by'] === $key, true, false );
									echo "<option value='{$key}' {$selected}>{$name}</option>";
								}
								?>
							</select>

							<select name="order">
								<?php
								foreach ( $order_options as $key => $name ) {
									$selected = selected( $request['order'] === $key, true, false );
									echo "<option value='{$key}' {$selected}>{$name}</option>";
								}
								?>
							</select>

							<input type="submit" class="yith-wcbk-admin-button" value="<?php _e( 'Filter', 'yith-booking-for-woocommerce' ); ?>">
						</form>
					</div>

					<div class="alignright actions">
						<form method="post">
					<span class="displaying-num">
						<?php echo sprintf( _n( '%s item', '%s items', 'yith-booking-for-woocommerce' ), $pagination->totals ); ?>
					</span>
							<span class="pagination">
						<?php
						if ( $pagination->pages > 1 ) {
							$first = "<span class='navspan first' aria-hidden='true'>«</span>";
							$prev  = "<span class='navspan prev' aria-hidden='true'>‹</span>";
							$next  = "<span class='navspan next' aria-hidden='true'>›</span>";
							$last  = "<span class='navspan last' aria-hidden='true'>»</span>";
							if ( $pagination->current_page > 1 ) {
								// PREV
								$prev_url = add_query_arg( array_merge( $request, array( 'paged' => $pagination->current_page - 1 ) ) );
								$prev     = "<a href='$prev_url'>$prev</a>";

								$first_url = add_query_arg( array_merge( $request, array( 'paged' => 1 ) ) );
								$first     = "<a href='$first_url'>$first</a>";
							}

							if ( $pagination->current_page < $pagination->pages ) {
								// NEXT
								$prev_url = add_query_arg( array_merge( $request, array( 'paged' => $pagination->current_page + 1 ) ) );
								$next     = "<a href='$prev_url'>$next</a>";

								$last_url = add_query_arg( array_merge( $request, array( 'paged' => $pagination->pages ) ) );
								$last     = "<a href='$last_url'>$last</a>";
							}

							$current  = "<span class='current-page'>";
							$current .= "<input type='text' name='paged' value='{$pagination->current_page}' size='3' />";
							$current .= "<span class='paging-text'> of {$pagination->pages}</span>";
							$current .= '</span>';

							echo $first . $prev . $current . $next . $last;
						}
						?>
					</span>
						</form>
					</div>

					<div class="clear"></div>
				</div>
				<div id="yith-wcbk-logs-tab">
					<table id="yith-wcbk-logs-tab-table" class="widefat striped">
						<thead>
						<tr>
							<th class="type-column"><?php _e( 'Type', 'yith-booking-for-woocommerce' ); ?></th>
							<th class="group-column"><?php _e( 'Group', 'yith-booking-for-woocommerce' ); ?></th>
							<th class="description-column"><?php _e( 'Description', 'yith-booking-for-woocommerce' ); ?></th>
							<th class="date-column"><?php _e( 'Date', 'yith-booking-for-woocommerce' ); ?></th>
						</tr>
						</thead>
						<tbody>

						<?php if ( ! ! $logs ) : ?>
							<?php
							foreach ( $logs as $log ) :
								$group_label = YITH_WCBK_Logger_Groups::get_label( $log->group );
								?>
								<tr>
									<td class="type-column">
										<span class="yith-wcbk-logs-type <?php echo $log->type; ?>"><?php echo $log->type; ?></span>
									</td>
									<td class="group-column">
										<span class="yith-wcbk-logs-group <?php echo $log->group; ?>"><?php echo $group_label; ?></span>
									</td>
									<td class="description-column">
										<?php
										$expand_class = ! ! strstr( $log->description, PHP_EOL ) || strlen( $log->description ) > 100 ? '' : 'disabled';
										?>
										<span class="expand <?php echo $expand_class; ?>"></span>
										<div class="log-description"><?php echo $log->description; ?></div>
									</td>
									<td class="date-column"><?php echo $log->date; ?></td>
								</tr>
							<?php endforeach ?>
						<?php else : ?>
							<tr>
								<td colspan="4"><?php _e( 'No result', 'yith-booking-for-woocommerce' ); ?></td>
							</tr>
						<?php endif; ?>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
