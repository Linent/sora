<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Theme' ) ) {
    /**
     * Class YITH_WCBK_Theme
     * handle the YITH Booking theme install and update
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Theme {
        /** @var YITH_WCBK_Theme */
        private static $_instance;

        /** @var object|null the theme info */
        private $theme_info;

        /** @var string the package url */
        private $booking_theme_package_path;

        /** @var string the package name */
        private $booking_theme_package_name = 'yith-booking.zip';

        /** @var string the booking theme version */
        private $booking_theme_version = '1.2.0';

        const BOOKING_THEME_NAME = 'YITH Booking';

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Theme
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Theme constructor.
         */
        private function __construct() {
            $this->booking_theme_package_path = YITH_WCBK_DIR . $this->booking_theme_package_name;

            $this->action_handler();

            add_action( 'admin_notices', array( $this, 'theme_notices' ) );
        }

        /**
         * print theme notices
         */
        function theme_notices() {
            $is_settings_page = isset( $_GET[ 'page' ] ) && 'yith_wcbk_panel' === $_GET[ 'page' ] && ( !isset( $_GET[ 'tab' ] ) || 'settings' === $_GET[ 'tab' ] );
            if ( !$is_settings_page && current_user_can( 'update_themes' ) && $this->has_booking_theme() && $this->booking_theme_needs_update() ) {
                $update_text   = sprintf( __( 'Update to version %s', 'yith-booking-for-woocommerce' ), $this->get_package_theme_version() );
                $update_url    = admin_url( 'admin.php?page=yith_wcbk_panel' );
                $update_notice = __( 'A new version of <strong>YITH Booking</strong> theme is now available.', 'yith-booking-for-woocommerce' );
                $update_notice .= ' ';
                $update_notice .= "<a href='{$update_url}'>{$update_text}</a>";
                yith_wcbk_print_notice( $update_notice, 'info', true );
            }
        }

        /**
         * Action Handler
         */
        public function action_handler() {
            if ( !empty( $_REQUEST[ 'yith-wcbk-themes-action' ] ) && isset( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'yith-wcbk-themes-action' ) ) {
                try {
                    switch ( $_REQUEST[ 'yith-wcbk-themes-action' ] ) {
                        case 'activate_booking_theme':
                            $this->activate_booking_theme();
                            break;

                        case 'install_booking_theme':
                            $this->install_booking_theme();
                            break;

                        case 'update_booking_theme':
                            $this->update_booking_theme();
                            break;
                        default:
                            break;
                    }

                    wp_redirect( admin_url( 'admin.php?page=yith_wcbk_panel' ) );
                    exit;

                } catch ( Exception $exception ) {
                    wp_die(
                        '<h1>' . __( 'Something went wrong.', 'yith-booking-for-woocommerce' ) . '</h1>' .
                        '<p>' . $exception->getMessage() . '</p>'
                    );
                }
            }
        }

        /**
         * is YITH Booking theme installed?
         *
         * @return bool
         */
        public function has_booking_theme() {
            return !!$this->get_booking_theme();
        }

        /**
         * get the booking theme
         *
         * @return bool|WP_Theme
         */
        public function get_booking_theme() {
            $themes = array_reverse( wp_get_themes(), true );
            foreach ( $themes as $theme ) {
                if ( self::BOOKING_THEME_NAME === $theme->get( 'Name' ) ) {
                    return $theme;
                }
            }

            return false;
        }

        /**
         * is the YITH Booking theme allowed?
         *
         * @return bool
         */
        public function is_booking_theme_allowed() {
            $theme = $this->get_booking_theme();

            return !!$theme && $theme->is_allowed();
        }

        /**
         * is YITH Booking theme active?
         *
         * @return bool
         */
        public function has_booking_theme_active() {
            return self::BOOKING_THEME_NAME === $this->get_theme_name();
        }


        /**
         * does the YITH Booking theme need update?
         *
         * @return bool
         */
        public function booking_theme_needs_update() {
            if ( $this->has_booking_theme() ) {
                $theme = $this->get_booking_theme();
                if ( version_compare( $this->get_package_theme_version(), $theme->get( 'Version' ), '>' ) ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Activate YITH Booking Theme
         *
         * @throws Exception
         */
        public function activate_booking_theme() {
            if ( !current_user_can( 'switch_themes' ) || !current_user_can( 'edit_theme_options' ) )
                throw new Exception( __( 'Sorry, you are not allowed to switch themes for this site.', 'yith-booking-for-woocommerce' ) );

            $theme = $this->get_booking_theme();
            if ( !$theme || !$theme->exists() || !$theme->is_allowed() )
                throw new Exception( __( 'The requested theme does not exist.', 'yith-booking-for-woocommerce' ) );

            switch_theme( $theme->get_stylesheet() );
        }

        /**
         * Install and activate YITH Booking Theme
         *
         * @throws Exception
         */
        public function install_booking_theme() {
            if ( !file_exists( $this->booking_theme_package_path ) )
                throw new Exception( sprintf( __( 'Theme package not found in <code>%s</code>.', 'yith-booking-for-woocommerce' ), $this->booking_theme_package_path ) );

            require_once ABSPATH . 'wp-admin/includes/file.php';
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            include_once ABSPATH . 'wp-admin/includes/theme.php';

            $skin     = new Automatic_Upgrader_Skin();
            $upgrader = new Theme_Upgrader( $skin );
            $result   = $upgrader->install( $this->booking_theme_package_path );

            if ( is_wp_error( $result ) ) {
                throw new Exception( $result->get_error_message() );
            } elseif ( is_wp_error( $skin->result ) ) {
                throw new Exception( $skin->result->get_error_message() );
            } elseif ( is_null( $result ) ) {
                throw new Exception( 'Unable to connect to the filesystem. Please confirm your credentials.', 'yith-booking-for-woocommerce' );
            }

        }

        /**
         * update YITH Booking Theme
         *
         * @throws Exception
         */
        public function update_booking_theme() {
            if ( !file_exists( $this->booking_theme_package_path ) )
                throw new Exception( sprintf( __( 'Theme package not found in <code>%s</code>.', 'yith-booking-for-woocommerce' ), $this->booking_theme_package_path ) );

            if ( !current_user_can( 'update_themes' ) )
                throw new Exception( __( 'Sorry, you are not allowed to update themes for this site.', 'yith-booking-for-woocommerce' ) );

            if ( $theme = $this->get_booking_theme() ) {
                $theme = $theme->get_stylesheet();

                require_once ABSPATH . 'wp-admin/includes/file.php';
                include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                include_once ABSPATH . 'wp-admin/includes/theme.php';

                add_filter( 'pre_site_transient_update_themes', array( $this, 'pre_site_transient_update_themes' ), 999, 1 );

                $skin     = new Automatic_Upgrader_Skin();
                $upgrader = new Theme_Upgrader( $skin );
                $result   = $upgrader->upgrade( $theme );

                if ( is_wp_error( $result ) ) {
                    throw new Exception( $result->get_error_message() );
                } elseif ( is_wp_error( $skin->result ) ) {
                    throw new Exception( $skin->result->get_error_message() );
                } elseif ( is_null( $result ) ) {
                    throw new Exception( __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'yith-booking-for-woocommerce' ) );
                }
            } else {
                $this->install_booking_theme();
            }

        }

        /**
         * set the theme package before upgrading
         *
         * @param $current
         * @return mixed
         */
        public function pre_site_transient_update_themes( $current ) {
            $theme                            = $this->get_booking_theme();
            $stylesheet                       = $theme->get_stylesheet();
            $current->response[ $stylesheet ] = array(
                'package' => $this->booking_theme_package_path
            );

            return $current;
        }

        /**
         * get the theme information
         *
         * @return stdClass
         */
        public function get_theme_info() {
            if ( is_null( $this->theme_info ) ) {
                $this->theme_info          = new stdClass();
                $this->theme_info->name    = '';
                $this->theme_info->slug    = '';
                $this->theme_info->version = '';

                $current_theme = wp_get_theme();
                if ( $current_theme ) {
                    if ( $current_theme->parent() ) {
                        $current_theme = $current_theme->parent();
                    }
                    $this->theme_info->name    = $current_theme->get( 'Name' );
                    $this->theme_info->slug    = sanitize_key( $this->theme_info->name );
                    $this->theme_info->version = $current_theme->get( 'Version' );
                }
            }

            return $this->theme_info;
        }

        /**
         * retrieve the current theme slug
         *
         * @return string
         */
        public function get_theme_slug() {
            $theme_info = $this->get_theme_info();

            return $theme_info->slug;
        }

        /**
         * retrieve the current theme name
         *
         * @return string
         */
        public function get_theme_name() {
            $theme_info = $this->get_theme_info();

            return $theme_info->name;
        }

        /**
         * retrieve the current theme version
         *
         * @return string
         */
        public function get_theme_version() {
            $theme_info = $this->get_theme_info();

            return $theme_info->version;
        }

        /**
         * retrieve the theme version of the package
         *
         * @return string
         */
        public function get_package_theme_version() {
            return $this->booking_theme_version;
        }

        /**
         * return the install theme URL
         *
         * @since 2.0.1
         * @return string
         */
        public function get_install_url() {
            return wp_nonce_url( add_query_arg( array( 'yith-wcbk-themes-action' => 'install_booking_theme' ) ), 'yith-wcbk-themes-action' );
        }

        /**
         * return the activate theme URL
         *
         * @since 2.0.1
         * @return string
         */
        public function get_activate_url() {
            return wp_nonce_url( add_query_arg( array( 'yith-wcbk-themes-action' => 'activate_booking_theme' ) ), 'yith-wcbk-themes-action' );
        }

        /**
         * return the update theme URL
         *
         * @since 2.0.1
         * @return string
         */
        public function get_update_url() {
            return wp_nonce_url( add_query_arg( array( 'yith-wcbk-themes-action' => 'update_booking_theme' ) ), 'yith-wcbk-themes-action' );
        }
    }
}