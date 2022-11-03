<?php
/**
 * @var array  $calendars
 * @var array  $options
 * @var string $nonce
 */
!defined( 'YITH_WCBK' ) && exit();
?>
<form method='POST' class='yith-wcbk-google-calendar-options'>
    <table>
        <tr>
            <th><?php _e( 'Select a calendar', 'yith-booking-for-woocommerce' ) ?></th>
            <td><select name='calendar-id'>
                    <option value=''><?php _e( '- Disabled -', 'yith-booking-for-woocommerce' ) ?></option>
                    <?php foreach ( $calendars as $calendar ) {
                        $selected = selected( $calendar->id === $options[ 'calendar-id' ], true, false );
                        echo "<option value='{$calendar->id}' {$selected}>{$calendar->summary}</option>";
                    }
                    ?>
                </select></td>
        </tr>
    </table>

    <input type='hidden' name='yith-wcbk-google-calendar-action' value='save-options'/>
    <input type='submit' class='yith-wcbk-admin-button' value='<?php _e( 'Save Options', 'yith-booking-for-woocommerce' ) ?>'/>
    <?php echo $nonce ?>

</form>