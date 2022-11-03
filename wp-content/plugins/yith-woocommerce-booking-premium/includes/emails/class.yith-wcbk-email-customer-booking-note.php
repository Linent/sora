<?php
!defined( 'ABSPATH' ) && exit;

if ( !class_exists( 'YITH_WCBK_Email_Customer_Booking_Note' ) ) :

    /**
     * Class YITH_WCBK_Email_Customer_Booking_Note
     *
     * An email sent to the customer when a new customer note is created
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since 2.0.0
     */
    class YITH_WCBK_Email_Customer_Booking_Note extends YITH_WCBK_Email {

        /** @var string */
        public $note = '';

        public function __construct() {
            $this->id             = 'yith_wcbk_customer_booking_note';
            $this->title          = __( 'Customer Booking Note', 'yith-booking-for-woocommerce' );
            $this->description    = __( 'Customer Booking Note emails are sent when you add a note to a booking.', 'yith-booking-for-woocommerce' );
            $this->heading        = __( 'A note has been added to your Booking #{booking_id}', 'yith-booking-for-woocommerce' );
            $this->subject        = __( 'Note added to your Booking #{booking_id}', 'yith-booking-for-woocommerce' );
            $this->customer_email = true;

            $this->template_base  = YITH_WCBK_TEMPLATE_PATH;
            $this->template_html  = 'emails/customer-booking-note.php';
            $this->template_plain = 'emails/plain/customer-booking-note.php';

            // Triggers for this email
            add_action( 'yith_wcbk_new_customer_note', array( $this, 'trigger' ) );

            // Call parent constructor
            parent::__construct();
        }

        /**
         * Trigger.
         *
         * @param array $params
         */
        public function trigger( $params ) {
            $defaults = array(
                'booking_id' => '',
                'note'       => ''
            );
            $params   = wp_parse_args( $params, $defaults );

            if ( !!$params[ 'booking_id' ] && !!$params[ 'note' ] ) {
                $this->object = yith_get_booking( $params[ 'booking_id' ] );
                $this->note   = $params[ 'note' ];

                if ( $this->object->is_valid() ) {
                    $this->find[ 'booking-id' ]        = '{booking_id}';
                    $this->find[ 'booking-status' ]    = '{status}';
                    $this->replace[ 'booking-id' ]     = absint( $params[ 'booking_id' ] );
                    $this->replace[ 'booking-status' ] = $this->object->get_status_text();

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
         * extra content params
         *
         * @return array
         */
        public function get_extra_content_params() {
            return array(
                'note' => $this->note
            );
        }
    }

endif;

return new YITH_WCBK_Email_Customer_Booking_Note();
