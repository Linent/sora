<?php
!defined( 'YITH_WCBK' ) && exit();
$google_calendar = YITH_WCBK_Google_Calendar::get_instance();

if ( isset( $_GET[ 'updated' ] ) ) {
    $message = !!$_GET[ 'updated' ] ? sprintf( __( 'Google Calendar: %s bookings updated!', 'yith-booking-for-woocommerce' ), $_GET[ 'updated' ] ) : __( 'Google Calendar: no booking to update!', 'yith-booking-for-woocommerce' );
    echo "<div id='message' class='updated notice is-dismissible'><p>{$message}</p></div>";
}

?>

<div id="yith-wcbk-settings-tab-wrapper" class="google-calendar yith-plugin-fw">

    <div class="yith-wcbk-settings-section">
        <div class="yith-wcbk-settings-section__title">
            <h3><?php _ex( 'Google Calendar', 'Settings tab title', 'yith-booking-for-woocommerce' ); ?></h3>
        </div>
        <div class="yith-wcbk-settings-section__content">
            <div id="yith-wcbk-google-calendar-tab__left" class="yith-wcbk-settings-content">
                <?php $google_calendar->display() ?>
            </div>

            <div id="yith-wcbk-google-calendar-tab__right" class="yith-wcbk-settings-content">
                <div class="yith-wcbk-settings-section">
                    <div class="yith-wcbk-settings-section__title">
                        <h3><?php _e( 'Settings', 'yith-booking-for-woocommerce' ) ?></h3>
                    </div>
                    <div class="yith-wcbk-settings-section__content">
                        <form method="POST">
                            <input type="hidden" name="yith-wcbk-google-calendar-action" value="save-settings">
                            <?php echo $google_calendar->get_nonce() ?>
                            <table>
                                <tr>
                                    <th>
                                        <?php _e( 'Debug', 'yith-booking-for-woocommerce' ) ?>
                                    </th>
                                    <td>
                                        <?php
                                        yith_plugin_fw_get_field( array(
                                                                      'type'  => 'onoff',
                                                                      'name'  => 'settings[debug]',
                                                                      'value' => $google_calendar->is_debug() ? 'yes' : 'no',
                                                                  ), true, false );
                                        ?>
                                        <span class="description"><?php _e( 'select to enable debug', 'yith-booking-for-woocommerce' ) ?></span>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <?php _e( 'Synchronize', 'yith-booking-for-woocommerce' ) ?>
                                    </th>
                                    <td>
                                        <?php
                                        $synchronize_settings  = array(
                                            'creation'      => __( 'on booking creation', 'yith-booking-for-woocommerce' ),
                                            'update'        => __( 'on booking update', 'yith-booking-for-woocommerce' ),
                                            'status-update' => __( 'on booking status update', 'yith-booking-for-woocommerce' ),
                                            'deletion'      => __( 'on booking deletion', 'yith-booking-for-woocommerce' ),
                                        );
                                        $events_to_synchronize = $google_calendar->get_booking_events_to_synchronize();
                                        ?>

                                        <div id="yith-wcbk-google-calendar-settings-synchronize-booking-events-container">
                                            <?php foreach ( $synchronize_settings as $key => $label ) :
                                                $_id = "yith-wcbk-google-calendar-booking-events-to-synchronize-" . esc_attr( $key );
                                                ?>
                                                <div>
                                                    <input type="checkbox" name="settings[booking-events-to-synchronize][]" id="<?php echo $_id; ?>"
                                                           value="<?php echo esc_attr( $key ) ?>" <?php checked( in_array( $key, $events_to_synchronize ) ) ?>/>
                                                    <label for="<?php echo $_id; ?>"><?php echo $label ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <?php _e( 'Add note on sync', 'yith-booking-for-woocommerce' ) ?>
                                    </th>
                                    <td>
                                        <?php
                                        yith_plugin_fw_get_field( array(
                                                                      'type'  => 'onoff',
                                                                      'name'  => 'settings[add-note-on-sync]',
                                                                      'value' => $google_calendar->is_add_note_on_sync_enabled() ? 'yes' : 'no',
                                                                  ), true, false );
                                        ?>
                                        <span class="description"><?php _e( 'select to enable adding note to bookings on Google Calendar sync', 'yith-booking-for-woocommerce' ) ?></span>
                                    </td>
                                </tr>

                                <tr>
                                    <th colspan="2">
                                        <input type="submit" class="yith-wcbk-admin-button" value="<?php _e( 'Save Settings', 'yith-booking-for-woocommerce' ) ?>">
                                    </th>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>


                <?php if ( $google_calendar && $google_calendar->is_calendar_sync_enabled() ) :
                    $actions = array(
                        array(
                            'title' => __( 'Syncronize not syncronized bookings', 'yith-booking-for-woocommerce' ),
                            'label' => __( 'Sync bookings', 'yith-booking-for-woocommerce' ),
                            'url'   => YITH_WCBK()->google_calendar_sync->get_action_url( 'sync-new-bookings' ),
                            'icon'  => 'update'
                        ),
                        array(
                            'title' => __( 'Syncronize all bookings (Force)', 'yith-booking-for-woocommerce' ),
                            'label' => __( 'Force sync bookings', 'yith-booking-for-woocommerce' ),
                            'url'   => YITH_WCBK()->google_calendar_sync->get_action_url( 'force-sync-all-bookings' ),
                            'icon'  => 'update'
                        )
                    );
                    ?>
                    <div class="yith-wcbk-settings-section">
                        <div class="yith-wcbk-settings-section__title">
                            <h3><?php _e( 'Actions', 'yith-booking-for-woocommerce' ) ?></h3>
                        </div>
                        <div class="yith-wcbk-settings-section__content">
                            <table>
                                <?php
                                foreach ( $actions as $action ) {
                                    extract( $action );
                                    /**
                                     * @var string $title
                                     * @var string $label
                                     * @var string $url
                                     * @var string $icon
                                     */
                                    $extra_class = $icon ? "yith-wcbk-admin-button--icon-{$icon}" : '';
                                    echo "<tr><th>$title</th><td><a href='$url' class='yith-wcbk-admin-button yith-wcbk-admin-button--green {$extra_class}'>$label</a></td></tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
