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

wp_enqueue_script( 'yith-wcbk-fields' );
wp_enqueue_style( 'yith-wcbk-fields' );
?>

<span <?php echo $custom_attributes . $data_html; ?> class="yith-wcbk-help-tip help_tip <?php echo $class; ?>" data-tip="<?php echo $value; ?>">
    <?php yith_wcbk_print_svg( 'info' ) ?>
</span>