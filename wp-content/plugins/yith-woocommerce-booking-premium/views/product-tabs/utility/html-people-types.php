<?php
/**
 * Template options in WC Product Panel
 *
 * @var array $people_types The product people types
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$all_people_type_ids = YITH_WCBK()->person_type_helper->get_person_type_ids();
$people_type_ids     = array_keys( $people_types );
$all_people_type_ids = array_unique( array_merge( $people_type_ids, $all_people_type_ids ) );

?>
<div class="yith-wcbk-people-types yith-wcbk-settings-section bk_show_if_people_and_people_types">
    <div class="yith-wcbk-settings-section__title">
        <h3><?php _e( 'People types', 'yith-booking-for-woocommerce' ) ?></h3>
        <div class="yith-wcbk-people-types__expand-collapse">
            <span class="yith-wcbk-people-types__expand"><?php _e( 'Expand all', 'yith-booking-for-woocommerce' ) ?></span>
            <span class="yith-wcbk-people-types__collapse"><?php _e( 'Collapse all', 'yith-booking-for-woocommerce' ) ?></span>
        </div>
    </div>
    <div class="yith-wcbk-settings-section__content">
        <div id="yith-wcbk-people-types__list" class="yith-wcbk-settings-section-box__sortable-container">
            <?php

            foreach ( $all_people_type_ids as $people_type_id ) {
                $people_type = isset( $people_types[ $people_type_id ] ) ? $people_types[ $people_type_id ] : array( 'id' => $people_type_id );

                yith_wcbk_get_view( 'product-tabs/utility/html-people-type.php', compact( 'people_type', 'people_type_id' ) );
            }

            ?>
        </div>
        <?php if ( current_user_can( 'edit_' . YITH_WCBK_Post_Types::$person_type . 's' ) && current_user_can( 'create_' . YITH_WCBK_Post_Types::$person_type . 's' ) ): ?>
            <?php if ( apply_filters( 'yith_wcbk_allow_creating_people_types_in_product_edit_page', false ) ): ?>
                <div class="yith-wcbk-settings-section__content__actions">

                    <span id="yith-wcbk-people-types__create-btn" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-plus"><?php _e( 'Create new type', 'yith-booking-for-woocommerce' ) ?></span>


                    <div id="yith-wcbk-people-types__create" class="yith-wcbk-settings-section-box yith-wcbk-settings-section-box--no-toggle">
                        <div class="yith-wcbk-settings-section-box__title">
                            <h3><?php _e( 'Create new type', 'yith-booking-for-woocommerce' ) ?></h3>
                        </div>
                        <div class="yith-wcbk-settings-section-box__content">

                            <?php
                            yith_wcbk_product_metabox_form_field( array(
                                                                      'title'  => __( 'People type', 'yith-booking-for-woocommerce' ),
                                                                      'fields' => array(
                                                                          array(
                                                                              'type'  => 'text',
                                                                              'value' => '',
                                                                              'class' => 'yith-wcbk-fake-form-field',
                                                                              'data'  => array(
                                                                                  'name'     => 'title',
                                                                                  'required' => 'yes',
                                                                              )
                                                                          )
                                                                      )
                                                                  ) );


                            ?>

                            <input type="hidden" class="yith-wcbk-fake-form-field" data-name="security" value="<?php echo wp_create_nonce( 'create-people-type' ) ?>">

                            <div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
                                <span class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-check yith-wcbk-people-types__create-submit"><?php _e( 'Create', 'yith-booking-for-woocommerce' ) ?></span>
                            </div>
                        </div>
                    </div>

                    <script type="text/html" id="tmpl-yith-wcbk-people-type-row">
                        <?php
                        $people_type_id = "{{ data.id }}";
                        $people_type    = array( 'id' => $people_type_id, 'enabled' => 'yes', 'title' => "{{ data.title }}" );
                        yith_wcbk_get_view( 'product-tabs/utility/html-people-type.php', compact( 'people_type', 'people_type_id' ) );
                        ?>
                    </script>
                </div>
            <?php else: ?>
                <div class="yith-wcbk-form-field__description"><?php echo sprintf( __( 'You can create people types in %sBookings > People%s', 'yith-booking-for-woocommerce' ), '<a href="' . admin_url( 'edit.php?post_type=ywcbk-person-type' ) . '">', '</a>' ); ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>