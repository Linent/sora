<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class             = !empty( $class ) ? $class : '';
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}

$default_time_value = array( '00', '00' );
$time_value         = !empty( $value ) ? explode( ':', $value ) : $default_time_value;
$time_value         = 2 === count( $time_value ) ? $time_value : $default_time_value;

$hour   = $time_value[ 0 ];
$minute = $time_value[ 1 ];

?>
<span class="yith-wcbk-time-select__container <?php echo $class ?>">
    <input type="hidden" class="yith-wcbk-time-select" value="<?php echo $value ?>" <?php echo $id_html . $name_html . $custom_attributes . $data_html; ?>/>
    <select class="yith-wcbk-time-select-hour">
        <?php for ( $i = 0; $i < 24; $i++ ) {
            $option_value = $i < 10 ? '0' . $i : $i;
            echo "<option value='$option_value'" . selected( $hour, $option_value, false ) . ">$option_value</option>";
        } ?>
    </select>
    <select class="yith-wcbk-time-select-minute">
        <?php
        $step = apply_filters( 'yith_wcbk_time_select_edit_booking_minute_step', 15 );
        for ( $i = 0; $i < 60; $i += $step ) {
            $option_value = $i < 10 ? '0' . $i : $i;
            echo "<option value='$option_value'" . selected( $minute, $option_value, false ) . ">$option_value</option>";
        } ?>
    </select>
    <span class="yith-wcbk-time-select__icon"><span class="dashicons dashicons-clock"></span></span>
</span>