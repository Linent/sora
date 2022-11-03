<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Email_Booking_Status_Vendor' ) ) :

    /**
     * Class YITH_WCBK_Email_Booking_Status_Vendor
     *
     * An email sent to the vendor when a new booking changes status
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Email_Booking_Status_Vendor extends YITH_WCBK_Email {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id          = 'yith_wcbk_booking_status_vendor';
            $this->title       = __( 'Booking status (Vendor)', 'yith-booking-for-woocommerce' );
            $this->description = __( 'This email is sent to the vendor when a booking\'s status changes.',
                                     'yith-booking-for-woocommerce' );
            $this->heading     = __( 'Booking status changed', 'yith-booking-for-woocommerce' );
            $this->subject     = __( 'Booking #{booking_id} is now {status}', 'yith-booking-for-woocommerce' );
            $this->reply_to    = '';

            $this->template_base  = YITH_WCBK_TEMPLATE_PATH;
            $this->template_html  = 'emails/admin-booking-status-vendor.php';
            $this->template_plain = 'emails/plain/admin-booking-status-vendor.php';

            // Triggers for this email
            foreach ( array_keys( yith_wcbk_get_booking_statuses( true ) ) as $status ) {
                add_action( 'yith_wcbk_booking_status_' . $status . '_notification', array( $this, 'trigger' ) );
            }

            // Call parent constructor
            parent::__construct();

            $this->recipient = YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );
        }

        /**
         * Trigger.
         *
         * @param int $booking_id
         */
        public function trigger( $booking_id ) {
            if ( $booking_id ) {
                $vendor = yith_get_vendor( $booking_id, 'product' );
                if ( $vendor->is_valid() ) {
                    $vendor_email = $vendor->store_email;

                    if ( empty( $vendor_email ) ) {
                        $vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
                        $vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
                    }

                    $this->recipient = $vendor_email;

                    $this->object = yith_get_booking( $booking_id );
                    if ( $this->object->is_valid() ) {
                        $this->find[ 'booking-id' ]        = '{booking_id}';
                        $this->find[ 'booking-status' ]    = '{status}';
                        $this->replace[ 'booking-id' ]     = absint( $booking_id );
                        $this->replace[ 'booking-status' ] = $this->object->get_status_text();

                        if ( !$this->is_enabled() || !$this->get_recipient() ) {
                            return;
                        }

                        if ( !is_array( $this->get_option( 'status' ) ) || !$this->object->has_status( $this->get_option( 'status' ) ) ) {
                            return;
                        }

                        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                    }
                }
            }
        }

        /**
         * Initialise settings form fields.
         */
        public function init_form_fields() {
            parent::init_form_fields();
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

return new YITH_WCBK_Email_Booking_Status_Vendor();
