<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$name_html         = isset( $name ) && $name === false ? "" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class_html        = !empty( $class ) ? " class='{$class}'" : '';
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}

if ( !$value && $options ) {
    $value = current( array_keys( $options ) );
}
?>
<div class="yith-wcbk-printer-field__select-inline <?php echo $class ?>" <?php echo $custom_attributes . $data_html; ?>>
    <?php foreach ( $options as $option_value => $option_title ) :
        $selected_class = $option_value === $value ? 'yith-wcbk-printer-field__select-inline__option--selected' : '';
        ?>
        <div class="yith-wcbk-printer-field__select-inline__option <?php echo $selected_class ?>" data-value="<?php echo $option_value; ?>"><?php echo $option_title; ?></div>
    <?php endforeach; ?>

    <input type="hidden" <?php echo $id_html . $name_html ?> value="<?php echo $value ?>"/>
</div>