<?php
/**
 * @var array $notices
 */
!defined( 'YITH_WCBK' ) && exit();
?>
<div class='yith-wcbk-google-calendar-notices'>
    <?php foreach ( $notices as $notice ) {
        $text = $notice[ 'text' ];
        $type = $notice[ 'type' ];
        echo "<span class='yith-wcbk-google-calendar-notice $type'>$text</span>";
    }
    ?>
</div>