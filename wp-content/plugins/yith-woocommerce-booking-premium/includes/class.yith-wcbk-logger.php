<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Logger' ) ) {
    /**
     * Class YITH_WCBK_Logger
     * the logger
     *
     * @since  2.0.0
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Logger {
        /** @var YITH_WCBK_Logger */
        private static $_instance;

        /** @var string Logger DB version */
        public static $db_version = '1.0.0';

        private $_time_debug = array();

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Logger
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Logger constructor.
         */
        private function __construct() {
        }

        /**
         * is logger enabled?
         *
         * @since 2.0.7
         * @return bool
         */
        public function is_enabled() {
            return apply_filters( 'yith_wcbk_logger_enabled', true );
        }

        /**
         * Add log
         *
         * @param string|Exception $description
         * @param string           $type
         * @param string           $group
         */
        public function add( $description, $type = 'info', $group = 'general' ) {
            if ( !$this->is_enabled() )
                return;

            global $wpdb;
            $table_name = self::get_db_table_name();

            if ( $description instanceof Exception ) {
                $description_string = 'CODE: ' . $description->getCode() . '<br />';
                $description_string .= 'MESSAGE: ' . $description->getMessage() . '<br />';
                $description_string .= 'TRACE: ' . $description->getTraceAsString() . '<br />';

                $description = $description_string;
            }

            $description = htmlspecialchars( $description );

            $insert_query = "INSERT INTO $table_name (`description`, `type`, `group`, `date`) VALUES ('" . esc_sql( $description ) . "', '" . esc_sql( $type ) . "', '" . esc_sql( $group ) . "', CURRENT_TIMESTAMP() )";
            $wpdb->query( $insert_query );
        }

        /**
         * parse args for query
         *
         * @param array $args
         *
         * @return array
         */
        public function parse_args( $args = array() ) {
            $default_args = array(
                'order_by' => 'date',
                'order'    => 'DESC',
                'limit'    => 20,
                'group'    => '',
                'type'     => '',
                'paged'    => ''
            );
            $args         = wp_parse_args( $args, $default_args );

            $query_args = array(
                'order_by' => $args[ 'order_by' ],
                'order'    => $args[ 'order' ],
                'limit'    => $args[ 'limit' ],
                'paged'    => $args[ 'paged' ],
                'where'    => array()
            );
            if ( $args[ 'group' ] ) {
                $query_args[ 'where' ][] = array(
                    'key'   => 'group',
                    'value' => $args[ 'group' ]
                );
            }

            if ( $args[ 'type' ] ) {
                $query_args[ 'where' ][] = array(
                    'key'   => 'type',
                    'value' => $args[ 'type' ]
                );
            }

            return $query_args;
        }

        /**
         * count the logs
         *
         * @param array $args
         *
         * @return int
         */
        public function count_logs( $args = array() ) {
            $_args = $this->parse_args( $args );
            $args  = array(
                'select' => 'COUNT(*) as count',
                'where'  => $_args[ 'where' ]
            );

            return absint( current( $this->select_query( $args ) )->count );
        }

        /**
         * retrieve groups from the logs in DB
         *
         * @return array
         */
        public function get_groups() {
            global $wpdb;
            $table_name = self::get_db_table_name();
            $query      = "SELECT DISTINCT logs.group FROM $table_name as logs";
            $results    = $wpdb->get_results( $query );

            if ( $results ) {
                $results = array_map( function ( $obj ) {
                    return $obj->group;
                }, $results );
            }

            return !!$results ? $results : array();
        }

        /**
         * retrieve types from the logs in DB
         *
         * @return array
         */
        public function get_types() {
            global $wpdb;
            $table_name = self::get_db_table_name();
            $query      = "SELECT DISTINCT logs.type FROM $table_name as logs";
            $results    = $wpdb->get_results( $query );

            if ( $results ) {
                $results = array_map( function ( $obj ) {
                    return $obj->type;
                }, $results );
            }

            return !!$results ? $results : array();
        }

        /**
         * retrieve the logs from DB
         *
         * @param array $args
         *
         * @return array
         */
        public function get_logs( $args = array() ) {
            return $this->select_query( $this->parse_args( $args ) );
        }

        public function select_query( $args = array() ) {
            global $wpdb;
            $table_name = self::get_db_table_name();

            $limit    = '';
            $where    = '';
            $order_by = '';
            $group_by = '';
            $join     = '';
            $select   = '*';

            if ( isset( $args[ 'select' ] ) ) {
                $select = $args[ 'select' ];
            }

            if ( isset( $args[ 'group_by' ] ) ) {
                $group_by = 'GROUP BY logs_table.' . $args[ 'group_by' ];
            }

            if ( isset( $args[ 'order_by' ] ) ) {
                $_order_by = 'date' === $args[ 'order_by' ] ? 'id' : $args[ 'order_by' ];
                $order_by  = 'ORDER BY logs_table.' . $_order_by;
                if ( isset( $args[ 'order' ] ) ) {
                    $order_by .= ' ' . $args[ 'order' ];
                }

                if ( 'date' !== $args[ 'order_by' ] ) {
                    $order_by .= ', logs_table.id DESC';
                }
            }

            if ( isset( $args[ 'join' ] ) ) {
                $join = $args[ 'join' ];
            }

            if ( !empty( $args[ 'limit' ] ) ) {
                $limit = 'LIMIT ' . absint( $args[ 'limit' ] );

                if ( !empty( $args[ 'paged' ] ) ) {
                    $offset = absint( $args[ 'limit' ] ) * ( absint( absint( $args[ 'paged' ] - 1 ) ) );
                    $limit  .= ' OFFSET ' . $offset;
                }
            }

            $where_array = array();
            if ( isset( $args[ 'where' ] ) ) {
                foreach ( $args[ 'where' ] as $s_where ) {
                    if ( isset( $s_where[ 'key' ] ) ) {
                        $value   = '';
                        $compare = '=';
                        if ( isset( $s_where[ 'value' ] ) ) {
                            $value = $s_where[ 'value' ];
                        } else {
                            $compare = '!=';
                        }

                        if ( isset( $s_where[ 'compare' ] ) ) {
                            $compare = $s_where[ 'compare' ];
                        }

                        $where_array[] = 'logs_table.' . $s_where[ 'key' ] . ' ' . $compare . ' "' . $value . '"';
                    }
                }
            }

            if ( !empty( $where_array ) ) {
                $where = 'WHERE ' . implode( ' AND ', $where_array );
            }

            $query = "SELECT $select FROM $table_name as logs_table $join $where $group_by $order_by $limit";

            $results = $wpdb->get_results( $query );

            return !!$results ? $results : array();
        }

        /**
         * Delete all logs
         */
        public function delete_logs() {
            global $wpdb;
            $table_name = self::get_db_table_name();

            $wpdb->query( "DELETE FROM $table_name WHERE 1" );
        }

        /**
         * get the db table name
         *
         * @return string
         */
        public static function get_db_table_name() {
            global $wpdb;

            return $wpdb->prefix . YITH_WCBK_DB::$log_table;
        }

        /**
         * start time debug
         *
         * @param string $key
         */
        public function time_debug_start( $key = 'global' ) {
            if ( !isset( $this->_time_debug[ $key ] ) ) {
                $this->_time_debug[ $key ] = array();
            }

            array_push( $this->_time_debug[ $key ], microtime( true ) );
        }

        /**
         * end time debug
         *
         * @param string $key
         *
         * @return bool|int
         */
        public function time_debug_end( $key = 'global' ) {
            if ( !empty( $this->_time_debug[ $key ] ) ) {
                $last_time = array_pop( $this->_time_debug[ $key ] );
                $seconds   = round( microtime( true ) - $last_time, 5 );

                return $seconds;
            }

            return false;
        }
    }
}

if ( !class_exists( 'YITH_WCBK_Logger_Groups' ) ) {
    /**
     * Class YITH_WCBK_Logger_Groups
     * Logger Groups Enumeration
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    abstract class YITH_WCBK_Logger_Groups {
        const GENERAL = 'general';
        const GOOGLE_CALENDAR = 'google-calendar';
        const DEBUG = 'debug';
        const BACKGROUND_PROCESS = 'background-process';
        const GOOGLE_MAPS = 'google-maps';

        public static function get_label( $group ) {
            return ucwords( str_replace( '-', ' ', $group ) );
        }
    }
}

if ( !class_exists( 'YITH_WCBK_Logger_Types' ) ) {
    /**
     * Class YITH_WCBK_Logger_Types
     * Logger Types Enumeration
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    abstract class YITH_WCBK_Logger_Types {
        const INFO = 'info';
        const WARNING = 'warning';
        const ERROR = 'error';
        const NOTICE = 'notice';
        const ALERT = 'alert';
        const DEBUG = 'debug';
    }
}

if ( !function_exists( 'yith_wcbk_logger' ) ) {
    /**
     * @return YITH_WCBK_Logger
     */
    function yith_wcbk_logger() {
        return YITH_WCBK_Logger::get_instance();
    }
}

if ( !function_exists( 'yith_wcbk_add_log' ) ) {
    /**
     * add log
     *
     * @param        $description
     * @param string $type
     * @param string $group
     */
    function yith_wcbk_add_log( $description, $type = 'info', $group = 'general' ) {
        yith_wcbk_logger()->add( $description, $type, $group );
    }
}

if ( !function_exists( 'yith_wcbk_maybe_debug' ) ) {
    /**
     * add debug log if debug is active
     *
     * @param        $description
     * @param string $group
     */
    function yith_wcbk_maybe_debug( $description, $group = 'debug' ) {
        if ( yith_wcbk_is_debug() ) {
            yith_wcbk_logger()->add( $description, YITH_WCBK_Logger_Types::DEBUG, $group );
        }
    }
}

if ( !function_exists( 'yith_wcbk_time_debug_start' ) ) {
    function yith_wcbk_time_debug_start( $key = 'global' ) {
        yith_wcbk_logger()->time_debug_start( $key );
    }
}

if ( !function_exists( 'yith_wcbk_time_debug_end' ) ) {
    function yith_wcbk_time_debug_end( $key = 'global' ) {
        return yith_wcbk_logger()->time_debug_end( $key );
    }
}

if ( !function_exists( 'yith_wcbk_time_debug_end_log' ) ) {
    function yith_wcbk_time_debug_end_log( $key = 'global' ) {
        $seconds = yith_wcbk_time_debug_end( $key );
        if ( yith_wcbk_is_debug() ) {
            $seconds = $seconds === false ? 'no' : $seconds;
            $debug   = sprintf( 'Time Debug (%s): %s seconds', $key, $seconds );
            yith_wcbk_logger()->add( $debug, YITH_WCBK_Logger_Types::DEBUG, YITH_WCBK_Logger_Groups::DEBUG );
        }
    }
}

