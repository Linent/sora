<?php
/**
 * @var string $id
 * @var string $name
 * @var string $value
 * @var array  $options
 * @var string $default_label
 */
$default_label = !empty( $default_label ) ? $default_label : '';

foreach ( $options as $option_key => $option_data ) {
    $default_option_data    = array(
        'class' => 'yith-wcbk-on-off-advanced-' . $value,
        'label' => $default_label,
    );
    $option_data            = wp_parse_args( $option_data, $default_option_data );
    $options[ $option_key ] = $option_data;
}

$name_html = !empty( $id ) ? " name='{$id}'" : '';
$name_html = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html   = !empty( $id ) ? " id='{$id}'" : '';

$onoff_class       = !empty( $options[ $value ][ 'class' ] ) ? $options[ $value ][ 'class' ] : 'yith-wcbk-on-off-advanced-' . $value;
$class_html        = !empty( $class ) ? " class='yith-wcbk-on-off-advanced $onoff_class {$class}'" : "class='yith-wcbk-on-off-advanced $onoff_class'";
$custom_attributes = ' ' . $custom_attributes;

$label = isset( $options[ $value ][ 'label' ] ) ? $options[ $value ][ 'label' ] : $default_label;

$data_html         = '';
$data[ 'options' ] = wp_json_encode( $options );
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
?>
<span <?php echo $id_html . $class_html . $custom_attributes . $data_html ?>>
    <input class="yith-wcbk-on-off-advanced__value" type="hidden" <?php echo $name_html; ?> value="<?php echo $value; ?>"/>
    <span class="yith-wcbk-on-off-advanced__label"><?php echo $label ?></span>
</span>