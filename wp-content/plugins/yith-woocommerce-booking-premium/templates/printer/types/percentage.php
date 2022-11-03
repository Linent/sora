<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class_html        = !empty( $class ) ? " class='{$class}'" : '';
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
$value = $value == '' ? null : $value;
?>
<div class="yith-wcbk-printer-percentage__container">
    <span class="yith-wcbk-printer-percentage__icon">%</span>
    <input type="number" <?php echo $id_html . $name_html . $class_html . $custom_attributes . $data_html; ?> value="<?php echo $value; ?>">
</div>