<?php
! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Services
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Services {
	/** @var YITH_WCBK_Wpml_Services */
	private static $_instance;

	/** @var YITH_WCBK_Wpml_Integration */
	public $wpml_integration;

	/**
	 * Singleton implementation
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration
	 *
	 * @return YITH_WCBK_Wpml_Services
	 */
	public static function get_instance( $wpml_integration ) {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new static( $wpml_integration );
	}

	/**
	 * Constructor
	 *
	 * @access private
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration
	 */
	private function __construct( $wpml_integration ) {
		$this->wpml_integration = $wpml_integration;

		// translate the service names
		add_filter( 'yith_wcbk_get_service_name', array( $this, 'translate_service_name' ), 10, 2 );

		// Display extra fields for taxonomy
		add_action( YITH_WCBK_Post_Types::$service_tax . '_add_form_fields', array( $this, 'add_taxonomy_fields' ), 1, 1 );
		add_action( YITH_WCBK_Post_Types::$service_tax . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ), 1, 1 );


		// Display languages in service table
		add_filter( 'manage_edit-' . YITH_WCBK_Post_Types::$service_tax . '_columns', array( $this, 'get_columns' ) );
		add_action( 'manage_' . YITH_WCBK_Post_Types::$service_tax . '_custom_column', array( $this, 'custom_columns' ), 10, 3 );
	}

	/**
	 * Add fields to Service taxonomy [Add New Service Screen]
	 *
	 * @param string $taxonomy Current taxonomy name
	 *
	 */
	public function add_taxonomy_fields( $taxonomy ) {
		global $sitepress;
		$active_languages = $sitepress->get_active_languages();
		$languages        = $active_languages;

		if ( isset( $languages[ $this->wpml_integration->default_language ] ) ) {
			unset( $languages[ $this->wpml_integration->default_language ] );
		}

		include YITH_WCBK_VIEWS_PATH . 'taxonomies/service/wpml/html-add-service.php';
	}

	/**
	 * Add WPML Languages column
	 *
	 *
	 * @param array $columns the columns
	 *
	 * @return array The columns list
	 * @use   manage_{YITH_WCBK_Post_Types::$service_tax}_columns filter
	 */
	public function get_columns( $columns ) {
		$wpml_languages_title = __( 'WPML Languages', 'yith-booking-for-woocommerce' );
		$to_add               = array(
			'service_wpml_languages' => "<span class='yith-wcbk-wpml-languages-head tips' data-tip='{$wpml_languages_title}'>$wpml_languages_title</span>",
		);

		return array_merge( $columns, $to_add );
	}

	/**
	 * Display WPML flags
	 *
	 *
	 * @param string $custom_column Filter value
	 * @param string $column_name   Column name
	 * @param int    $term_id       The term id
	 *
	 * @internal param \The $columns columns
	 *
	 * @use      manage_{YITH_WCBK_Post_Types::$service_tax}_custom_column filter
	 */
	public function custom_columns( $custom_column, $column_name, $term_id ) {
		$service = yith_get_booking_service( $term_id );
		switch ( $column_name ) {
			case 'service_wpml_languages':
				global $sitepress;
				$active_languages = $sitepress->get_active_languages();
				$languages        = $active_languages;

				if ( isset( $languages[ $this->wpml_integration->default_language ] ) ) {
					unset( $languages[ $this->wpml_integration->default_language ] );
				}
				foreach ( $languages as $language_code => $language_data ) {
					if ( ! empty( $service->wpml_translated_name[ $language_code ] ) ) {
						$service_translated_name = $service->wpml_translated_name[ $language_code ];
						$flag_url                = $this->wpml_integration->sitepress->get_flag_url( $language_code );
						$language_name           = $language_data['display_name'];
						$info                    = "$service_translated_name ($language_name)";
						$flag                    = "<img class='tips' src='$flag_url' width='18' height='12' alt='$language_name' data-tip='$info' style='margin:2px' />";
						echo $flag;
					}
				}
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
		global $sitepress;
		$active_languages = $sitepress->get_active_languages();
		$languages        = $active_languages;

		if ( isset( $languages[ $this->wpml_integration->default_language ] ) ) {
			unset( $languages[ $this->wpml_integration->default_language ] );
		}

		$service_id = $service_term->term_id;
		$service    = yith_get_booking_service( $service_id, $service_term );

		include YITH_WCBK_VIEWS_PATH . 'taxonomies/service/wpml/html-edit-service.php';
	}

	/**
	 * @param string            $name
	 * @param YITH_WCBK_Service $service
	 *
	 * @return string
	 */
	public function translate_service_name( $name, $service ) {
		$language_code = $this->wpml_integration->current_language;

		return ! empty( $service->wpml_translated_name[ $language_code ] ) ? $service->wpml_translated_name[ $language_code ] : $name;
	}
}