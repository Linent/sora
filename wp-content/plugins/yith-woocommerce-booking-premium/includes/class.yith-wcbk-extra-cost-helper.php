<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Extra_Cost_Helper' ) ) {
    /**
     * Class YITH_WCBK_Extra_Cost_Helper
     * helper class for Extra Cost
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Extra_Cost_Helper {
        /** @var YITH_WCBK_Extra_Cost_Helper */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Extra_Cost_Helper
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Get all person types by arguments
         *
         * @param array $args argument for get_posts
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function get_extra_costs( $args = array() ) {
            do_action( 'yith_wcbk_before_get_extra_costs', $args );

            $default_args = array(
                'post_type'        => YITH_WCBK_Post_Types::$extra_cost,
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'suppress_filters' => false,
                'fields'           => 'ids',
                'orderby'          => 'title',
                'order'            => 'ASC'
            );

            $args  = wp_parse_args( $args, $default_args );
            $posts = get_posts( $args );

            do_action( 'yith_wcbk_after_get_extra_costs', $args );

            return $posts;
        }

    }
}