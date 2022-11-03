<?php
/**
 * @var array $calendars
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly
?>
<table class="yith-wcbk-product-sync-imported-calendars-table">
    <thead>
    <tr>
        <th><?php _e( 'Name', 'yith-booking-for-woocommerce' ) ?></th>
        <th><?php _e( 'URL', 'yith-booking-for-woocommerce' ) ?></th>
        <th class="yith-wcbk-product-sync-imported-calendars-table__delete-column"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $index = 1;
    foreach ( $calendars as $calendar ) {
        $name = $calendar[ 'name' ];
        $url  = $calendar[ 'url' ];
        yith_wcbk_get_view( 'product-tabs/utility/html-imported-calendar-row.php', compact( 'index', 'name', 'url' ) );
        $index++;
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3">
            <a href="#" class="yith-wcbk-admin-button yith-wcbk-admin-button--dark yith-wcbk-admin-button--icon-plus insert" data-row="<?php
            $name  = '';
            $url   = '';
            $index = '{{INDEX}}';
            ob_start();
            yith_wcbk_get_view( 'product-tabs/utility/html-imported-calendar-row.php', compact( 'index', 'name', 'url' ) );
            echo esc_attr( ob_get_clean() );
            ?>"><?php _e( 'Add ICS calendar', 'yith-booking-for-woocommerce' ); ?></a>
        </th>
    </tr>
    </tfoot>
</table>
