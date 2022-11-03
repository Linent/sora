<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Email_Booking_Status' ) ) :

    /**
     * Class YITH_WCBK_Email_Booking_Status
     *
     * An email sent to the admin when a new booking changes status
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Email_Booking_Status extends YITH_WCBK_Email {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id          = 'yith_wcbk_booking_status';
            $this->title       = __( 'Booking status', 'yith-booking-for-woocommerce' );
            $this->description = __( 'This email is sent to the administrator when a booking\'s status changes.',
                                     'yith-booking-for-woocommerce' );
            $this->heading     = __( 'Booking status changed', 'yith-booking-for-woocommerce' );
            $this->subject     = __( 'Booking #{booking_id} is now {status}', 'yith-booking-for-woocommerce' );
            $this->reply_to    = '';

            $this->template_base  = YITH_WCBK_TEMPLATE_PATH;
            $this->template_html  = 'emails/admin-booking-status.php';
            $this->template_plain = 'emails/plain/admin-booking-status.php';

            // Triggers for this email
            $statuses = $this->get_option( 'status' );
            $statuses = is_array( $statuses ) ? $statuses : array();
            foreach ( $statuses as $status ) {
                add_action( 'yith_wcbk_booking_status_' . $status . '_notification', array( $this, 'trigger' ) );
            }

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

        /**
         * Initialise settings form fields.
         */
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

            yith_wcbk_array_add_before( $this->form_fields, 'email_type', 'status', array(
                'title'       => __( 'Send email for these statuses', 'yith-booking-for-woocommerce' ),
                'type'        => 'multiselect',
                'description' => __( 'Choose on which status(es) this email notification should be sent.',
                                     'yith-booking-for-woocommerce' ),
                'default'     => array( 'unpaid', 'cancelled_by_user', 'pending-confirm' ),
                'class'       => 'email_type wc-enhanced-select',
                'options'     => yith_wcbk_get_booking_statuses( true ),
                'desc_tip'    => true
            ) );
        }
    }

endif;

return new YITH_WCBK_Email_Booking_Status();
