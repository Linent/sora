<?php
/**
 * @var int    $index
 * @var string $name
 * @var string $url
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly
?>
<tr>
    <td>
        <input type="text" name="_yith_booking_external_calendars[<?php echo $index ?>][name]" value="<?php echo $name ?>"/>
    </td>
    <td>
        <input type="text" name="_yith_booking_external_calendars[<?php echo $index ?>][url]" value="<?php echo $url ?>"/>
    </td>
    <td class="yith-wcbk-product-sync-imported-calendars-table__delete-column"><span class="yith-wcbk-icon-trash delete"></span></td>
</tr>
