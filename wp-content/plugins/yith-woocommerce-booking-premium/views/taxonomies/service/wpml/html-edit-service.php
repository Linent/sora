<?php
/**
 * @var YITH_WCBK_Service $service the booking service
 */

?>

    <tr class="form-field">
        <td scope="row" valign="top">
            <h3><?php echo __( 'WPML translations', 'yith-booking-for-woocommerce' ); ?></h3>
        </td>
    </tr>

<?php
$fields = array();

foreach ( $languages as $language_code => $language ) {
    $language_name = isset( $language[ 'display_name' ] ) ? $language[ 'display_name' ] : $language_code;
    $name          = "yith_booking_service_data[wpml_translated_name][{$language_code}]";
    $value         = isset( $service->wpml_translated_name[ $language_code ] ) ? $service->wpml_translated_name[ $language_code ] : '';
    ?>

    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="yith_booking_service_wpml_translated_name<?php echo $language_code; ?>"><?php echo sprintf( __( 'Name (%s)', 'yith-booking-for-woocommerce' ), $language_name ) ?></label>
        </th>
        <td>
            <input type="text" name="<?php echo $name; ?>"
                   id="yith_booking_service_wpml_translated_name_<?php echo $language_code; ?>"
                   value="<?php echo $value ?>"/>
        </td>
    </tr>


    <?php
}
?>