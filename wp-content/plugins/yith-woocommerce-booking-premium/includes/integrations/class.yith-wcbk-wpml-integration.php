<?php
! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Integration extends YITH_WCBK_Integration {
	/** @var YITH_WCBK_Wpml_Integration */
	protected static $_instance;

	/** @var SitePress */
	public $sitepress;

	/** @var string */
	public $current_language;

	/** @var string */
	public $default_language;

	/** @var YITH_WCBK_Wpml_Services */
	public $services;

	/**
	 * Constructor
	 *
	 * @param bool $plugin_active
	 * @param bool $integration_active
	 *
	 * @access protected
	 */
	protected function __construct( $plugin_active, $integration_active ) {
		parent::__construct( $plugin_active, $integration_active );

		if ( $this->is_active() ) {
			$this->_init_wpml_vars();
			$this->_load_classes();
		}
	}


	/**
	 * init the WPML vars
	 */
	protected function _init_wpml_vars() {
		if ( $this->is_active() ) {
			global $sitepress;
			$this->sitepress        = $sitepress;
			$this->current_language = $this->sitepress->get_current_language();
			$this->default_language = $this->sitepress->get_default_language();
		}
	}

	/**
	 * get the class name from slug
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function get_class_name_from_slug( $slug ) {
		$class_slug = str_replace( '-', ' ', $slug );
		$class_slug = ucwords( $class_slug );
		$class_slug = str_replace( ' ', '_', $class_slug );

		return 'YITH_WCBK_WPML_' . $class_slug;
	}

	/**
	 * init the WPML vars
	 */
	protected function _load_classes() {
		$utils = array(
			'booking-product',
			'services',
			'person-types',
			'extra-costs',
			'search-forms',
			'cart',
			'multi-currency',
		);

		foreach ( $utils as $util ) {
			$filename  = YITH_WCBK_INCLUDES_PATH . '/integrations/wpml/class.yith-wcbk-wpml-' . $util . '.php';
			$classname = $this->get_class_name_from_slug( $util );

			$var = str_replace( '-', '_', $util );
			if ( file_exists( $filename ) && ! class_exists( $classname ) ) {
				require_once $filename;
			}

			if ( method_exists( $classname, 'get_instance' ) ) {
				$this->$var = $classname::get_instance( $this );
			}
		}
	}


	/**
	 * Return an array of meta to copy from parent booking product
	 *
	 * @return array
	 */
	public static function get_meta_to_copy_from_parent_product() {
		$meta = array(
			'yith_booking_duration_type',
			'yith_booking_duration',
			'yith_booking_duration_unit',
			'yith_booking_minimum_duration',
			'yith_booking_maximum_duration',
			'yith_booking_enable_calendar_range_picker',
			'yith_booking_request_confirmation',
			'yith_booking_can_be_cancelled',
			'yith_booking_cancelled_duration',
			'yith_booking_cancelled_unit',
			'yith_booking_location',
			'yith_booking_location_lat',
			'yith_booking_location_lng',
			'yith_booking_max_per_block',
			'yith_booking_allow_after',
			'yith_booking_allow_after_unit',
			'yith_booking_allow_until',
			'yith_booking_allow_until_unit',
			'yith_booking_availability_range',
			'yith_booking_costs_range',
			'yith_booking_base_cost',
			'yith_booking_block_cost',
			'yith_booking_has_persons',
			'yith_booking_min_persons',
			'yith_booking_max_persons',
			'yith_booking_multiply_costs_by_persons',
			'yith_booking_enable_person_types',
			'yith_booking_person_types',
			'yith_booking_services',
		);

		return apply_filters( 'yith_wcbk_wpml_integration_meta_to_copy_from_parent_product', $meta );
	}

	/**
	 * Retrieve the WPML parent product id
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function get_parent_id( $id ) {
		/** @var WPML_Post_Translation $wpml_post_translations */
		global $wpml_post_translations;
		if ( $wpml_post_translations && $parent_id = $wpml_post_translations->get_original_element( $id ) ) {
			$id = $parent_id;
		}

		return $id;
	}

	/**
	 * get the id for the current language
	 *
	 * @param      $id
	 * @param bool $return_original_if_missing
	 *
	 * @return int|null
	 */
	public function get_current_language_id( $id, $return_original_if_missing = true ) {
		return $this->get_language_id( $id, $return_original_if_missing );
	}

	/**
	 * get the id for the specified language
	 *
	 * @param        $id
	 * @param bool   $return_original_if_missing
	 * @param string $language
	 *
	 * @return int|null
	 */
	public function get_language_id( $id, $return_original_if_missing = true, $language = '' ) {
		$language = ! ! $language ? $language : $this->current_language;
		if ( function_exists( 'icl_object_id' ) ) {
			$id = icl_object_id( $id, get_post_type( $id ), $return_original_if_missing, $language );
		} else if ( function_exists( 'wpml_object_id_filter' ) ) {
			$id = wpml_object_id_filter( $id, get_post_type( $id ), $return_original_if_missing, $language );
		}

		return $id;
	}

	/**
	 * return true if WPML is active
	 *
	 * @return bool
	 */
	public function is_active() {
		global $sitepress;

		return ! empty( $sitepress );
	}

	/**
	 * restore the current language
	 */
	public function restore_current_language() {
		$this->sitepress->switch_lang( $this->current_language );
	}

	/**
	 * Set the current language to default language
	 */
	public function set_current_language_to_default() {
		$this->sitepress->switch_lang( $this->default_language );
	}

	/**
	 * @param $id
	 * @param $taxonomy
	 *
	 * @return bool
	 */
	public function get_original_term_id( $id, $taxonomy ) {
		global $sitepress;

		return is_callable( array( $sitepress, 'get_original_element_id' ) ) ? $sitepress->get_original_element_id( $id, 'tax_' . $taxonomy ) : $id;
	}


}