<?php
/**
 * @var int                  $index
 * @var string               $field_name
 * @var YITH_WCBK_Price_Rule $price_rule
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly


$default_toggle_class = is_numeric( $index ) ? 'yith-wcbk-settings-section-box--closed' : '';

$_field_name      = "{$field_name}[{$index}]";
$_field_id_prefix = "{$field_name}-id--{$index}__";

$rule_name  = $price_rule->get_name( 'edit' );
$conditions = $price_rule->get_conditions( 'edit' );

$condition_defaults = array( 'type' => 'custom', 'from' => '', 'to' => '' );
if ( !$conditions || !is_array( $conditions ) || count( $conditions ) < 1 ) {
    $conditions = array( $condition_defaults );
}

$operators = array( 'add' => '+', 'sub' => '-', 'mul' => '*', 'div' => '/', 'set-to' => '=', 'add-percentage' => '+%', 'sub-percentage' => '-%' );

$person_type_ids     = YITH_WCBK()->person_type_helper->get_person_type_ids();
$person_type_options = array();
if ( !!$person_type_ids ) {
    foreach ( $person_type_ids as $person_type_id ) {
        $option_id                         = 'person-type-' . $person_type_id;
        $person_type_options[ $option_id ] = YITH_WCBK()->person_type_helper->get_person_type_title( $person_type_id );
    }
}

$range_types = array(
    'custom' => __( 'Custom date range', 'yith-booking-for-woocommerce' ),
    'month'  => __( 'Range of months', 'yith-booking-for-woocommerce' ),
    'week'   => __( 'Range of weeks', 'yith-booking-for-woocommerce' ),
    'day'    => __( 'Range of days', 'yith-booking-for-woocommerce' ),
    'person' => __( 'People count', 'yith-booking-for-woocommerce' ),
    'block'  => __( 'Unit count', 'yith-booking-for-woocommerce' ),
    'time'   => __( 'Time range', 'yith-booking-for-woocommerce' ),
);

if ( !!$person_type_options ) {
    $range_types[ 'group-person' ] = array(
        'title'   => __( 'People', 'yith-booking-for-woocommerce' ),
        'options' => $person_type_options
    );
}
?>
<div class="yith-wcbk-settings-section-box yith-wcbk-price-rule <?php echo $default_toggle_class ?>" data-index="<?php echo $index ?>">
    <?php yith_wcbk_print_field( array( 'type' => 'hidden', 'class' => 'yith-wcbk-settings-section-box__sortable-position', 'name' => $_field_name . '[position]', 'value' => $index ) ); ?>
    <div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
        <h3><?php echo $rule_name ? $rule_name : __( 'Untitled', 'yith-booking-for-woocommerce' ) ?></h3>
        <span class="yith-wcbk-settings-section-box__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
        <span class="yith-wcbk-settings-section-box__enabled"><?php yith_wcbk_print_field( array(
                                                                                               'type'  => 'onoff',
                                                                                               'name'  => $_field_name . '[enabled]',
                                                                                               'value' => $price_rule->get_enabled()
                                                                                           ) );
            ?></span>
    </div>
    <div class="yith-wcbk-settings-section-box__content">
        <?php
        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'Rule name', 'yith-booking-for-woocommerce' ),
                                                  'class'  => 'yith-wcbk-settings-section-box__edit-title',
                                                  'fields' => array(
                                                      array(
                                                          'type'  => 'text',
                                                          'class' => 'yith-wcbk-price-rule__title-field',
                                                          'value' => $rule_name,
                                                          'name'  => $_field_name . '[name]'
                                                      )
                                                  )
                                              ) );

        $conditions_html = '';
        $condition_index = 1;
        foreach ( $conditions as $condition ) {
            $condition = wp_parse_args( $condition, $condition_defaults );
            ob_start();
            yith_wcbk_get_view( 'product-tabs/utility/html-price-rule-condition.php', array(
                'condition'                 => $condition,
                'index'                     => $index,
                'condition_index'           => $condition_index,
                'condition_type'            => $condition[ 'type' ],
                'condition_from'            => $condition[ 'from' ],
                'condition_to'              => $condition[ 'to' ],
                'condition_field_name'      => $_field_name . '[conditions][' . $condition_index . ']',
                'condition_field_id_prefix' => $_field_id_prefix . "condition-{$condition_index}__",
            ) );
            $conditions_html .= ob_get_clean();

            if ( 1 === $condition_index ) {
                $conditions_html .= "<div class='yith-wcbk-settings-section__content__actions'>
                <span class='yith-wcbk-admin-action-link yith-wcbk-price-rule__conditions__new-condition'>+ " .
                                            __( 'Add new condition', 'yith-booking-for-woocommerce' ) . "</span>
                </div>";
            }

            $condition_index++;
        }


        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'Conditions', 'yith-booking-for-woocommerce' ),
                                                  'fields' => array(
                                                      'type'   => 'section',
                                                      'class'  => 'yith-wcbk-price-rule__conditions',
                                                      'fields' => array(
                                                          array(
                                                              'type'   => 'section',
                                                              'class'  => 'yith-wcbk-price-rule__conditions__list',
                                                              'fields' => array(
                                                                  array(
                                                                      'type'  => 'html',
                                                                      'value' => $conditions_html,
                                                                  )
                                                              ) )
                                                      )
                                                  )
                                              ) );

        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => sprintf( __( 'Base price for %s', 'yith-booking-for-woocommerce' ), yith_wcbk_product_metabox_dynamic_duration() ),
                                                  'class'  => 'yith_booking_multi_fields',
                                                  'fields' => array(
                                                      array(
                                                          'type'    => 'select',
                                                          'name'    => $_field_name . '[base_price_operator]',
                                                          'options' => $operators,
                                                          'value'   => $price_rule->get_base_price_operator( 'edit' ),
                                                      ),
                                                      array(
                                                          'type'  => 'text',
                                                          'class' => 'yith-wcbk-mini-field',
                                                          'name'  => $_field_name . '[base_price]',
                                                          'value' => $price_rule->get_base_price( 'edit' ),
                                                      )
                                                  )
                                              ) );

        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
                                                  'class'  => 'yith_booking_multi_fields',
                                                  'fields' => array(
                                                      array(
                                                          'type'    => 'select',
                                                          'name'    => $_field_name . '[base_fee_operator]',
                                                          'options' => $operators,
                                                          'value'   => $price_rule->get_base_fee_operator( 'edit' ),
                                                      ),
                                                      array(
                                                          'type'  => 'text',
                                                          'class' => 'yith-wcbk-mini-field',
                                                          'name'  => $_field_name . '[base_fee]',
                                                          'value' => $price_rule->get_base_fee( 'edit' ),
                                                      )
                                                  )
                                              ) );
        ?>
        <div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
            <?php if ( !empty( $add_button ) ): ?>
                <span class="yith-wcbk-admin-button yith-wcbk-admin-button--primary yith-wcbk-admin-button--icon-check yith-wcbk-price-rules__add-rule"><?php _e( 'Add rule', 'yith-booking-for-woocommerce' ) ?></span>
            <?php endif; ?>
            <span class="yith-wcbk-admin-button yith-wcbk-admin-button--light-grey yith-wcbk-admin-button--icon-trash yith-wcbk-price-rules__delete-rule"><?php _e( 'Delete rule', 'yith-booking-for-woocommerce' ) ?></span>
        </div>
    </div>
</div>