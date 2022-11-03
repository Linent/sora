<?php
$id    = !empty( $id ) ? $id : '';
$name  = !empty( $name ) ? $name : '';
$class = !empty( $class ) ? $class : '';
$value = !empty( $value ) ? $value : '';
$data  = !empty( $data ) ? $data : array();
?>
<div class="yith-wcbk-date-picker-wrapper yith-wcbk-clearfix">
    <?php
    yith_wcbk_print_field( array(
                               'type'              => 'text',
                               'id'                => $id,
                               'name'              => $name,
                               'class'             => 'yith-wcbk-date-picker ' . $class,
                               'data'              => $data,
                               'value'             => $value,
                               'custom_attributes' => 'readonly'
                           ) );
    ?>

    <?php
    yith_wcbk_print_field( array(
                               'type'              => 'text',
                               'id'                => $id . '--formatted',
                               'class'             => 'yith-wcbk-date-picker--formatted '  . $class,
                               'custom_attributes' => 'readonly'
                           ) );
    ?>
    <span class="yith-wcbk-booking-date-icon"><?php yith_wcbk_print_svg( 'arrow-down-alt' ); ?></span>
</div>