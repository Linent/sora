<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Email_Admin_New_Booking' ) ) :
    /**
     * Class YITH_WCBK_Email_Admin_New_Booking
     *
     * An email sent to the admin when a booking is created
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since 1.0.8
     */
    class YITH_WCBK_Email_Admin_New_Booking extends YITH_WCBK_Email {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id          = 'yith_wcbk_admin_new_booking';
            $this->title       = __( 'New Booking (Admin)', 'yith-booking-for-woocommerce' );
            $this->description = __( 'This email is sent to the admin when a booking is created.', 'yith-booking-for-woocommerce' );
            $this->heading     = __( 'New Booking', 'yith-booking-for-woocommerce' );
            $this->subject     = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );

            $this->template_base  = YITH_WCBK_TEMPLATE_PATH;
            $this->template_html  = 'emails/admin-new-booking.php';
            $this->template_plain = 'emails/plain/admin-new-booking.php';

            // Triggers for this email
            add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

            // Call parent constructor
            parent::__construct();

            // Other settings
            $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
        }


        /**
         * Trigger.
         *
         * @param int $booking_id
         */
        public function trigger( $booking_id ) {
            if ( $booking_id ) {
                $this->object = yith_get_booking( $booking_id );
                if ( $this->object->is_valid() ) {
                    $this->find[ 'booking-id' ]        = '{booking_id}';
                    $this->find[ 'booking-status' ]    = '{status}';
                    $this->replace[ 'booking-id' ]     = absint( $booking_id );
                    $this->replace[ 'booking-status' ] = $this->object->get_status_text();

                    if ( !$this->is_enabled() || !$this->get_recipient() ) {
                        return;
                    }
                    $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                }
            }
        }

        public function init_form_fields() {
            parent::init_form_fields();
            yith_wcbk_array_add_after( $this->form_fields, 'enabled', 'recipient', array(
                'title'       => __( 'Recipient(s)', 'yith-booking-for-woocommerce' ),
                'type'        => 'text',
                'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'yith-booking-for-woocommerce' ), esc_attr( get_option( 'admin_email' ) ) ),
                'placeholder' => '',
                'default'     => '',
                'desc_tip'    => true
            ) );
        }
    }

endif;

return new YITH_WCBK_Email_Admin_New_Booking();
