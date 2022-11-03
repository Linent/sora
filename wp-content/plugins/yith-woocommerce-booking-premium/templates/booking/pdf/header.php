<?php
/**
 * @var YITH_WCBK_Booking $booking
 * @var bool              $is_admin
 */

$logo = apply_filters( 'yith_wcbk_booking_pdf_logo_url', '' );
?>
<div class="logo">
    <img src="<?php echo $logo ?>">
</div>
<div class="clear"></div>
<div class="booking-title">
    <?php
    $booking_link = $is_admin ? get_edit_post_link( $booking->id ) : $booking->get_view_booking_url();
    ?>

    <h2>
		<a href="<?php echo $booking_link ?>">
            <?php echo $booking->get_name() ?>
        </a>
    </h2>
</div>
