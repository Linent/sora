<?php
/**
 * Bookings
 * Shows booking on the account page.
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/bookings.php.
 *
 * @var YITH_WCBK_Booking[] $bookings
 * @var bool                $has_bookings
 * @var int                 $max_num_pages
 * @var int                 $current_page
 * @var int                 $total
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$account_bookings_columns = array(
	'booking-id'      => __( 'Booking', 'yith-booking-for-woocommerce' ),
	'booking-order'   => __( 'Order', 'yith-booking-for-woocommerce' ),
	'booking-from'    => __( 'From', 'yith-booking-for-woocommerce' ),
	'booking-to'      => __( 'To', 'yith-booking-for-woocommerce' ),
	'booking-status'  => __( 'Status', 'yith-booking-for-woocommerce' ),
	'booking-actions' => '&nbsp;',

);
$bookings_endpoint = apply_filters( 'yith_wcbk_endpoint_bookings','bookings' );
$endpoint = YITH_WCBK()->endpoints->get_endpoint( $bookings_endpoint );
?>

<?php do_action( 'yith_wcbk_before_account_bookings', $has_bookings ); ?>

<?php if ( $has_bookings ) : ?>

	<?php do_action( 'yith_wcbk_show_bookings_table', $bookings ); ?>

	<?php do_action( 'yith_wcbk_before_account_bookings_pagination' ); ?>

	<?php if ( 1 < $max_num_pages ) : ?>
        <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) :
				$prev_url = wc_get_endpoint_url( $endpoint, $current_page - 1 );
				?>
                <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( $prev_url ); ?>"><?php esc_html_e( 'Previous', 'yith-booking-for-woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $max_num_pages ) !== $current_page ) :
				$next_url = wc_get_endpoint_url( $endpoint, $current_page + 1 );
				?>
                <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( $next_url ); ?>"><?php esc_html_e( 'Next', 'yith-booking-for-woocommerce' ); ?></a>
			<?php endif; ?>
        </div>
	<?php endif; ?>

<?php else : ?>
    <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
        <a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Go to the Shop', 'yith-booking-for-woocommerce' ) ?>
        </a>
		<?php _e( 'No booking has been made yet.', 'yith-booking-for-woocommerce' ); ?>
    </div>
<?php endif; ?>

<?php do_action( 'yith_wcbk_after_account_bookings', $has_bookings ); ?>

