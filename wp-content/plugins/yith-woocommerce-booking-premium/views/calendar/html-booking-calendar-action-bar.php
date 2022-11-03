<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( 'day' === $view ) {
    $prev_date = date( 'Y-m-d', strtotime( '-1 day', strtotime( $date ) ) );
    $next_date = date( 'Y-m-d', strtotime( '+1 day', strtotime( $date ) ) );
    $prev_link = add_query_arg( array( 'date' => $prev_date ) );
    $next_link = add_query_arg( array( 'date' => $next_date ) );
} else {
    $next_month = $month + 1;
    $next_year  = $year;
    $prev_month = $month - 1;
    $prev_year  = $year;
    if ( $next_month < 1 ) {
        $next_month = 12;
        $next_year  = $year - 1;
    }
    if( $prev_month < 1 ) {
    	$prev_month = 12;
    	$prev_year = $year -1;
	}
	if ( $next_month > 12 ) {
		$next_month = 1;
		$next_year  = $year + 1;
	}
    $next_link = add_query_arg( array( 'month' => $next_month, 'year' => $next_year, ) );
    $prev_link = add_query_arg( array( 'month' => $prev_month, 'year' => $prev_year, ) );
}

$day_view_url   = $view != 'day' ? add_query_arg( array( 'view' => 'day' ) ) : '#';
$month_view_url = $view != 'month' ? remove_query_arg( 'time_step', add_query_arg( array( 'view' => 'month' ) ) ) : '#';

$product_id = !empty( $_REQUEST[ 'product_id' ] ) ? absint( $_REQUEST[ 'product_id' ] ) : '';

$time_steps = YITH_WCBK_Booking_Calendar::get_time_steps();

?>
<form method="get">
    <?php
    $select2_args = array(
        'class'            => 'yith-booking-product-search',
        'id'               => 'product_id',
        'name'             => 'product_id',
        'data-placeholder' => __( 'Select a booking product...', 'yith-booking-for-woocommerce' ),
        'data-allow_clear' => true,
        'data-multiple'    => false,
        'style'            => 'width:400px',
    );
    if ( $product_id ) {
        $_product       = wc_get_product( $product_id );
        $_product_title = !!$_product ? $_product->get_formatted_name() : sprintf( __( 'Deleted Product #%s', 'yith-booking-for-woocommerce' ), $product_id );

        $select2_args[ 'value' ]         = $product_id;
        $select2_args[ 'data-selected' ] = array( $product_id => $_product_title );
    }
    ?>

    <div id='yith-wcbk-booking-calendar-select-product'>
        <?php yit_add_select2_fields( $select2_args ); ?>
    </div>
    <div id="yith-wcbk-booking-calendar-action-bar">
        <div id="yith-wcbk-booking-calendar-action-bar-left">
            <a href="<?php echo $prev_link; ?>">
                <span class="yith-wcbk-booking-calendar-action yith-wcbk-booking-calendar-action-prev dashicons dashicons-arrow-left-alt2"></span>
            </a>
            <a href="<?php echo $next_link; ?>">
                <span class="yith-wcbk-booking-calendar-action yith-wcbk-booking-calendar-action-next dashicons dashicons-arrow-right-alt2"></span>
            </a>

            <span class="yith-wcbk-booking-calendar-action-current-date">

                <input type="hidden" name="post_type" value="<?php echo YITH_WCBK_Post_Types::$booking ?>"/>
                <input type="hidden" name="page" value="yith-wcbk-booking-calendar"/>
                <input type="hidden" name="view" value="<?php echo $view ?>"/>
                <?php if ( 'day' === $view ) : ?>
                    <input type="text" name="date" class="yith-wcbk-admin-date-picker" value="<?php echo $date ?>"/>
                <?php else : ?>
                    <select name="month">
                        <?php foreach ( yith_wcbk_get_months_array() as $month_id => $month_name ) : ?>
                            <option value="<?php echo $month_id ?>" <?php selected( $month == $month_id ); ?>><?php echo $month_name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="year">
                        <?php for ( $current_year = $year - 10; $current_year < $year + 11; $current_year++ ) : ?>
                            <option value="<?php echo $current_year ?>" <?php selected( $current_year == $year ); ?>><?php echo $current_year ?></option>
                        <?php endfor; ?>
                    </select>
                <?php endif ?>
                <button type="submit">
                    <span id="yith-wcbk-booking-calendar-action-select-date-submit"><?php yith_wcbk_print_svg( 'arrow-right-alt' ) ?></span>
                </button>
        </span>
        </div>

        <div id="yith-wcbk-booking-calendar-action-bar-center">
            <div class="yith-wcbk-booking-calendar-action-bar-change-view__container">
                <div class="yith-wcbk-booking-calendar-action-bar-change-view <?php echo 'month' === $view ? 'current' : '' ?>">
                    <a href="<?php echo $month_view_url ?>"><?php _e( 'Month', 'yith-booking-for-woocommerce' ) ?></a>
                </div>

                <div class="yith-wcbk-booking-calendar-action-bar-change-view <?php echo 'day' === $view ? 'current' : '' ?>">
                    <a href="<?php echo $day_view_url ?>"><?php _e( 'Day', 'yith-booking-for-woocommerce' ) ?></a>
                </div>
            </div>
            <?php if ( 'day' === $view ) : ?>
                <div class="yith-wcbk-booking-calendar-action-bar-change-time-step__container">
                    <?php foreach ( $time_steps as $time_step_key => $time_step_label ) :
                        $time_step_link = add_query_arg( array( 'time_step' => $time_step_key ) );
                        ?>
                        <div class="yith-wcbk-booking-calendar-action-bar-time-step <?php echo $time_step === $time_step_key ? 'current' : '' ?>">
                            <a href="<?php echo $time_step_link ?>"><?php echo $time_step_label ?></a>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php endif ?>
        </div>

        <div id="yith-wcbk-booking-calendar-action-bar-right">
            <input type="text" id="yith-wcbk-booking-calendar-fast-search" placeholder="<?php _e( 'Quick search...', 'yith-booking-for-woocommerce' ); ?>"/>
        </div>
        <div class="clearfix"></div>
    </div>
</form>
