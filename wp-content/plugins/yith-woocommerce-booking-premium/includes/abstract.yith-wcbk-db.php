<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_DB' ) ) {
	/**
	 * Class YITH_WCBK_DB
	 * handle DB custom tables
	 *
	 * @abstract
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	abstract class YITH_WCBK_DB {

		/** @var string DB version */
		public static $version = '1.0.3';

		public static $booking_notes_table     = 'yith_wcbk_booking_notes';
		public static $log_table               = 'yith_wcbk_logs';
		public static $external_bookings_table = 'yith_wcbk_external_bookings';

		/**
		 * install
		 */
		public static function install() {
			self::create_db_tables();
		}

		/**
		 * create tables
		 *
		 * @param bool $force
		 */
		public static function create_db_tables( $force = false ) {
			global $wpdb;

			$current_version = get_option( 'yith_wcbk_db_version' );

			if ( $force || $current_version != self::$version ) {
				$wpdb->hide_errors();

				$booking_notes_table_name = $wpdb->prefix . self::$booking_notes_table;
				$log_table_name           = $wpdb->prefix . self::$log_table;
				$external_bookings_table  = $wpdb->prefix . self::$external_bookings_table;
				$charset_collate          = $wpdb->get_charset_collate();

				$sql
					= "CREATE TABLE $booking_notes_table_name (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `booking_id` bigint(20) NOT NULL,
                    `type` varchar(255) NOT NULL,
                    `description` TEXT NOT NULL,
                    `note_date` datetime NOT NULL,
                    PRIMARY KEY (id)
                    ) $charset_collate;";


				$sql
					.= "CREATE TABLE $log_table_name (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `description` text NOT NULL,
                    `type` varchar(255) NOT NULL DEFAULT '',
                    `group` varchar(255) NOT NULL,
                    `date` datetime NOT NULL,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

				$sql
					.= "CREATE TABLE $external_bookings_table (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `product_id` bigint(20),
                    `from` bigint(20),
                    `to` bigint(20),
                    `description` text,
                    `summary` text,
                    `location` varchar(255),
                    `uid` varchar(255),
                    `calendar_name` varchar(255) DEFAULT '',
                    `source` varchar(255),
                    `date` datetime,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

				if ( ! function_exists( 'dbDelta' ) ) {
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				}
				dbDelta( $sql );
				update_option( 'yith_wcbk_db_version', self::$version );
			}
		}
	}
}