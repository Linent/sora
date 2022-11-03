<?php

$id                = ! empty( $id ) ? $id : '';
$name              = ! empty( $name ) ? $name : '';
$class             = ! empty( $class ) ? $class : '';
$value             = ! empty( $value ) ? $value : '';
$custom_attributes = ! empty( $custom_attributes ) ? $custom_attributes : '';
$value             = ! empty( $value ) ? $value : '';
$data              = ! empty( $data ) ? $data : array();

$hidden_form_field_id     = $id . '--hidden-form-field';
$data['update-on-change'] = '#' . $hidden_form_field_id;
$data['value']            = $value;

$data_html = '';
foreach ( $data as $data_key => $data_value ) {
	$data_html .= " data-{$data_key}='{$data_value}'";
}

?>
<div class="yith-wcbk-date-picker-inline-wrapper yith-wcbk-clearfix">
	<div id="<?php echo esc_attr( $id ); ?>"
			class="yith-wcbk-date-picker yith-wcbk-date-picker--inline <?php echo esc_attr( $class ); ?>"
		<?php echo $custom_attributes . $data_html; ?>
	></div>

	<input id="<?php echo esc_attr( $hidden_form_field_id ); ?>"
			type="hidden"
			name="<?php echo esc_attr( $name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
	/>
</div>