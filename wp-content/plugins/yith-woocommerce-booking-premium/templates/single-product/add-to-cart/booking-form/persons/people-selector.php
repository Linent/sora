<?php
/**
 * Booking form persons - people selector
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var array              $person_types
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$min_persons = $product->get_minimum_number_of_people();
$max_persons = $product->get_maximum_number_of_people();


$people_selector_id = 'yith-wcbk-people-selector-' . $product->get_id();
$data               = "data-min='{$min_persons}'";
$data               .= $max_persons > 0 ? " data-max='{$max_persons}'" : '';
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-people-selector">
    <label class='yith-wcbk-booking-form__label' for="<?php echo $people_selector_id ?>"><?php echo yith_wcbk_get_label( 'people' ) ?></label>
    <div id="<?php echo $people_selector_id ?>" class="yith-wcbk-people-selector" <?php echo $data ?>>
        <div class="yith-wcbk-people-selector__toggle-handler">
            <span class="yith-wcbk-people-selector__totals"></span>
            <span class="yith-wcbk-people-selector__toggle-handler__icon">
                <?php yith_wcbk_print_svg( 'arrow-down-alt' ); ?>
            </span>
        </div>
        <div class="yith-wcbk-people-selector__fields-container">
            <?php foreach ( $person_types as $person_type ) :
                /**
                 * @var int  $id
                 * @var bool $enabled
                 * @var int  $min
                 * @var int  $max
                 */
                extract( $person_type );
                $default_person_number = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'person_type_' . $id );
                $min                   = max( 0, $min );
                $title                 = YITH_WCBK()->person_type_helper->get_person_type_title( $id );
                $value                 = max( $min, $default_person_number );

                $data = "data-min='{$min}'";
                $data .= $max > 0 ? " data-max='{$max}'" : '';
                $data .= $value ? " data-value='{$value}'" : '';
                ?>
                <div id="yith-wcbk-booking-persons-type-<?php echo $id ?>" class="yith-wcbk-people-selector__field yith-wcbk-clearfix" <?php echo $data ?>>
                    <div class="yith-wcbk-people-selector__field__title"><?php echo $title ?></div>
                    <div class="yith-wcbk-people-selector__field__totals">
                        <span class="yith-wcbk-people-selector__field__minus">
                            <span class="yith-wcbk-people-selector__field__minus-wrap">
                                <?php yith_wcbk_print_svg( 'minus' ); ?>
                            </span>
                        </span>
                        <span class="yith-wcbk-people-selector__field__total"></span>
                        <span class="yith-wcbk-people-selector__field__plus">
                            <span class="yith-wcbk-people-selector__field__plus-wrap">
                                <?php yith_wcbk_print_svg( 'plus' ); ?>
                            </span>
                        </span>
                    </div>

                    <input type="hidden" name="person_types[<?php echo $id ?>]" class="yith-wcbk-people-selector__field__value yith-wcbk-booking-person-types" data-person-type-id="<?php echo $id ?>" value="<?php echo $value ?>"/>
                </div>
            <?php endforeach; ?>
            <div class="yith-wcbk-people-selector__fields-container__footer yith-wcbk-clearfix">
                <span class="yith-wcbk-people-selector__close-handler"><?php _e( 'Close', 'yith-booking-for-woocommerce' ) ?></span>
            </div>
        </div>
    </div>
</div>