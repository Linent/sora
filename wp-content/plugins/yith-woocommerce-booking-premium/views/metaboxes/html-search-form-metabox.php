<?php
/**
 * Search Form Metabox
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */

!defined( 'YITH_WCBK' ) && exit();

$search_form = yith_wcbk_get_search_form( $post->ID );
$fields      = $search_form->get_fields();

$field_titles = array(
    'search'     => __( 'Search', 'yith-booking-for-woocommerce' ),
    'location'   => __( 'Location', 'yith-booking-for-woocommerce' ),
    'categories' => __( 'Categories', 'yith-booking-for-woocommerce' ),
    'tags'       => __( 'Tags', 'yith-booking-for-woocommerce' ),
    'date'       => __( 'Dates', 'yith-booking-for-woocommerce' ),
    'persons'    => __( 'People', 'yith-booking-for-woocommerce' ),
    'services'   => __( 'Services', 'yith-booking-for-woocommerce' ),
);

$enabled_class     = 'yith-wcbk-enabled';
$not_enabled_class = 'yith-wcbk-disabled';

$field_with_options = array( 'persons', 'search' );

?>
<div class="yith-wcbk-admin-search-form-wrapper">
    <div class="yith-wcbk-admin-search-form-fields" data-enabled-class="<?php echo $enabled_class ?>" data-not-enabled-class="<?php echo $not_enabled_class ?>">

        <?php foreach ( $fields as $field_key => $field_data ) : ?>
            <?php
            $field_enabled_class = $field_data[ 'enabled' ] === 'yes' ? $enabled_class : $not_enabled_class;
            ?>
            <div class="yith-wcbk-admin-search-form-field">
                <input type="hidden" id="yith-wcbk-admin-search-form-field-enabled-<?php echo $field_key ?>" class="yith-wcbk-admin-search-form-field-enabled" name="_yith_wcbk_admin_search_form_fields[<?php echo $field_key ?>][enabled]" value="<?php echo $field_data[ 'enabled' ] ?>"/>
                <div class="yith-wcbk-admin-search-form-field-title">
                    <span class="dashicons dashicons-menu"></span>
                    <span class="yith-wcbk-admin-search-form-field-enable dashicons dashicons-visibility <?php echo $field_enabled_class ?>"></span>
                    <?php if ( in_array( $field_key, $field_with_options ) ): ?>
                        <span class="yith-wcbk-admin-search-form-field-toggle dashicons dashicons-arrow-down"></span>
                    <?php endif ?>
                    <h2><?php echo strtr( $field_key, $field_titles ); ?></h2>
                </div>
                <?php if ( in_array( $field_key, $field_with_options ) ): ?>
                    <div class="yith-wcbk-admin-search-form-field-content hidden">
                        <?php if ( $field_key === 'persons' ): ?>

                            <div class="yith-wcbk-admin-search-form-field-row">
                                <input type="radio" class="yith-wcbk-admin-search-form-field-persons-type" id="yith-wcbk-admin-search-form-field-type-persons" name="_yith_wcbk_admin_search_form_fields[persons][type]" value="persons" <?php checked( $field_data[ 'type' ] === 'persons' ) ?>/>
                                <label for="yith-wcbk-admin-search-form-field-type-persons"><?php _e( 'People field', 'yith-booking-for-woocommerce' ) ?></label>
                            </div>
                            <div class="yith-wcbk-admin-search-form-field-row">
                                <input type="radio" class="yith-wcbk-admin-search-form-field-persons-type" id="yith-wcbk-admin-search-form-field-type-person-types" name="_yith_wcbk_admin_search_form_fields[persons][type]"
                                       value="person-types" <?php checked( $field_data[ 'type' ] === 'person-types' ) ?>/>
                                <label for="yith-wcbk-admin-search-form-field-type-person-types"><?php _e( 'People type field', 'yith-booking-for-woocommerce' ) ?></label>
                            </div>
                        <?php elseif ( $field_key === 'search' ): ?>
                            <div class="yith-wcbk-admin-search-form-field-row">
                                <label for="yith-wcbk-admin-search-form-field-search-label"><?php _e( 'Label', 'yith-booking-for-woocommerce' ) ?></label>
                                <input type="text" id="yith-wcbk-admin-search-form-field-search-label" name="_yith_wcbk_admin_search_form_fields[search][label]" value=""/>
                            </div>
                        <?php endif ?>
                    </div>
                <?php endif ?>
            </div>

        <?php endforeach; ?>
    </div>
    <div class="yith-wcbk-admin-search-form-clear"></div>
</div>