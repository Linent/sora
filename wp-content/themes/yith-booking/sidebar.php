<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package YITH_Booking
 */

if ( ! yith_booking_has_sidebar() ) {
	return;
}
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">
	<?php yith_booking_print_sidebar() ?>
</aside><!-- .sidebar .widget-area -->
