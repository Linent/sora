<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Google_Calendar' ) ) {
    /**
     * Class YITH_WCBK_Google_Calendar
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Google_Calendar {
        private $redirect_uri = '';
        private $scopes       = 'https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/userinfo.profile';
        private $oauth_url    = 'https://accounts.google.com/o/oauth2/';

        /** @var string */
        private $views_path;

        private $options = array();

        /** @var YITH_WCBK_Google_Calendar */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Google_Calendar
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Google_Calendar constructor.
         */
        private function __construct() {
            $this->views_path = YITH_WCBK_VIEWS_PATH . 'settings-tabs/google-calendar-views/';

            $query_args         = array( 'page' => 'yith_wcbk_panel', 'tab' => 'google-calendar', 'yith-wcbk-google-auth' => '1', );
            $this->redirect_uri = add_query_arg( $query_args, admin_url( 'admin.php' ) );

            if ( is_admin() ) {
                $this->handle_actions();
            }
        }

        /**
         * Display the Views for options and access
         */
        public function display() {
            $actions = array();
            $html    = '';
            if ( !$this->get_client_id() || !$this->get_client_secret() ) {
                $html .= $this->get_client_secret_form_view();
            } else {
                $this->oauth_access();

                if ( $this->is_connected() ) {
                    $html .= $this->get_profile_details_view();

                    $html .= $this->get_timezone_info_view();

                    $html .= $this->get_options_form_view();

                    $actions[] = 'logout';
                } else {
                    $html .= $this->get_access_form_view();
                }

                $actions[] = 'delete-secret';
            }

            if ( !!$actions ) {
                $args = array(
                    'actions'         => $actions,
                    'google_calendar' => $this,
                );

                $html .= $this->get_view( 'actions.php', $args );
            }

            echo $html;
        }

        /**
         * Handle the actions of Google Calendar panel
         */
        public function handle_actions() {
            if ( !empty( $_REQUEST[ 'yith-wcbk-google-calendar-action' ] ) && $this->check_nonce() ) {
                $credentials_options = array( 'client-id', 'client-secret' );;
                switch ( $_REQUEST[ 'yith-wcbk-google-calendar-action' ] ) {
                    case 'save-options':
                        $options = array( 'calendar-id' );
                        foreach ( $options as $option ) {
                            $value = !empty( $_REQUEST[ $option ] ) ? $_REQUEST[ $option ] : '';
                            $this->set_option( $option, $value );
                        }
                        break;
                    case 'save-settings':
                        $settings         = !empty( $_REQUEST[ 'settings' ] ) ? $_REQUEST[ 'settings' ] : array();
                        $default_settings = array(
                            'debug'                         => 'no',
                            'add-note-on-sync'              => 'no',
                            'booking-events-to-synchronize' => array(),
                        );
                        $settings         = wp_parse_args( $settings, $default_settings );
                        foreach ( $settings as $option => $value ) {
                            $this->set_option( $option, $value );
                        }
                        break;
                    case 'save-credentials':
                        foreach ( $credentials_options as $option ) {
                            $value = !empty( $_REQUEST[ $option ] ) ? $_REQUEST[ $option ] : '';
                            $this->set_option( $option, $value );
                        }
                        break;

                    case 'delete-client-secret':
                        foreach ( $credentials_options as $option ) {
                            $this->set_option( $option, '' );
                        }
                        break;

                    case 'logout':
                        $this->set_option( 'access-token', '' );
                        $this->set_option( 'refresh-token', '' );
                        delete_transient( 'yith-wcbk-gcal-access-token' );
                        break;
                }
            }
        }

        /**
         * oAuth Access
         * on Google response
         */
        public function oauth_access() {
            if ( !empty( $_GET[ 'yith-wcbk-google-auth' ] ) && !empty( $_GET[ 'code' ] ) ) {
                $code = $_GET[ 'code' ];
                $data = array(
                    'code'          => $code,
                    'client_id'     => $this->get_client_id(),
                    'client_secret' => $this->get_client_secret(),
                    'redirect_uri'  => $this->redirect_uri,
                    'grant_type'    => 'authorization_code',
                );

                $params = array(
                    'body'      => http_build_query( $data ),
                    'sslverify' => false,
                    'timeout'   => 60,
                    'headers'   => array(
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ),
                );

                $response = wp_remote_post( $this->oauth_url . 'token', $params );

                if ( !is_wp_error( $response ) && 200 == $response[ 'response' ][ 'code' ] && 'OK' == $response[ 'response' ][ 'message' ] ) {
                    $body         = json_decode( $response[ 'body' ] );
                    $access_token = sanitize_text_field( $body->access_token );
                    $expires_in   = isset( $body->expires_in ) ? absint( $body->expires_in ) : HOUR_IN_SECONDS;
                    $expires_in   -= 100;

                    $this->set_option( 'refresh-token', $body->refresh_token );

                    set_transient( 'yith-wcbk-gcal-access-token', $access_token, $expires_in );

                    $this->debug( 'Access Token generated successfully', compact( 'access_token', 'expires_in', 'body' ) );

                    $redirect_url = add_query_arg( array( 'page' => 'yith_wcbk_panel', 'tab' => 'google-calendar', ), admin_url( 'admin.php' ) );
                    wp_safe_redirect( $redirect_url );
                    exit();
                } else {
                    $this->error( 'Error while generating Access Token: ', $response );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Getters - Setters
        |--------------------------------------------------------------------------
        */

        /**
         * get the option
         *
         * @param string $key
         * @param bool   $default
         * @return mixed
         */
        public function get_option( $key, $default = false ) {
            if ( !array_key_exists( $key, $this->options ) ) {
                $this->options[ $key ] = get_option( 'yith-wcbk-google-calendar-option-' . $key, $default );
            }

            return $this->options[ $key ];
        }

        /**
         * set the option
         *
         * @param string $key
         * @param mixed  $value
         */
        public function set_option( $key, $value ) {
            $this->options[ $key ] = $value;
            update_option( 'yith-wcbk-google-calendar-option-' . $key, $value );
        }

        /**
         * get the Google API Access Token
         *
         * @return mixed|string
         */
        public function get_access_token() {
            $access_token = get_transient( 'yith-wcbk-gcal-access-token' );
            if ( !$access_token ) {
                $refresh_token = $this->get_option( 'refresh-token' );
                if ( $refresh_token ) {
                    $data = array(
                        'client_id'     => $this->get_client_id(),
                        'client_secret' => $this->get_client_secret(),
                        'refresh_token' => $refresh_token,
                        'grant_type'    => 'refresh_token',
                    );

                    $params = array(
                        'body'      => http_build_query( $data ),
                        'sslverify' => false,
                        'timeout'   => 60,
                        'headers'   => array(
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ),
                    );

                    $response = wp_remote_post( $this->oauth_url . 'token', $params );

                    if ( !is_wp_error( $response ) && 200 == $response[ 'response' ][ 'code' ] && 'OK' == $response[ 'response' ][ 'message' ] ) {
                        $body         = json_decode( $response[ 'body' ] );
                        $access_token = sanitize_text_field( $body->access_token );
                        $expires_in   = isset( $body->expires_in ) ? absint( $body->expires_in ) : HOUR_IN_SECONDS;
                        $expires_in   -= 100;

                        set_transient( 'yith-wcbk-gcal-access-token', $access_token, $expires_in );

                        $this->debug( 'Access Token refreshed successfully', compact( 'access_token', 'expires_in', 'body' ) );
                    } else {
                        $this->error( 'Error while refreshing Access Token: ', $response );
                    }
                }
            }

            return $access_token;
        }

        /**
         * retrieve the calendar id
         *
         * @return string
         */
        public function get_calendar_id() {
            return $this->get_option( 'calendar-id', '' );
        }

        /**
         * retrieve the client id
         *
         * @return string
         */
        public function get_client_id() {
            return $this->get_option( 'client-id', '' );
        }

        /**
         * retrieve the calendar secret
         *
         * @return string
         */
        public function get_client_secret() {
            return $this->get_option( 'client-secret', '' );
        }

        /**
         * @return array
         */
        public function get_booking_events_to_synchronize() {
            $events = $this->get_option( 'booking-events-to-synchronize', array( 'creation', 'update', 'status-update', 'deletion' ) );
            if ( !$events ) {
                $events = array();
            }

            return $events;
        }

        /**
         * retrieve the Calendar list from Google Calendar
         *
         * @return array
         */
        public function get_calendar_list() {
            $calendars = array();
            $uri       = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
            $params    = array(
                'sslverify' => false,
                'timeout'   => 60,
                'headers'   => array(
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->get_access_token(),
                ),
            );

            $response = wp_remote_get( $uri, $params );

            if ( !is_wp_error( $response ) && 200 == $response[ 'response' ][ 'code' ] && 'OK' == $response[ 'response' ][ 'message' ] ) {
                $body      = json_decode( $response[ 'body' ] );
                $calendars = $body->items;

                $this->debug( 'Calendar List retrieved successfully', $body );
            } else {
                $this->error( 'Error while retrieving Calendar List: ', $response );
            }

            return $calendars;
        }

        /**
         * retrieve the timezone info of the Google Calendar set
         *
         * @return string
         */
        public function get_timezone_info() {
            $info = '';
            if ( $this->is_calendar_sync_enabled() ) {
                $uri    = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->get_calendar_id();
                $params = array(
                    'sslverify' => false,
                    'timeout'   => 60,
                    'headers'   => array(
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $this->get_access_token(),
                    ),
                );

                $response = wp_remote_get( $uri, $params );

                if ( !is_wp_error( $response ) && 200 == $response[ 'response' ][ 'code' ] && 'OK' == $response[ 'response' ][ 'message' ] ) {
                    $body = json_decode( $response[ 'body' ] );
                    $info = $body->timeZone;

                    $this->debug( 'TimeZone info retrieved successfully', $body );
                } else {
                    $this->error( 'Error while retrieving timezone information: ', $response );
                }
            }

            return $info;
        }


        /*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        */

        /**
         * return true if is connected
         *
         * @return bool
         */
        public function is_connected() {
            return !!$this->get_access_token();
        }

        /**
         * return true is the calendar sync is enabled
         *
         * @return bool
         */
        public function is_calendar_sync_enabled() {
            return $this->is_connected() && $this->get_calendar_id();
        }

        public function is_debug() {
            return 'yes' === $this->get_option( 'debug', 'no' );
        }

        public function is_add_note_on_sync_enabled() {
            return 'yes' === $this->get_option( 'add-note-on-sync', 'yes' );
        }

        public function is_synchronize_on_creation_enabled() {
            return in_array( 'creation', $this->get_booking_events_to_synchronize() );
        }

        public function is_synchronize_on_update_enabled() {
            return in_array( 'update', $this->get_booking_events_to_synchronize() );
        }

        public function is_synchronize_on_status_update_enabled() {
            return in_array( 'status-update', $this->get_booking_events_to_synchronize() );
        }

        public function is_synchronize_on_deletion_enabled() {
            return in_array( 'deletion', $this->get_booking_events_to_synchronize() );
        }

        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        /**
         * get the view
         *
         * @param string $view
         * @param array  $args
         * @return string
         */
        public function get_view( $view, $args = array() ) {
            extract( $args );
            $path = $this->views_path . 'html-' . $view;

            ob_start();

            if ( file_exists( $path ) ) {
                include $path;
            }

            return ob_get_clean();
        }

        /**
         * get the Client Secret Form view
         *
         * @return string
         */
        public function get_client_secret_form_view() {
            $args = array(
                'client_id'     => $this->get_client_id(),
                'client_secret' => $this->get_client_secret(),
                'redirect_uri'  => $this->redirect_uri,
                'nonce'         => $this->get_nonce()
            );

            return $this->get_view( 'client-secret-form.php', $args );
        }

        /**
         * get the Access Form view
         *
         * @return string
         */
        public function get_access_form_view() {
            $auth_url = add_query_arg(
                array(
                    'scope'           => $this->scopes,
                    'redirect_uri'    => urlencode( $this->redirect_uri ),
                    'response_type'   => 'code',
                    'client_id'       => $this->get_client_id(),
                    'approval_prompt' => 'force',
                    'access_type'     => 'offline',
                ),
                $this->oauth_url . 'auth'
            );

            return $this->get_view( 'access-form.php', array( 'auth_url' => $auth_url, 'redirect_uri' => $this->redirect_uri ) );
        }

        /**
         * get the Options Form view
         *
         * @return string
         */
        public function get_options_form_view() {
            $options = array(
                'calendar-id' => ''
            );

            foreach ( $options as $option => $default ) {
                $options[ $option ] = $this->get_option( $option, $default );
            }


            $args = array(
                'options'   => $options,
                'calendars' => $this->get_calendar_list(),
                'nonce'     => $this->get_nonce()
            );

            return $this->get_view( 'options-form.php', $args );
        }

        /**
         * get the Timezone Info Form view
         *
         * @return string
         */
        public function get_timezone_info_view() {
            $google_calendar_timezone = $this->get_timezone_info();

            $args = array(
                'is_calendar_sync_enabled' => $this->is_calendar_sync_enabled(),
                'google_calendar_timezone' => $google_calendar_timezone,
                'current_timezone'         => yith_wcbk_get_timezone( 'human' )
            );

            return $this->get_view( 'timezone-info.php', $args );
        }

        /**
         * get the Profile Details view
         *
         * @return string
         */
        public function get_profile_details_view() {
            $html = '';
            if ( $this->get_access_token() ) {
                $uri    = 'https://www.googleapis.com/oauth2/v2/userinfo';
                $params = array(
                    'sslverify' => false,
                    'timeout'   => 60,
                    'headers'   => array(
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $this->get_access_token(),
                    ),
                );

                $response = wp_remote_get( $uri, $params );

                if ( !is_wp_error( $response ) && 200 == $response[ 'response' ][ 'code' ] && 'OK' == $response[ 'response' ][ 'message' ] ) {
                    $body = json_decode( $response[ 'body' ] );
                    $html = $this->get_view( 'profile-details.php', array( 'name' => $body->name, 'picture' => $body->picture ) );

                    $this->debug( 'Profile Details retrieved successfully', $body );
                } else {
                    $this->error( 'Error while retrieving user information: ', $response );
                }
            }

            return $html;
        }


        /*
        |--------------------------------------------------------------------------
        | Sync Booking
        |--------------------------------------------------------------------------
        */

        /**
         * sync the booking product by updating/creating the event in Google Calendar
         *
         * @param int|YITH_WCBK_Booking $booking
         * @return bool|string
         */
        public function sync_booking_event( $booking ) {
            $sync_result = false;
            if ( $this->is_calendar_sync_enabled() ) {
                $booking = yith_get_booking( $booking );
                if ( $booking && $booking->is_valid() ) {
                    $booking_id  = $booking->get_id();
                    $calendar_id = $this->get_calendar_id();
                    $event_id    = $this->create_booking_event_id( $booking_id );

                    $time_debug_key = __FUNCTION__ . '_' . $booking_id;
                    yith_wcbk_time_debug_start( $time_debug_key );

                    $date_format      = 'Y-m-d';
                    $date_time_format = 'Y-m-d\TH:i:s';

                    $event_args = array(
                        'id'     => $event_id,
                        'source' => array(
                            'title' => __( 'View Booking', 'yith-booking-for-woocommerce' ),
                            'url'   => admin_url( 'post.php?post=' . $booking_id . '&action=edit' )
                        )
                    );

                    if ( $booking->has_time() ) {
                        $timezone = yith_wcbk_get_timezone();

                        $event_args[ 'start' ] = array(
                            'dateTime' => date( $date_time_format, $booking->from ),
                            'timeZone' => $timezone
                        );
                        $event_args[ 'end' ]   = array(
                            'dateTime' => date( $date_time_format, $booking->to ),
                            'timeZone' => $timezone
                        );

                    } else {
                        $to = $booking->to;
                        if ( $booking->is_all_day() ) {
                            $to = YITH_WCBK_Date_Helper()->get_time_sum( $to, 1, 'day' );
                        }
                        $event_args[ 'start' ] = array(
                            'date' => date( $date_format, $booking->from ),
                        );
                        $event_args[ 'end' ]   = array(
                            'date' => date( $date_format, $to ),
                        );
                    }

                    if ( !empty( $booking->location ) ) {
                        $event_args[ 'location' ] = $booking->location;
                    }

                    if ( $booking->user_id ) {
                        $user_id   = absint( $booking->user_id );
                        $user      = get_user_by( 'id', $user_id );
                        $user_info = '';
                        if ( $user ) {
                            $user_info = ' - ' . esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')';
                        }
                        $event_args[ 'summary' ] = $booking->get_title() . $user_info;
                    } else {
                        $event_args[ 'summary' ] = $booking->get_title();
                    }

                    $description_rows = array(
                        'FROM: ' . $booking->get_formatted_date( 'from' ),
                        'TO: ' . $booking->get_formatted_date( 'to' ),
                        'STATUS: ' . $booking->get_status_text(),
                        'PEOPLE: ' . $booking->persons,
                    );

                    if ( $services = $booking->get_service_names( true ) ) {
                        $description_rows[] = 'SERVICES: ' . implode( ', ', $services );
                    }

                    $event_args[ 'description' ] = implode( ' ', $description_rows );

                    $uri = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';

                    $event_uri = $uri . '/' . $event_id;

                    $get_event_params = array(
                        'sslverify' => false,
                        'timeout'   => 60,
                        'headers'   => array(
                            'Content-Type'  => 'application/json',
                            'Authorization' => 'Bearer ' . $this->get_access_token(),
                        ),
                    );

                    $get_event_response = wp_remote_get( $event_uri, $get_event_params );

                    $event_args = apply_filters( 'yith_wcbk_google_calendar_sync_event_args', $event_args, $booking );

                    $params = array(
                        'method'    => 'POST',
                        'body'      => json_encode( $event_args ),
                        'sslverify' => false,
                        'timeout'   => 60,
                        'headers'   => array(
                            'Content-Type'  => 'application/json',
                            'Authorization' => 'Bearer ' . $this->get_access_token(),
                        ),
                    );

                    $sync_result = 'created';
                    if ( !is_wp_error( $get_event_response ) && 200 == $get_event_response[ 'response' ][ 'code' ] && 'OK' == $get_event_response[ 'response' ][ 'message' ] ) {
                        // UPDATE
                        $uri                = $event_uri;
                        $params[ 'method' ] = 'PUT';
                        $sync_result        = 'updated';

                        $this->debug( 'Booking event already exists', compact( 'booking_id', 'event_id' ) );
                    }

                    $response = wp_remote_post( $uri, $params );

                    if ( !is_wp_error( $response ) && 200 == $response[ 'response' ][ 'code' ] && 'OK' == $response[ 'response' ][ 'message' ] ) {
                        $seconds = yith_wcbk_time_debug_end( $time_debug_key );
                        $this->debug( sprintf( 'Booking event sync success (%s seconds taken)', $seconds ), compact( 'booking_id', 'sync_result', 'event_args' ) );
                    } else {
                        $sync_result = false;
                        $this->error( "Error while synchronizing Booking #$booking_id: ", $response );
                    }

                }

            }

            return $sync_result;
        }


        /**
         * Delete a booking event
         *
         * @param $booking
         * @return bool|string
         * @since 2.1.4
         */
        public function delete_booking_event( $booking ) {
            $sync_result = false;
            if ( $this->is_calendar_sync_enabled() ) {
                $booking = yith_get_booking( $booking );
                if ( $booking && $booking->is_valid() ) {
                    $booking_id  = $booking->get_id();
                    $calendar_id = $this->get_calendar_id();
                    $event_id    = $this->create_booking_event_id( $booking_id );

                    $uri       = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';
                    $event_uri = $uri . '/' . $event_id;

                    $params = array(
                        'method'    => 'DELETE',
                        'sslverify' => false,
                        'timeout'   => 60,
                        'headers'   => array(
                            'Content-Type'  => 'application/json',
                            'Authorization' => 'Bearer ' . $this->get_access_token(),
                        ),
                    );

                    $response = wp_remote_post( $event_uri, $params );
                    if ( !is_wp_error( $response ) && empty( $response[ 'body' ] ) ) {
                        $sync_result = 'deleted';
                        $this->debug( 'Booking event deleted success', compact( 'booking_id', 'sync_result' ) );
                    } else {
                        $sync_result = false;
                        $this->error( "Error while deleting Booking #{$booking_id}: ", $response );
                    }
                }
            }
            return $sync_result;
        }


        /*
        |--------------------------------------------------------------------------
        | Utils
        |--------------------------------------------------------------------------
        */

        /**
         * get the home url
         *
         * @return string
         */
        private function get_home_url() {
            $home_url = home_url();
            $schemes  = apply_filters( 'yith_wcbk_google_calendar_home_url_schemes', array( 'https://', 'http://', 'www.' ) );

            foreach ( $schemes as $scheme ) {
                $home_url = str_replace( $scheme, '', $home_url );
            }

            if ( strpos( $home_url, '?' ) !== false ) {
                list( $base, $query ) = explode( '?', $home_url, 2 );
                $home_url = $base;
            }

            return apply_filters( 'yith_wcbk_google_calendar_get_home_url', $home_url );
        }

        /**
         * retrieve an unique booking event id based on booking ID
         *
         * @param int $booking_id
         * @return string
         */
        public function create_booking_event_id( $booking_id ) {
            $home_url = $this->get_home_url();

            return md5( 'booking' . absint( $booking_id ) . $home_url );
        }

        /**
         * check the nonce
         *
         * @return bool
         */
        public function check_nonce() {
            return !empty( $_POST[ 'yith_wcbk_gcal_nonce' ] ) && wp_verify_nonce( $_POST[ 'yith_wcbk_gcal_nonce' ], 'yith_wcbk_google_calendar_form' );
        }

        /**
         * retrieve the nonce field
         *
         * @return string
         */
        public function get_nonce() {
            return wp_nonce_field( 'yith_wcbk_google_calendar_form', 'yith_wcbk_gcal_nonce', false, false );
        }

        /**
         * add a Log as Debug message if Debug is active
         *
         * @param string $message
         * @param mixed  $obj
         */
        public function debug( $message = '', $obj = null ) {
            if ( $this->is_debug() ) {
                if ( !is_null( $obj ) ) {
                    $message .= !!$message ? ' - ' : '';
                    $message .= print_r( $obj, true );
                }
                yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::DEBUG, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
            }
        }

        /**
         * add a Log as an Error message
         *
         * @param string $message
         * @param mixed  $obj
         */
        public function error( $message = '', $obj = null ) {
            if ( $this->is_debug() ) {
                if ( !is_null( $obj ) ) {
                    $message .= !!$message ? ' - ' : '';
                    $message .= print_r( $obj, true );
                }
                yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
            }
        }
    }
}