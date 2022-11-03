<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Person_Type_Helper' ) ) {
    /**
     * Class YITH_WCBK_Person_Type_Helper
     *
     *helper class for Person Types
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Person_Type_Helper {
        /** @var YITH_WCBK_Person_Type_Helper */
        private static $_instance;

        /** @var string post type name of Person Types */
        public $post_type_name;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Person_Type_Helper
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Person_Type_Helper constructor.
         */
        public function __construct() {
            $this->post_type_name = YITH_WCBK_Post_Types::$person_type;
        }

        /**
         * Get all person types by arguments
         *
         *
         * @param array $args argument for get_posts
         *
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return WP_Post[]|bool
         */
        public function get_person_types( $args = array() ) {
            $do_actions = !isset( $args[ 'suppress_filters' ] ) || $args[ 'suppress_filters' ] === true;
            if ( $do_actions )
                do_action( 'yith_wcbk_before_get_person_types', $args );

            $default_args = array(
                'post_type'        => $this->post_type_name,
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'suppress_filters' => false,
            );

            $args  = wp_parse_args( $args, $default_args );
            $posts = get_posts( $args );

            if ( $do_actions )
                do_action( 'yith_wcbk_after_get_person_types', $args );

            return $posts;
        }

        /**
         * Get all person type ids by arguments
         *
         *
         * @param array $args argument for get_posts
         *
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return int[]|bool
         */
        public function get_person_type_ids( $args = array() ) {
            $default_args = array(
                'fields' => 'ids',
            );

            $args = wp_parse_args( $args, $default_args );

            return $this->get_person_types( $args );
        }


        /**
         * Get all person types in array id => name
         *
         *
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function get_person_types_array() {
            $ids          = $this->get_person_type_ids();
            $person_types = array();

            if ( !!$ids && is_array( $ids ) ) {
                foreach ( $ids as $id ) {
                    $person_types[ $id ] = get_the_title( $id );
                }
            }

            return $person_types;
        }

        /**
         * @param $person_type_id
         *
         * @return string
         */
        public function get_person_type_title( $person_type_id ) {
            return apply_filters( 'yith_wcbk_get_person_type_title', get_the_title( $person_type_id ), $person_type_id );
        }

    }
}