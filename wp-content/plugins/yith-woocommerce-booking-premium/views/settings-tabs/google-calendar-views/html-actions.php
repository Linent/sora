<?php
/**
 * @var array                     $actions
 * @var YITH_WCBK_Google_Calendar $google_calendar
 */
!defined( 'YITH_WCBK' ) && exit();
?>
<div class='yith-wcbk-google-calendar-actions__container'>
    <div class='yith-wcbk-google-calendar-actions'>
        <?php
        foreach ( $actions as $action ) {
            echo $google_calendar->get_view( $action . '-form.php', array( 'nonce' => $google_calendar->get_nonce() ) );
        }
        ?>
    </div>
</div>