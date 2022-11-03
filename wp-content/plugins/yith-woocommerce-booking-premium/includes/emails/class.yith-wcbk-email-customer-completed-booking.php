<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Email_Customer_Completed_Booking' ) ) :

    /**
     * Class YITH_WCBK_Email_Customer_Completed_Booking
     *
     * An email sent to the customer when a new booking is completed
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  2.0.6
     */
    class YITH_WCBK_Email_Customer_Completed_Booking extends YITH_WCBK_Email {

        /** @var string */
        public $custom_message = '';

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id             = 'yith_wcbk_customer_completed_booking';
            $this->title          = __( 'Completed Booking', 'yith-booking-for-woocommerce' );
            $this->description    = __( 'This email is sent to customers when a booking is completed.', 'yith-booking-for-woocommerce' );
            $this->heading        = __( 'Thanks for your Booking', 'yith-booking-for-woocommerce' );
            $this->subject        = __( 'Booking #{booking_id} was completed', 'yith-booking-for-woocommerce' );
            $this->custom_message = $this->get_option( 'custom_message', __( 'Booking #{booking_id} was completed', 'yith-booking-for-woocommerce' ) );
            $this->customer_email = true;

            $this->placeholders = array(
                '{booking_id}' => '',
                '{status}'     => '',
            );

            $this->template_base  = YITH_WCBK_TEMPLATE_PATH;
            $this->template_html  = 'emails/customer-completed-booking.php';
            $this->template_plain = 'emails/plain/customer-completed-booking.php';

            // Triggers for this email
            add_action( 'yith_wcbk_booking_status_completed_notification', array( $this, 'trigger' ) );

            // Call parent constructor
            parent::__construct();

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
                    $this->placeholders[ '{booking_id}' ] = absint( $booking_id );
                    $this->placeholders[ '{status}' ]     = $this->object->get_status_text();


                    if ( $this->object->order_id && $order = wc_get_order( $this->object->order_id ) ) {
                        $this->recipient = yit_get_prop( $order, 'billing_email', true );
                    } elseif ( $this->object->user_id && $user_data = get_userdata( $this->object->user_id ) ) {
                        $this->recipient = $user_data->user_email;
                    }

                    if ( !$this->is_enabled() || !$this->get_recipient() ) {
                        return;
                    }

                    $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                }
            }
        }


        /**
         * init form fields
         */
        public function init_form_fields() {
            parent::init_form_fields();
            yith_wcbk_array_add_after( $this->form_fields, 'heading', 'custom_message', array(
                'title'       => __( 'Custom Message', 'yith-booking-for-woocommerce' ),
                'type'        => 'textarea',
                'placeholder' => __( 'Booking #{booking_id} was completed', 'yith-booking-for-woocommerce' ),
                'default'     => '',
                'description' => sprintf( __( 'Available placeholders: %s', 'yith-booking-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
                'desc_tip'    => true
            ) );

            $this->form_fields[ 'email_type' ][ 'default' ] = 'html';
        }

        /**
         * extra content params
         *
         * @return array
         */
        public function get_extra_content_params() {
            return array(
                'custom_message' => nl2br( $this->format_string( $this->custom_message ) )
            );
        }
    }

endif;

return new YITH_WCBK_Email_Customer_Completed_Booking();
