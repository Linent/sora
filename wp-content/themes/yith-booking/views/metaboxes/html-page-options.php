<?php
/**
 * @var WP_Post $post
 */

$hide_title = yith_booking_get_page_option( $post->ID, 'hide_title' ) === 'yes';
?>
<div>
    <label>
        <input type="checkbox" name="_yith_bk_th_page_option_hide_title" <?php checked( $hide_title ) ?> value="yes"/>
        <span><?php _e( 'Hide title', 'yith-booking' ) ?></span>
    </label>
</div>