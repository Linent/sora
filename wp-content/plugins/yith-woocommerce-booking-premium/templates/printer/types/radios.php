<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$name_html         = isset( $name ) && $name === false ? "" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class_html        = !empty( $class ) ? " class='{$class}'" : '';
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
$multiple          = !empty( $multiple );
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
?>

<span class="yith-wcbk-printer-field__radios" <?php echo $id_html . $data_html; ?>>
    <?php foreach ( $options as $option_value => $option_title ):
        $radio_id = '';
        if ( !empty( $id ) ) {
            $radio_id = $id;
        } elseif ( !empty( $name ) ) {
            $radio_id = $name ;

        }
        $radio_id .= '-' . sanitize_key( $option_value );
        ?>
        <input type="radio" id="<?php echo $radio_id ?>" <?php echo $name_html . $class_html . $custom_attributes . ' ' . checked( $value === $option_value, true, false ) ?> value="<?php echo $option_value ?>">
        <label for="<?php echo $radio_id ?>"><?php echo $option_title ?></label>
    <?php endforeach; ?>
</span>