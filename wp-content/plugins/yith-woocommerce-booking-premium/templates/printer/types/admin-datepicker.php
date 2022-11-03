<?php
$id                = !empty( $id ) ? $id : '';
$name              = !empty( $name ) ? $name : '';
$class             = !empty( $class ) ? $class : '';
$class             = 'yith-wcbk-admin-date-picker ' . $class;
$value             = !empty( $value ) ? $value : '';
$data              = !empty( $data ) ? $data : array();
$custom_attributes = !empty( $custom_attributes ) ? $custom_attributes : '';
?>
<span class="yith-wcbk-printer-field__admin-datepicker yith-wcbk-clearfix">
    <?php
    yith_wcbk_print_field( array(
                               'type'              => 'text',
                               'id'                => $id,
                               'name'              => $name,
                               'class'             => $class,
                               'data'              => $data,
                               'value'             => $value,
                               'custom_attributes' => $custom_attributes
                           ) );
    ?>
    <span class="yith-wcbk-printer-field__admin-datepicker__icon"><span class="yith-wcbk-icon yith-wcbk-icon-calendar"></span></span>
</span>