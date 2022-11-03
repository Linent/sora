<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Search_Form' ) ) {
    /**
     * Class YITH_WCBK_Search_Form
     * the Search Form
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Search_Form {

        /** @var int id of the cpt Search Form */
        public $id;

        /** @var int number of the instance */
        public static $instance_number = 0;

        /** @var int the current number of the instance */
        private $_current_instance_number;

        /** @var array */
        private $_data = array();

        /** @var WP_Post the post object */
        private $_post;

        /**
         * YITH_WCBK_Search_Form constructor.
         *
         * @param int $id
         */
        public function __construct( $id ) {
            $this->id = absint( $id );

            self::$instance_number++;
            $this->_current_instance_number = self::$instance_number;

            $this->_post = get_post( $this->id );
        }

        /**
         * return the current instance number
         *
         * @return int
         */
        public function get_current_instance_number() {
            return $this->_current_instance_number;
        }

        /**
         * return the unique id of the search form
         * id-current_instance_number
         *
         * @return string
         */
        public function get_unique_id() {
            return $this->id . '-' . $this->get_current_instance_number();
        }

        /**
         * @return array|null|WP_Post
         */
        public function get_post_data() {
            return $this->_post;
        }

        /**
         * get the options of the form
         *
         * @return array
         */
        public function get_options() {
            if ( !array_key_exists( 'options', $this->_data ) ) {
                $this->_data[ 'options' ] = array(
                    'show-results' => ( $meta = get_post_meta( $this->id, '_show-results', true ) ) ? $meta : 'popup',
                );
            }

            return $this->_data[ 'options' ];
        }

        /**
         * get the style setting of the form
         *
         * @return array
         */
        public function get_styles() {
            if ( !array_key_exists( 'styles', $this->_data ) ) {
                $this->_data[ 'styles' ] = array(
                    'style'                   => ( $meta = get_post_meta( $this->id, '_style', true ) ) ? $meta : 'default',
                    'background-color'        => ( $meta = get_post_meta( $this->id, '_background-color', true ) ) ? $meta : '#ffffff',
                    'text-color'              => ( $meta = get_post_meta( $this->id, '_text-color', true ) ) ? $meta : '#1a1a1a',
                    'search-background-color' => ( $meta = get_post_meta( $this->id, '_search-background-color', true ) ) ? $meta : '#00a699',
                    'search-text-color'       => ( $meta = get_post_meta( $this->id, '_search-text-color', true ) ) ? $meta : '#ffffff',
                    'search-hover-color'      => ( $meta = get_post_meta( $this->id, '_search-hover-color', true ) ) ? $meta : '#41b7ae',
                );
            }

            return $this->_data[ 'styles' ];
        }

        /**
         * Get fields
         *
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function get_fields() {
            if ( !array_key_exists( 'fields', $this->_data ) ) {
                $default_fields = array(
                    'search'     => array(
                        'enabled' => 'no',
                        'label'   => '',
                    ),
                    'location'   => array(
                        'enabled' => 'yes',
                    ),
                    'categories' => array(
                        'enabled' => 'no',
                    ),
                    'tags'       => array(
                        'enabled' => 'no',
                    ),
                    'date'       => array(
                        'enabled' => 'yes',
                    ),
                    'persons'    => array(
                        'enabled' => 'yes',
                        'type'    => 'persons',
                    ),
                    'services'   => array(
                        'enabled' => 'yes',
                    ),
                );

                $fields = get_post_meta( $this->id, '_yith_wcbk_admin_search_form_fields', true );
                $fields = !!$fields && is_array( $fields ) ? $fields : array();

                foreach ( $default_fields as $_key => $_value ) {
                    empty( $fields[ $_key ] ) && $fields[ $_key ] = $_value;
                }

                $this->_data[ 'fields' ] = $fields;
            }

            return $this->_data[ 'fields' ];
        }


        /**
         * return true if the search form is valid
         *
         * @return bool
         */
        public function is_valid() {
            return !empty( $this->id ) && !empty( $this->_post );
        }

        /**
         * Print the search form
         *
         * @param array $args
         */
        public function output( $args = array() ) {
            if ( !$this->is_valid() )
                return;

            $defaults              = array(
                'cat' => ''
            );
            $args                  = wp_parse_args( $args, $defaults );
            $args[ 'search_form' ] = $this;

            wc_get_template( 'booking/search-form/booking-search-form.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
        }
    }
}