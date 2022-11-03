<?php
/**
 * Create booking page html
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */
if ( !defined( 'YITH_WCBK' ) ) {
    exit;
} // Exit if accessed directly

$assign_order_default = apply_filters( 'yith_wcbk_create_booking_assign_order_default', 'no' );

?>

<div id="yith-wcbk-create-booking-page-wrap">
    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
    <h2><?php _e( 'Create Booking', 'yith-booking-for-woocommerce' ); ?></h2>

    <form method="POST">
        <table class="form-table">
            <tbody>
            <?php do_action( 'yith_wcbk_before_create_booking_page' ); ?>
            <tr valign="top">
                <th scope="row">
                    <label for="user_id"><?php _e( 'User', 'yith-booking-for-woocommerce' ); ?></label>
                </th>
                <td>
                    <?php
                    yit_add_select2_fields( array(
                                                'class'            => 'wc-customer-search',
                                                'id'               => 'user_id',
                                                'name'             => 'user_id',
                                                'data-placeholder' => __( 'Guest', 'yith-booking-for-woocommerce' ),
                                                'data-allow_clear' => true,
                                                'data-multiple'    => false,
                                                'style'            => 'width:400px',
                                            ) );
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="product_id"><?php _e( 'Product', 'yith-booking-for-woocommerce' ); ?></label>
                </th>
                <td>
                    <?php
                    yit_add_select2_fields( array(
                                                'class'            => 'yith-booking-product-search',
                                                'id'               => 'product_id',
                                                'name'             => 'product_id',
                                                'data-placeholder' => __( 'Select a booking product...', 'yith-booking-for-woocommerce' ),
                                                'data-allow_clear' => true,
                                                'data-multiple'    => false,
                                                'style'            => 'width:400px',
                                            ) );
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e( 'Order', 'yith-booking-for-woocommerce' ); ?></label>
                </th>
                <td>
                    <p>
                        <input type="radio" id="yith-wcbk-no-order" name="order" value="no" class="checkbox" <?php checked( 'no', $assign_order_default ) ?>/>
                        <label for="yith-wcbk-no-order">
                            <?php _e( 'Don\'t assign this booking to any order.', 'yith-booking-for-woocommerce' ); ?>
                        </label>
                    </p>

                    <p>
                        <input type="radio" id="yith-wcbk-assign-order" name="order" value="yes" class="checkbox" <?php checked( 'yes', $assign_order_default ) ?>/>
                        <label for="yith-wcbk-assign-order">
                            <?php _e( 'Assign this booking to an order:', 'yith-booking-for-woocommerce' ); ?>
                        </label>
                    </p>
                    <p>
                        <?php
                        yit_add_select2_fields( array(
                                                    'class'            => 'yith-wcbk-order-search',
                                                    'id'               => 'order_id',
                                                    'name'             => 'order_id',
                                                    'data-placeholder' => __( 'Create new order', 'yith-booking-for-woocommerce' ),
                                                    'data-allow_clear' => true,
                                                    'data-multiple'    => false,
                                                    'style'            => 'width:400px',
                                                ) );
                        ?>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e( 'Details', 'yith-booking-for-woocommerce' ); ?></label>
                </th>
                <td>
                    <div id="yith-wcbk-create-booking-form-wrap" class="product">
                        <?php _e( 'Select a booking product to see details', 'yith-booking-for-woocommerce' ); ?>
                    </div>
                </td>
            </tr>
            <?php do_action( 'yith_wcbk_after_create_booking_page' ); ?>
            <tr valign="top">
                <th scope="row">&nbsp;</th>
                <td>
                    <button type="submit" name="create_booking" disabled class="yith-wcbk-create-booking"><?php _e( 'Create Booking', 'yith-booking-for-woocommerce' ); ?></button>
                    <?php wp_nonce_field( 'create-booking' ); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>