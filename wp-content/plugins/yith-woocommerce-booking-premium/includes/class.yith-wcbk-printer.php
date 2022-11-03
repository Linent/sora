<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Printer' ) ) {

    /**
     * Class YITH_WCBK_Printer
     * the printer
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Printer {
        /** @var YITH_WCBK_Product_Post_Type_Admin */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Printer|YITH_WCBK_Product_Post_Type_Admin
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Printer constructor.
         */
        private function __construct() {
        }

        /**
         * print fields
         *
         * @param array $args
         */
        public function print_fields( $args = array() ) {
            $args = apply_filters( 'yith_wcbk_printer_print_fields_args', $args );
            if ( isset( $args[ 'type' ] ) ) {
                $args = array( $args );
            }
            foreach ( $args as $field_args ) {
                $this->print_field( $field_args );
            }
        }

        /**
         * print a field
         *
         * @param array $args
         */
        public function print_field( $args = array() ) {
            $default_args = array(
                'type'              => '',
                'id'                => '',
                'name'              => '',
                'value'             => '',
                'class'             => '',
                'custom_attributes' => '',
                'for'               => '',
                'options'           => array(),
                'data'              => array(),
                'title'             => '',
                'fields'            => array(),
                'help_tip'          => '',
                'help_tip_alt'      => '',
                'desc'              => '',
                'desc_box'          => '',
                'section_html_tag'  => 'div'
            );
            $args         = apply_filters( 'yith_wcbk_printer_print_field_args', $args );
            $args         = wp_parse_args( $args, $default_args );

            $type         = $args[ 'type' ];
            $title        = $args[ 'title' ];
            $help_tip     = $args[ 'help_tip' ];
            $help_tip_alt = $args[ 'help_tip_alt' ];
            $desc         = $args[ 'desc' ];
            $desc_box     = $args[ 'desc_box' ];

            if ( !empty( $title ) && $type !== 'checkbox' ) {
                $this->print_field( array(
                                        'type'  => 'label',
                                        'value' => $title,
                                        'for'   => $args[ 'id' ]
                                    ) );
            }

            switch ( $type ) {
                case 'section':
                    $fields = $args[ 'fields' ];
                    unset( $args[ 'fields' ] );

                    $args[ 'type' ] = 'section-start';
                    $this->print_field( $args );

                    if ( !empty( $fields ) ) {
                        $this->print_fields( $fields );
                    } elseif ( !empty( $args[ 'value' ] ) ) {
                        $this->print_field( array(
                                                'type'  => 'html',
                                                'value' => $args[ 'value' ],
                                            ) );
                    }

                    $args[ 'type' ] = 'section-end';
                    $this->print_field( $args );
                    break;
                default:
                    if ( file_exists( YITH_WCBK_TEMPLATE_PATH . 'printer/types/' . $type . '.php' ) ) {
                        wc_get_template( 'printer/types/' . $type . '.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
                    }
                    break;
            }

            if ( !empty( $title ) && $type === 'checkbox' ) {
                $this->print_field( array(
                                        'type'  => 'label',
                                        'value' => $title,
                                        'for'   => $args[ 'id' ]
                                    ) );
            }

            if ( !in_array( $type, array( 'section', 'section-start' ) ) ) {
                if ( !empty( $help_tip ) ) {
                    $this->print_field( array(
                                            'type'  => 'help-tip',
                                            'value' => $help_tip,
                                        ) );
                } elseif ( !empty( $help_tip_alt ) ) {
                    $this->print_field( array(
                                            'type'  => 'help-tip-alt',
                                            'value' => $help_tip_alt,
                                        ) );
                } elseif ( !empty( $desc ) ) {
                    $this->print_field( array(
                                            'type'             => 'section',
                                            'value'            => $desc,
                                            'class'            => 'description',
                                            'section_html_tag' => 'span'
                                        ) );
                }

                if ( !empty( $desc_box ) ) {
                    $this->print_field( array(
                                            'type'  => 'desc-box',
                                            'value' => $desc_box,
                                        ) );
                }
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCBK_Printer class
 *
 * @return YITH_WCBK_Printer
 */
function YITH_WCBK_Printer() {
    return YITH_WCBK_Printer::get_instance();
}
