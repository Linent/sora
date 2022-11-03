<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking' ) ) {
    /**
     * Class YITH_WCBK_Booking
     * the Booking class
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Booking extends YITH_WCBK_Booking_Abstract {
        /** @var WP_Post|bool the post object */
        public $post;

        /**
         * YITH_WCBK_Booking constructor.
         *
         * @param int   $booking the booking id
         * @param array $args    array of meta for creating booking
         */
        public function __construct( $booking = 0, $args = array() ) {
            //the booking if $booking_id is defined
            if ( $booking ) {
                if ( is_numeric( $booking ) ) {
                    $this->id   = absint( $booking );
                    $this->post = get_post( $this->id );
                } elseif ( $booking instanceof YITH_WCBK_Booking ) {
                    $this->id   = absint( $booking->id );
                    $this->post = $booking->post;
                } elseif ( isset( $booking->ID ) ) {
                    $this->id   = absint( $booking->ID );
                    $this->post = $booking;
                }
            }

            //add a new booking if $args is passed
            if ( !$booking && !empty( $args ) ) {
                $this->create_booking( $args );
            }

        }

        /**
         * __get function.
         *
         * @param string $key
         * @return mixed
         */
        public function __get( $key ) {

            if ( 'status' === $key ) {
                $this->status = get_post_status( $this->id );

                return $this->status;
            } elseif ( 'services' === $key ) {
                $this->services = wp_get_post_terms( $this->id, YITH_WCBK_Post_Types::$service_tax, array( 'fields' => 'ids' ) );

                return $this->services;
            }

            $value = get_post_meta( $this->id, '_' . $key, true );

            if ( !empty( $value ) ) {
                $this->$key = $value;
            }

            return $value;
        }

        /**
         * __isset function.
         *
         * @param string $key
         * @return mixed
         */
        public function __isset( $key ) {
            if ( 'status' === $key ) {
                $this->status = get_post_status( $this->id );

                return true;
            } elseif ( 'services' === $key ) {
                $this->services = wp_get_post_terms( $this->id, YITH_WCBK_Post_Types::$service_tax, array( 'fields' => 'ids' ) );

                return true;
            }

            return metadata_exists( 'post', $this->id, '_' . $key );
        }

        /**
         * __set function.
         *
         * @param string $property
         * @param mixed  $value
         * @return bool|int
         */
        public function set( $property, $value ) {
            if ( 'status' === $property ) {
                $this->status     = 'bk-' . $value;
                $update_post_data = array(
                    'ID'          => $this->id,
                    'post_status' => $this->status,
                );

                return wp_update_post( $update_post_data );
            }
            $this->$property = $value;

            return update_post_meta( $this->id, '_' . $property, $value );
        }

        /**
         * return the Booking ID
         *
         * @return int
         */
        public function get_id() {
            return $this->id;
        }

        /**
         * @param        $type
         * @param string $note
         * @return false|int
         */
        public function add_note( $type, $note = '' ) {
            return YITH_WCBK()->notes->add_booking_note( $this->id, $type, $note );
        }

        /**
         * Create new booking
         */
        public function create_booking( $args ) {
            $booking_title = isset( $args[ 'title' ] ) ? $args[ 'title' ] : '';

            $booking_id = wp_insert_post( array(
                                              'post_status' => isset( $args[ 'status' ] ) ? $args[ 'status' ] : 'bk-unpaid',
                                              'post_type'   => YITH_WCBK_Post_Types::$booking,
                                              'post_title'  => $booking_title
                                          ) );


            if ( $booking_id ) {
                $this->id = $booking_id;
                /** @var WC_Product_Booking $product */
                if ( isset( $args[ 'product_id' ] ) && $product = wc_get_product( absint( $args[ 'product_id' ] ) ) ) {
                    $args[ 'can_be_cancelled' ]   = $product->is_cancellation_available();
                    $args[ 'cancelled_duration' ] = $product->get_cancellation_available_up_to();
                    $args[ 'cancelled_unit' ]     = $product->get_cancellation_available_up_to_unit();
                    $args[ 'location' ]           = $product->get_location();
                    $args[ 'all_day' ]            = $product->is_full_day() ? 'yes' : 'no';
                    $args[ 'has_persons' ]        = $product->has_people() ? 'yes' : 'no';

                    $meta = wp_parse_args( $args, $this->get_default_meta_data() );
                    $this->update_booking_meta( $meta );
                    $this->populate();

                    $this->maybe_adjust_all_day_to();

                    $this->add_note( 'new', __( 'Booking successfully created.', 'yith-booking-for-woocommerce' ) );

                    do_action( 'yith_wcbk_booking_created', $this );

                    WC()->mailer();
                    do_action( 'yith_wcbk_new_booking', $booking_id );

                    YITH_WCBK_Cache()->delete_product_data( $args[ 'product_id' ] );

                    // Trigger background update data
                    YITH_WCBK()->background_processes->schedule_product_data_update( $args[ 'product_id' ] );
                }
            }
        }

        /**
         * if the booking is 'all day' adjust the To
         *
         * @since 2.0.4
         */
        public function maybe_adjust_all_day_to() {
            if ( $this->is_all_day() ) {
                $this->to = strtotime( '23:59:59', $this->to );
                $this->set( 'to', $this->to );
            }
        }

        /**
         * get the booking notes
         *
         * @return array|null|object
         */
        public function get_notes() {
            return YITH_WCBK()->notes->get_booking_notes( $this->id );
        }


        /**
         * get the value of the title
         *
         * @return string
         * @since 2.0.5
         */
        public function get_raw_title() {
            return apply_filters( 'yith_wcbk_booking_get_raw_title', $this->title, $this );
        }

        /**
         * Get the title with id
         *
         * @return string
         */
        public function get_title() {
            $title = sprintf( '#%s %s', $this->get_id(), $this->get_raw_title() );

            return apply_filters( 'yith_wcbk_booking_get_title', $title, $this );
        }

        /**
         * Get the service names for current booking
         *
         * @param bool   $show_hidden
         * @param string $type the service type; possible values 'included' | 'additional'. Leave empty to get all services
         * @return array
         */
        public function get_service_names( $show_hidden = true, $type = '' ) {
            $names    = array();
            $services = $this->services;
            if ( $type ) {
                $splitted_services = yith_wcbk_split_services_by_type( $services );
                $services          = isset( $splitted_services[ $type ] ) ? $splitted_services[ $type ] : array();
            }

            if ( !!$services ) {
                foreach ( $services as $service ) {
                    $service = yith_get_booking_service( $service );
                    if ( $service->is_valid() ) {
                        if ( $show_hidden || !$service->is_hidden() )
                            $names[] = $service->get_name_with_quantity( $this->get_service_quantity( $service->id ) );
                    }
                }
            }

            return apply_filters( 'yith_wcbk_booking_get_service_names', $names, $show_hidden, $type, $this );
        }


        /**
         * get the booking duration
         *
         * @return int
         * @since 2.0.4
         */
        public function get_duration() {
            if ( empty( $this->duration ) ) {
                $this->duration = $this->calculate_duration();
            }
            return $this->duration;
        }

        /**
         * calculate duration based on From and To
         *
         * @return int
         * @since 2.0.4
         */
        public function calculate_duration() {
            $date_helper = YITH_WCBK_Date_Helper();
            $duration    = $date_helper->get_time_diff( $this->from, $this->to, $this->duration_unit );
            if ( $this->is_all_day() )
                $duration += 1;

            return $duration;
        }

        /**
         * Get the duration of booking including duration unit
         */
        public function get_duration_html() {
            $duration_html = yith_wcbk_format_duration( $this->get_duration(), $this->duration_unit );
            $duration_html .= $this->is_all_day() ? ' ' . __( '(All Day)', 'yith-booking-for-woocommerce' ) : '';

            return apply_filters( 'yith_wcbk_booking_get_duration_html', $duration_html, $this );
        }

        /**
         * Generates a URL to view a booking from the my account page.
         *
         * @return string
         */
        public function get_view_booking_url() {
            $view_booking_endpoint = YITH_WCBK()->endpoints->get_endpoint( 'view-booking' );

            $view_booking_url = wc_get_endpoint_url( $view_booking_endpoint, $this->id, wc_get_page_permalink( 'myaccount' ) );

            return apply_filters( 'yith_wcbk_get_view_booking_url', $view_booking_url, $this );
        }

        /**
         * Generates a URL to cancel a booking from the my account page.
         *
         * @return string
         */
        public function get_cancel_booking_url() {
            $bookings_endpoint = YITH_WCBK()->endpoints->get_endpoint( 'bookings' );
            $bookings_url      = wc_get_endpoint_url( $bookings_endpoint, '', wc_get_page_permalink( 'myaccount' ) );
            $cancel_url        = add_query_arg( array( 'cancel-booking' => $this->id ), $bookings_url );

            return apply_filters( 'yith_wcbk_get_cancel_booking_url', $cancel_url, $this );
        }


        /**
         * return the mark action url
         *
         * @param string $status
         * @param array  $params
         * @return string
         * @since 2.0.0
         */
        public function get_mark_action_url( $status, $params = array() ) {
            $allowed_statuses = apply_filters( 'yith_wcbk_booking_product_get_mark_action_url_allowed_statuses', array( 'paid', 'confirmed', 'unconfirmed' ), $this );
            $url              = '';
            if ( in_array( $status, $allowed_statuses ) ) {
                $url = "admin-ajax.php?action=yith_wcbk_mark_booking_status&status={$status}&booking_id={$this->get_id()}";
                $url = add_query_arg( $params, $url );
                $url = admin_url( $url );
            }

            return apply_filters( 'yith_wcbk_booking_product_get_mark_action_url', $url, $status, $params, $allowed_statuses, $this );
        }

        /**
         * Generates a URL to pay a booking from the my account page.
         *
         * @return string
         */
        public function get_confirmed_booking_payment_url() {
            $bookings_endpoint = YITH_WCBK()->endpoints->get_endpoint( 'bookings' );
            $bookings_url      = wc_get_endpoint_url( $bookings_endpoint, '', wc_get_page_permalink( 'myaccount' ) );
            $payment_url       = add_query_arg( array( 'pay-confirmed-booking' => $this->id ), $bookings_url );

            return apply_filters( 'yith_wcbk_get_confirmed_booking_payment_url', $payment_url, $this );
        }

        /**
         * return the product ID
         *
         * @return int
         */
        public function get_product_id() {
            return $this->product_id;
        }

        /**
         * return the booking product
         *
         * @return WC_Product_Booking|false
         * @since 2.0.0
         */
        public function get_product() {
            $product = wc_get_product( $this->get_product_id() );

            return $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ? $product : false;
        }

        /**
         * return the service quantities
         *
         * @return mixed
         * @since 2.0.5
         */
        public function get_service_quantities() {
            return $this->service_quantities;
        }

        /**
         * return the service quantity
         *
         * @param bool|int $service_id
         * @return int
         * @since 2.0.5
         */
        public function get_service_quantity( $service_id = false ) {
            $quantities = $this->get_service_quantities();
            return isset( $quantities[ $service_id ] ) ? $quantities[ $service_id ] : 0;
        }

        /**
         * return true if duration unit is hour or minute
         *
         * @return bool
         * @since 2.0.0
         */
        public function has_time() {
            return in_array( $this->duration_unit, array( 'hour', 'minute' ) );
        }

        /**
         * check if the product is all day
         *
         * @return bool
         * @since 2.0.0
         */
        public function is_all_day() {
            return 'yes' === $this->all_day;
        }

        /**
         * Populate the booking
         */
        public function populate() {
            $this->post = get_post( $this->id );

            foreach ( $this->get_booking_meta() as $key => $value ) {
                $this->$key = $value;
            }

            do_action( 'yith_wcbk_booking_loaded', $this );
        }

        /**
         * Check if the booking is valid, controlling if this post exist
         */
        public function is_valid() {
            return !empty( $this->post ) && YITH_WCBK_Post_Types::$booking === $this->post->post_type;
        }

        /**
         * Update post meta in booking
         *
         * @param array $meta the meta
         */
        function update_booking_meta( $meta ) {
            foreach ( $meta as $key => $value ) {
                if ( $key !== 'services' ) {
                    update_post_meta( $this->id, '_' . $key, $value );
                } else {
                    wp_set_post_terms( $this->id, $value, YITH_WCBK_Post_Types::$service_tax );
                }
            }
        }

        /**
         * Fill the default metadata with the post meta stored in db
         *
         * @return array
         */
        function get_booking_meta() {
            $meta = array();
            foreach ( $this->get_default_meta_data() as $key => $value ) {
                if ( $key !== 'services' ) {
                    $meta[ $key ] = get_post_meta( $this->id, '_' . $key, true );
                } else {
                    $meta[ $key ] = wp_get_post_terms( $this->id, YITH_WCBK_Post_Types::$service_tax, array( 'fields' => 'ids' ) );
                }
            }

            return $meta;
        }

        /**
         * Return an array of all custom fields booking
         *
         * @return array
         */
        private function get_default_meta_data() {
            $default_meta_data = array(
                'product_id'                  => 0,
                'title'                       => '',
                'from'                        => '',
                'to'                          => '',
                'duration'                    => 1,
                'duration_unit'               => '',
                'persons'                     => 1,
                'person_types'                => array(),
                'order_id'                    => 0,
                'order_item_id'               => 0,
                'user_id'                     => 0,
                'services'                    => array(),
                'service_quantities'          => array(),
                'can_be_cancelled'            => false,
                'cancelled_duration'          => 0,
                'cancelled_unit'              => 'month',
                'activities'                  => array(),
                'location'                    => '',
                'all_day'                     => 'no',
                'has_persons'                 => '',
                'google_calendar_last_update' => ''

            );

            return $default_meta_data;
        }

        /** -------------------------------------------------
         * CRUD Getters
         */

        /**
         * get the order id
         *
         * @return int
         * @since 2.1.9
         */
        public function get_order_id() {
            $order_id = $this->order_id;
            return !!$order_id ? $order_id : 0;
        }

        /** -------------------------------------------------
         * Non CRUD Getters
         */

        /**
         * get the order
         *
         * @return bool|WC_Order|WC_Order_Refund
         * @since 2.1.9
         */
        public function get_order() {
            return wc_get_order( $this->get_order_id() );
        }

        /**
         * Get the edit link
         *
         * @return string
         */
        public function get_edit_link() {
            return get_edit_post_link( $this->get_id() );
        }

        /**
         * Get the person types HTML
         *
         * @return string
         */
        public function get_person_types_html() {
            $html = '';
            if ( !empty( $this->person_types ) ) {
                foreach ( $this->person_types as $person_type ) {
                    $id     = isset( $person_type[ 'id' ] ) ? $person_type[ 'id' ] : false;
                    $title  = isset( $person_type[ 'title' ] ) ? $person_type[ 'title' ] : false;
                    $number = isset( $person_type[ 'number' ] ) ? $person_type[ 'number' ] : false;

                    if ( $id === false || $title === false || !$number )
                        continue;

                    $person_type_title = get_the_title( $id );
                    $title             = !!$person_type_title ? $person_type_title : $title;
                    $html              .= "<strong>{$title}:</strong> {$number}";
                    $html              .= "<br />";
                }
            }

            return $html;
        }

        /**
         * return the persons
         *
         * @return int
         */
        public function get_persons() {
            return absint( $this->persons );
        }

        /**
         * Return the status
         *
         * @return string
         */
        public function get_status() {
            if ( !isset( $this->status ) )
                $this->status = get_post_status( $this->id );

            return apply_filters( 'yith_wcbk_booking_get_status', 'bk-' === substr( $this->status, 0, 3 ) ? substr( $this->status, 3 ) : $this->status, $this );
        }

        /**
         * Return string for status
         *
         * @return string
         */
        public function get_status_text() {
            $text = strtr( $this->get_status(), yith_wcbk_get_booking_statuses() );

            return $text;
        }

        /**
         * check if booking has person types
         *
         * @return bool
         */
        public function has_person_types() {
            return !empty( $this->person_types );
        }

        /**
         * check if the booking has persons
         *
         * @return bool
         * @since 2.0.0
         */
        public function has_persons() {
            if ( empty( $this->has_persons ) ) {
                // old booking
                $product = $this->get_product();
                if ( $product ) {
                    return $product->has_people();
                } else {
                    return true;
                }
            }

            return 'yes' === $this->has_persons;
        }

        /**
         * check if the booking can change status to $status
         *
         * @param string $status
         * @return bool
         */
        public function can_be( $status ) {

            $value = false;

            switch ( $status ) {
                case 'cancelled_by_user':
                    if ( !!$this->can_be_cancelled && $this->has_status( array( 'unpaid',
                                                                                'paid',
                                                                                'pending-confirm',
                                                                                'confirmed' ) )
                    ) {
                        $now              = strtotime( 'now midnight' );
                        $last_cancel_date = YITH_WCBK_Date_Helper()->get_time_sum( $this->from, - $this->cancelled_duration, $this->cancelled_unit );
                        if ( $now <= $last_cancel_date ) {
                            $value = true;
                        }
                    }
                    break;
                default:
                    $value = true;
            }

            return apply_filters( 'yith_wcbk_booking_can_be_' . $status, $value, $this );
        }

        /**
         * Checks the booking status against a passed in status.
         *
         * @param array|string $status
         * @return bool
         */
        public function has_status( $status ) {
            return apply_filters( 'yith_wcbk_booking_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status ) ) || $this->get_status() === $status ? true : false, $this, $status );
        }

        /**
         * Updates status of booking
         *
         * @param string $new_status
         * @param string $additional_note
         * @param string $deprecated
         */
        public function update_status( $new_status, $additional_note = '', $deprecated = '' ) {
            $additional_note = !!$deprecated ? $deprecated : $additional_note;

            if ( !$this->id ) {
                return;
            }

            // Standardise status names.
            $new_status = 'bk-' === substr( $new_status, 0, 3 ) ? substr( $new_status, 3 ) : $new_status;
            $old_status = $this->get_status();

            $edited_by = '';

            if ( $new_status !== $old_status && ( in_array( $new_status, array_keys( yith_wcbk_get_booking_statuses( true ) ) ) ) ) {

                switch ( $new_status ) {
                    case 'cancelled_by_user' :
                        if ( !$this->can_be( 'cancelled_by_user' ) )
                            return;
                        $this->set( 'status', 'cancelled' );
                        $edited_by = ' ' . __( 'by customer', 'yith-booking-for-woocommerce' );
                        break;
                    default:
                        if ( !$this->can_be( $new_status ) )
                            return;
                        $this->set( 'status', $new_status );
                        break;
                }

                $current_status = $new_status !== 'cancelled_by_user' ? $new_status : 'cancelled';
                $booking_note   = sprintf( __( 'Booking status updated to %s.', 'yith-booking-for-woocommerce' ), yith_wcbk_get_booking_status_name( $current_status ) . $edited_by );
                $booking_note   .= !!$additional_note ? ' ' . $additional_note : '';
                $this->add_note( 'status_changed', $booking_note );

                if ( $this->get_product_id() ) {
                    $booked_statuses = yith_wcbk_get_booked_statuses();
                    $old_is_booked   = in_array( 'bk-' . $old_status, $booked_statuses );
                    $new_is_booked   = in_array( 'bk-' . $new_status, $booked_statuses );
                    // regenerate date only if "booked" status changes
                    if ( $old_is_booked xor $new_is_booked ) {
                        YITH_WCBK_Cache()->delete_product_data( $this->get_product_id() );
                        YITH_WCBK()->background_processes->schedule_product_data_update( $this->get_product_id() );
                    }
                }

                // Status was changed
                do_action( 'yith_wcbk_booking_status_' . $new_status, $this->id );
                do_action( 'yith_wcbk_booking_status_' . $old_status . '_to_' . $new_status, $this->id );
                do_action( 'yith_wcbk_booking_status_changed', $this->id, $old_status, $new_status );
            }
        }
    }
}