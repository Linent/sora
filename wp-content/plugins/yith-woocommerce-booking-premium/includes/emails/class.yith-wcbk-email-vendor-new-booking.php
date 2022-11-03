<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Email_Vendor_New_Booking' ) ) :

    /**
     * Class YITH_WCBK_Email_Vendor_New_Booking
     *
     * An email sent to the vendor when a new booking is created
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since 1.0.8
     */
    class YITH_WCBK_Email_Vendor_New_Booking extends YITH_WCBK_Email {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id          = 'yith_wcbk_vendor_new_booking';
            $this->title       = __( 'New Booking (Vendor)', 'yith-booking-for-woocommerce' );
            $this->description = __( 'This email is sent to the vendor when a booking is created.', 'yith-booking-for-woocommerce' );
            $this->heading     = __( 'New Booking', 'yith-booking-for-woocommerce' );
            $this->subject     = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );

            $this->template_base  = YITH_WCBK_TEMPLATE_PATH;
            $this->template_html  = 'emails/vendor-new-booking.php';
            $this->template_plain = 'emails/plain/vendor-new-booking.php';

            // Triggers for this email
            add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

            // Call parent constructor
            parent::__construct();

            // Other settings
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

                        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                    }
                }
            }
        }
    }

endif;

return new YITH_WCBK_Email_Vendor_New_Booking();
