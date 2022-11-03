<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Search_Form_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Search_Form_Post_Type_Admin
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Search_Form_Post_Type_Admin {
		/** @var YITH_WCBK_Search_Form_Post_Type_Admin */
		private static $_instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Search_Form_Post_Type_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Search_Form_Post_Type_Admin constructor.
		 */
		private function __construct() {
			add_filter( 'manage_' . YITH_WCBK_Post_Types::$search_form . '_posts_columns', array( $this, 'add_columns' ) );
			add_action( 'manage_' . YITH_WCBK_Post_Types::$search_form . '_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );

			add_action( 'init', array( $this, 'add_style_metabox' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save' ), 10, 1 );
		}

		/**
		 * Add Columns in WP_List_Table for Search Forms
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function add_columns( $columns ) {
			$date_text = $columns['date'];
			unset( $columns['date'] );

			$columns['shortcode'] = __( 'Shortcode', 'yith-booking-for-woocommerce' );
			$columns['date']      = $date_text;

			return $columns;
		}

		/**
		 * Render Shortcode column in Membership Plan List
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function render_columns( $column, $post_id ) {
			if ( $column == 'shortcode' ) {
				$to_copy_id = 'yith-wcbk-copy-to-clipboard-' . $post_id;
				$copy_text  = __( 'Copy to clipboard', 'yith-booking-for-woocommerce' );

				echo "<code id='{$to_copy_id}'>[booking_search_form id={$post_id}]</code>";
				echo "<span class='dashicons dashicons-admin-page yith-wcbk-copy-to-clipboard tips' data-selector-to-copy='#{$to_copy_id}' data-tip='{$copy_text}'></span>";
			}
		}

		/**
		 * Add meta boxes to edit booking page
		 *
		 * @access public
		 *
		 * @param string $post_type Post type.
		 *
		 * @since  1.0.0
		 */
		public function add_meta_boxes( $post_type ) {
			add_meta_box( 'yith-wcbk-search-form-metabox', __( 'Search form', 'yith-booking-for-woocommerce' ), array(
				$this,
				'print_search_form_metabox',
			), YITH_WCBK_Post_Types::$search_form, 'normal', 'high' );

			add_meta_box( 'yith-wcbk-search-form-shortcode-metabox', __( 'Search form shortcode', 'yith-booking-for-woocommerce' ), array(
				$this,
				'print_search_form_shortcode_metabox',
			), YITH_WCBK_Post_Types::$search_form, 'side', 'default' );
		}

		/**
		 * Add the Style metabox through Plugin-FW
		 */
		public function add_style_metabox() {

			$args = array(
				'label'    => __( 'Search form options', 'yith-booking-for-woocommerce' ),
				'pages'    => YITH_WCBK_Post_Types::$search_form,
				'context'  => 'normal',
				'class'    => yith_set_wrapper_class(),
				'priority' => 'high',
				'tabs'     => apply_filters( 'yith_wcbk_search_form_style_settings', array(
					'style'   => array(
						'label'  => __( 'Style', 'yith-booking-for-woocommerce' ),
						'fields' => array(
							'style'                   => array(
								'label'   => __( 'Style', 'yith-booking-for-woocommerce' ),
								'desc'    => __( 'Select a style for the form.', 'yith-booking-for-woocommerce' ),
								'type'    => 'select',
								'options' => array(
									'default'  => __( 'Default', 'yith-booking-for-woocommerce' ),
									'informal' => __( 'Informal', 'yith-booking-for-woocommerce' ),
									'elegant'  => __( 'Elegant', 'yith-booking-for-woocommerce' ),
									'casual'   => __( 'Casual', 'yith-booking-for-woocommerce' ),
								),
								'std'     => 'default',
							),
							'background-color'        => array(
								'label' => __( 'Background', 'yith-booking-for-woocommerce' ),
								'desc'  => __( 'Select the background color of the form.', 'yith-booking-for-woocommerce' ),
								'type'  => 'colorpicker',
								'std'   => 'transparent',
							),
							'text-color'              => array(
								'label' => __( 'Text', 'yith-booking-for-woocommerce' ),
								'desc'  => __( 'Select the label text color of the form.', 'yith-booking-for-woocommerce' ),
								'type'  => 'colorpicker',
								'std'   => '#333333',
							),
							'search-background-color' => array(
								'label' => __( 'Search button background', 'yith-booking-for-woocommerce' ),
								'desc'  => __( 'Select the search button background color.', 'yith-booking-for-woocommerce' ),
								'type'  => 'colorpicker',
								'std'   => '#3b4b56',
							),
							'search-text-color'       => array(
								'label' => __( 'Search button text', 'yith-booking-for-woocommerce' ),
								'desc'  => __( 'Select the search button text color.', 'yith-booking-for-woocommerce' ),
								'type'  => 'colorpicker',
								'std'   => '#ffffff',
							),
							'search-hover-color'      => array(
								'label' => __( 'Search button on hover', 'yith-booking-for-woocommerce' ),
								'desc'  => __( 'Select the search button color on hover.', 'yith-booking-for-woocommerce' ),
								'type'  => 'colorpicker',
								'std'   => '#2e627c',
							),
						),
					),
					'options' => array(
						'label'  => __( 'Options', 'yith-booking-for-woocommerce' ),
						'fields' => array(
							'show-results' => array(
								'label'   => __( 'Show results', 'yith-booking-for-woocommerce' ),
								'desc'    => __( 'Select where you want to show results.', 'yith-booking-for-woocommerce' ),
								'type'    => 'select',
								'options' => array(
									'popup' => __( 'Popup', 'yith-booking-for-woocommerce' ),
									'shop'  => __( 'Shop Page', 'yith-booking-for-woocommerce' ),
								),
								'std'     => 'popup',
							),
						),
					),
				) ),
			);

			$metabox = YIT_Metabox( 'yith-wcbk-search-form-style' );
			$metabox->init( $args );
		}

		/**
		 * render Search form metabox
		 *
		 * @param $post WP_Post
		 *
		 * @return void
		 */
		public function print_search_form_metabox( $post ) {
			include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-search-form-metabox.php';
		}

		/**
		 * render Search form Shortcode metabox
		 *
		 * @param $post WP_Post
		 *
		 * @return void
		 */
		public function print_search_form_shortcode_metabox( $post ) {
			$this->render_columns( 'shortcode', $post->ID );
		}

		/**
		 * Save meta on save post
		 *
		 * @param int $post_id
		 */
		public function save( $post_id ) {
			if ( get_post_type( $post_id ) !== YITH_WCBK_Post_Types::$search_form ) {
				return;
			}

			if ( isset( $_POST['_yith_wcbk_admin_search_form_fields'] ) ) {
				update_post_meta( $post_id, '_yith_wcbk_admin_search_form_fields', $_POST['_yith_wcbk_admin_search_form_fields'] );
			}
		}

	}
}