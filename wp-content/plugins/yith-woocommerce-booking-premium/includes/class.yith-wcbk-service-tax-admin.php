<?php
/**
 * Service Taxonomy class
 * Manage Service taxonomy
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WCBK' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Service_Tax_Admin' ) ) {
	/**
	 * YITH_WCBK_Service_Tax
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBK_Service_Tax_Admin {
		/** @var YITH_WCBK_Service_Tax_Admin */
		private static $_instance;

		/** @var string the service taxonomy name */
		public $taxonomy_name;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Service_Tax_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Service_Tax_Admin constructor.
		 */
		private function __construct() {
			$this->taxonomy_name = YITH_WCBK_Post_Types::$service_tax;

			// Display extra fields for taxonomy
			add_action( $this->taxonomy_name . '_add_form_fields', array( $this, 'add_taxonomy_fields' ), 1, 1 );
			add_action( $this->taxonomy_name . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ), 1, 1 );

			// Save extra fields
			add_action( 'edited_' . $this->taxonomy_name, array( $this, 'save_taxonomy_fields' ), 10, 2 );
			add_action( 'created_' . $this->taxonomy_name, array( $this, 'save_taxonomy_fields' ), 10, 2 );

			/* Taxonomy table customization */
			add_filter( "manage_edit-{$this->taxonomy_name}_columns", array( $this, 'get_columns' ) );
			add_action( "manage_{$this->taxonomy_name}_custom_column", array( $this, 'custom_columns' ), 10, 3 );
		}

		/**
		 * Add fields to Service taxonomy [Add New Service Screen]
		 *
		 * @param string $taxonomy Current taxonomy name
		 *
		 * @return void
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_taxonomy_fields( $taxonomy ) {
			if ( apply_filters( 'yith_wcbk_add_taxonomy_fields_display', true, $taxonomy ) ) {
				include YITH_WCBK_VIEWS_PATH . 'taxonomies/service/html-add-service.php';
			}
		}

		/**
		 * Add custom column
		 *
		 * @param string $columns the column
		 *
		 * @return array The columns list
		 * @use   manage_{$this->screen->id}_columns filter
		 * @since 1.0.0
		 */
		public function get_columns( $columns ) {
			$to_remove = array( 'posts', 'slug' );

			foreach ( $to_remove as $column ) {
				unset( $columns[ $column ] );
			}

			$optional_text   = __( 'Optional', 'yith-booking-for-woocommerce' );
			$hidden_text     = __( 'Hidden', 'yith-booking-for-woocommerce' );
			$mult_block_text = __( 'Multiply per units', 'yith-booking-for-woocommerce' );
			$mult_pers_text  = __( 'Multiply per people', 'yith-booking-for-woocommerce' );
			$has_qty_text    = __( 'Has quantity', 'yith-booking-for-woocommerce' );

			$to_add = array(
				'service_price'                => __( 'Price', 'yith-booking-for-woocommerce' ),
				'service_optional'             => "<span class='yith-wcbk-optional-head tips' data-tip='{$optional_text}'>{$optional_text}</span>",
				'service_hidden'               => "<span class='yith-wcbk-hidden-head tips' data-tip='{$hidden_text}'>{$hidden_text}</span>",
				'service_multiply_per_blocks'  => "<span class='yith-wcbk-mult-block-head tips' data-tip='{$mult_block_text}'>{$mult_block_text}</span>",
				'service_multiply_per_persons' => "<span class='yith-wcbk-mult-pers-head tips' data-tip='{$mult_pers_text}'>{$mult_pers_text}</span>",
				'service_quantity_enabled'     => "<span class='yith-wcbk-has-qty-head tips' data-tip='{$has_qty_text}'>{$has_qty_text}</span>",
				//'service_actions'              => __( 'Actions', 'yith-booking-for-woocommerce' ),
			);

			return array_merge( $columns, $to_add );
		}

		/**
		 * Display custom columns for Service Taxonomy
		 *
		 * @param string $custom_column Filter value
		 * @param string $column_name   Column name
		 * @param int    $term_id       The term id
		 *
		 * @internal param \The $columns columns
		 * @use      manage_{$this->screen->taxonomy}_custom_column filter
		 * @since    1.0.0
		 */
		public function custom_columns( $custom_column, $column_name, $term_id ) {
			$service = yith_get_booking_service( $term_id );
			switch ( $column_name ) {
				case 'service_price':
					$person_types_pricing = '';
					if ( $service->is_multiply_per_persons() ) {
						$person_types = YITH_WCBK()->person_type_helper->get_person_type_ids();
						if ( ! ! $person_types ) {
							foreach ( $person_types as $person_type_id ) {
								$pt_price             = $service->get_price_html( $person_type_id );
								$pt_title             = get_the_title( $person_type_id );
								$person_types_pricing .= $pt_title . ': ' . $pt_price . '<br />';
							}
						}
					}

					if ( ! ! $person_types_pricing ) {
						$price_html = $service->get_price_html();
						echo "<span class='tips' data-tip='$person_types_pricing'>$price_html</span>";
					} else {
						echo $service->get_price_html();
					}
					break;

				case 'service_optional':
				case 'service_hidden':
				case 'service_multiply_per_blocks':
				case 'service_multiply_per_persons':
				case 'service_quantity_enabled':
					$method   = 'is_' . substr( $column_name, 8 );
					$value    = $service->$method();
					$dashicon = ! ! $value ? 'yes blue' : 'no';
					$title    = '';
					if ( 'service_hidden' === $column_name && ! $value && $service->is_hidden_in_search_forms() ) {
						$dashicon = 'search blue';
						$title    = __( 'Hidden in search forms', 'yith-booking-for-woocommerce' );
					}

					echo "<span class='yith-wcbk-service-yes-no-icon dashicons dashicons-{$dashicon}' title='{$title}'></span>";
					break;
				case 'service_actions':
					$edit_link  = esc_url( get_edit_term_link( $term_id, $this->taxonomy_name, 'product' ) );
					$edit_title = _x( 'Edit', 'Edit action for services', 'yith-booking-for-woocommerce' );
					echo "<a href='{$edit_link}' class='button tips edit_extra_info' data-tip='{$edit_title}'>{$edit_title}</a>";

					break;
			}
		}


		/**
		 * Edit fields to service taxonomy
		 *
		 * @param WP_Term $service_term Current service information
		 *
		 * @return void
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function edit_taxonomy_fields( $service_term ) {
			if ( ! apply_filters( 'yith_wcbk_edit_taxonomy_fields_display', true, $service_term ) ) {
				return;
			}

			$service_id = $service_term->term_id;
			$service    = yith_get_booking_service( $service_id, $service_term );
			include YITH_WCBK_VIEWS_PATH . 'taxonomies/service/html-edit-service.php';
		}


		/**
		 * Save extra taxonomy fields for service taxonomy
		 *
		 * @param int $taxonomy_id string The vendor id
		 *
		 * @return void
		 * @since  1.0
		 */
		public function save_taxonomy_fields( $taxonomy_id = 0 ) {
			if ( ! isset( $_POST['yith_booking_service_data'] ) ) {
				return;
			}


			$service_data = $_POST['yith_booking_service_data'];
			$service_data = wp_parse_args( $service_data, YITH_WCBK_Service::get_default_meta_data() );

			if ( is_array( $service_data ) && ! ! ( $service_data ) ) {
				$service = yith_get_booking_service( $taxonomy_id );
				if ( $service->is_valid() ) {
					foreach ( $service_data as $key => $value ) {
						$service->set( $key, $value );
					}

					do_action( 'yith_wcbk_service_fields_set', $service, $service_data );
				}
			}

			do_action( 'yith_wcbk_service_tax_taxonomy_fields_saved', $taxonomy_id, $service_data );
		}

		/**
		 * Retrieve info for the taxonomy service
		 * used to create settings
		 *
		 * @param string $service_arg_name
		 * @param string $service_args
		 *
		 * @return array|mixed|string
		 */
		public static function get_service_taxonomy_info( $service_arg_name = '', $service_args = '' ) {
			$service_array = array(
				'price'                  => array(
					'title'   => __( 'Price', 'yith-booking-for-woocommerce' ),
					'type'    => 'price',
					'default' => '',
					'desc'    => __( 'Select the price for this service.', 'yith-booking-for-woocommerce' ),
				),
				'optional'               => array(
					'title'   => __( 'Optional', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if this service is optional (let customers choose to add it or not).',
									 'yith-booking-for-woocommerce' ),
				),
				'hidden'                 => array(
					'title'   => __( 'Hidden', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to hide this service to customers.', 'yith-booking-for-woocommerce' ),
				),
				'hidden_in_search_forms' => array(
					'title'   => __( 'Hidden in search forms', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to hide this service to customers in Booking Search Forms.', 'yith-booking-for-woocommerce' ),
					'deps'    => array(
						'id'    => 'yith_booking_service_hidden',
						'value' => 'no',
					),
				),
				'multiply_per_blocks'    => array(
					'title'   => __( 'Multiply cost by units selected', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to multiply the cost of this service by the number of units selected.',
									 'yith-booking-for-woocommerce' ),
				),
				'multiply_per_persons'   => array(
					'title'   => __( 'Multiply cost by people', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to multiply the cost of this service by the number of people selected.',
									 'yith-booking-for-woocommerce' ),
				),
				'quantity_enabled'       => array(
					'title'   => __( 'Has quantity', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to allow a quantity selection for this service.',
									 'yith-booking-for-woocommerce' ),
				),
				'min_quantity'           => array(
					'title'   => __( 'Min quantity', 'yith-booking-for-woocommerce' ),
					'type'    => 'number',
					'default' => '',
					'desc'    => __( 'Choose the minimum quantity for this service.',
									 'yith-booking-for-woocommerce' ),
					'min'     => 0,
					'deps'    => array(
						'id'    => 'yith_booking_service_quantity_enabled',
						'value' => 'yes',
					),
				),
				'max_quantity'           => array(
					'title'   => __( 'Max quantity', 'yith-booking-for-woocommerce' ),
					'type'    => 'number',
					'default' => '',
					'desc'    => __( 'Choose the maximum quantity for this service. Leave empty for unlimited',
									 'yith-booking-for-woocommerce' ),
					'min'     => 0,
					'deps'    => array(
						'id'    => 'yith_booking_service_quantity_enabled',
						'value' => 'yes',
					),
				),

			);

			/* Add service price for Peron Types */
			$person_types = YITH_WCBK()->person_type_helper->get_person_types_array();
			foreach ( $person_types as $_id => $_title ) {
				$service_array[ 'price_for_pt_' . $_id ] = array(
					'title'          => sprintf( _x( 'Price for %s', 'Price for person type: ex. Price for Children', 'yith-booking-for-woocommerce' ), $_title ),
					'type'           => 'price',
					'default'        => '',
					'name'           => 'yith_booking_service_data[price_for_person_types][' . $_id . ']',
					'desc'           => sprintf( _x( 'Select this service price for %s. Leave empty to use default price', 'e.g. Select this service price for Children/Adults', 'yith-booking-for-woocommerce' ), $_title ),
					'person_type_id' => $_id,
					'deps'           => array(
						'id'    => 'yith_booking_service_multiply_per_persons',
						'value' => 'yes',
					),
				);
			}

			$service_array = apply_filters( 'yith_wcbk_service_tax_get_service_taxonomy_info_service_array', $service_array );

			if ( $service_arg_name === '' ) {
				$info = $service_array;
			} elseif ( $service_args === '' ) {
				$info = isset( $service_array[ $service_arg_name ] ) ? $service_array[ $service_arg_name ] : array();
			} else {
				$info = isset( $service_array[ $service_arg_name ][ $service_args ] ) ? $service_array[ $service_arg_name ][ $service_args ] : '';
			}

			return apply_filters( 'yith_wcbk_service_tax_get_service_taxonomy_info', $info, $service_arg_name, $service_args, $service_array );
		}


		/**
		 * Retrieve field for the "service" taxonomy
		 * used to create settings
		 *
		 * @return array|mixed|string
		 * @since 2.1
		 */
		public static function get_service_taxonomy_fields() {
			$service_array = array(
				'price'                  => array(
					'title'   => __( 'Price', 'yith-booking-for-woocommerce' ),
					'type'    => 'text',
					'class'   => 'wc_input_price',
					'default' => '',
					'desc'    => __( 'Select the price for this service.', 'yith-booking-for-woocommerce' ),
				),
				'optional'               => array(
					'title'   => __( 'Optional', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if this service is optional (let customers choose to add it or not).',
									 'yith-booking-for-woocommerce' ),
				),
				'hidden'                 => array(
					'title'   => __( 'Hidden', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to hide this service to customers.', 'yith-booking-for-woocommerce' ),
				),
				'hidden_in_search_forms' => array(
					'title'      => __( 'Hidden in search forms', 'yith-booking-for-woocommerce' ),
					'type'       => 'checkbox',
					'default'    => 'no',
					'desc'       => __( 'Select if you want to hide this service to customers in Booking Search Forms.', 'yith-booking-for-woocommerce' ),
					'field_deps' => array(
						'id'    => 'hidden',
						'value' => 'no',
					),
				),
				'multiply_per_blocks'    => array(
					'title'   => __( 'Multiply cost by units selected', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to multiply the cost of this service by the number of units selected.',
									 'yith-booking-for-woocommerce' ),
				),
				'multiply_per_persons'   => array(
					'title'   => __( 'Multiply cost by people', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to multiply the cost of this service by the number of people selected.',
									 'yith-booking-for-woocommerce' ),
				),
				'quantity_enabled'       => array(
					'title'   => __( 'Has quantity', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to allow a quantity selection for this service.',
									 'yith-booking-for-woocommerce' ),
				),
				'min_quantity'           => array(
					'title'             => __( 'Min quantity', 'yith-booking-for-woocommerce' ),
					'type'              => 'number',
					'default'           => '',
					'desc'              => __( 'Choose the minimum quantity for this service.',
											   'yith-booking-for-woocommerce' ),
					'custom_attributes' => 'min="0"',
					'field_deps'        => array(
						'id'    => 'quantity_enabled',
						'value' => 'yes',
					),
				),
				'max_quantity'           => array(
					'title'             => __( 'Max quantity', 'yith-booking-for-woocommerce' ),
					'type'              => 'number',
					'default'           => '',
					'desc'              => __( 'Choose the maximum quantity for this service. Leave empty for unlimited',
											   'yith-booking-for-woocommerce' ),
					'custom_attributes' => 'min="0"',
					'field_deps'        => array(
						'id'    => 'quantity_enabled',
						'value' => 'yes',
					),
				),

			);

			/* Add service price for Peron Types */
			$person_types = YITH_WCBK()->person_type_helper->get_person_types_array();
			foreach ( $person_types as $_id => $_title ) {
				$service_array[ 'price_for_pt_' . $_id ] = array(
					'title'          => sprintf( _x( 'Price for %s', 'Price for person type: ex. Price for Children', 'yith-booking-for-woocommerce' ), $_title ),
					'type'           => 'text',
					'class'          => 'wc_input_price',
					'default'        => '',
					'name'           => 'price_for_person_types[' . $_id . ']',
					'desc'           => sprintf( _x( 'Select this service price for %s. Leave empty to use default price', 'e.g. Select this service price for Children/Adults', 'yith-booking-for-woocommerce' ), $_title ),
					'person_type_id' => $_id,
					'field_deps'     => array(
						'id'    => 'multiply_per_persons',
						'value' => 'yes',
					),
				);
			}

			return apply_filters( 'yith_wcbk_service_tax_get_service_taxonomy_fields', $service_array );
		}
	}
}