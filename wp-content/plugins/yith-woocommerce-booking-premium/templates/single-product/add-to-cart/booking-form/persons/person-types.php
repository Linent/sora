<?php
/**
 * Booking form persons - person types
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var array              $person_types
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly
foreach ( $person_types as $person_type ) :
    /**
     * @var int  $id
     * @var bool $enabled
     * @var int  $min
     * @var int  $max
     */
    extract( $person_type );
    $default_person_number = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'person_type_' . $id );
    $min                   = max( 0, $min );
    ?>
    <div class="yith-wcbk-form-section yith-wcbk-form-section-person-types">
        <label class='yith-wcbk-booking-form__label'><?php echo YITH_WCBK()->person_type_helper->get_person_type_title( $id ) ?></label>
        <?php
        $custom_attributes = "step='1' min='{$min}'";
        $custom_attributes .= $max > 0 ? " max='{$max}'" : '';

        yith_wcbk_print_field( array(
                                   'type'              => 'number',
                                   'id'                => 'yith-wcbk-booking-persons-type-' . $id,
                                   'name'              => "person_types[$id]",
                                   'data'              => array(
                                       'person-type-id' => $id,
                                   ),
                                   'custom_attributes' => $custom_attributes,
                                   'value'             => max( $min, $default_person_number ),
                                   'class'             => 'yith-wcbk-booking-person-types yith-wcbk-number-minifield',
                               ) );
        ?>
    </div>
<?php endforeach; ?>