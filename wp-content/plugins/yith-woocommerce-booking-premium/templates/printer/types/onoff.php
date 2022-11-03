<?php
$enabled = $value === 'yes';
$value   = $enabled ? 'yes' : 'no';

$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class             = !empty( $class ) ? $class : '';
$class             .= $enabled ? 'yith-wcbk-printer-field__on-off--enabled' : '';
$class_html        = " class='yith-wcbk-printer-field__on-off {$class}'";
$custom_attributes = ' ' . $custom_attributes;

$data_html = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
?>
<span <?php echo $id_html . $class_html . $custom_attributes . $data_html; ?>>
    <input class="yith-wcbk-printer-field__on-off__value" type="hidden" <?php echo $name_html; ?> value="<?php echo $value; ?>"/>
    <span></span>
</span>