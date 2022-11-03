<?php
!defined( 'YITH_WCBK' ) && exit;

if ( !function_exists( 'yith_wcbk_get_search_form' ) ) {

    /**
     * Get the booking search form object.
     *
     * @param  int $id
     * @uses   YITH_WCBK_Search_Form
     * @return YITH_WCBK_Search_Form|bool
     */
    function yith_wcbk_get_search_form( $id ) {
        $_search_form = false;
        if ( is_numeric( $id ) ) {
            $_search_form = new YITH_WCBK_Search_Form( $id );
        }

        if ( !$_search_form instanceof YITH_WCBK_Search_Form || !$_search_form->is_valid() ) {
            $_search_form = false;
        }

        return apply_filters( 'yith_wcbk_get_search_form', $_search_form );
    }
}

if ( !function_exists( 'yith_wcbk_product_extra_costs_array_reduce' ) ) {
    /**
     * @param array                        $result the result array
     * @param YITH_WCBK_Product_Extra_Cost $extra_cost
     * @return array
     */
    function yith_wcbk_product_extra_costs_array_reduce( $result, $extra_cost ) {
        if ( $extra_cost->is_valid() ) {
            $result[ $extra_cost->get_identifier() ] = $extra_cost;
        }
        return $result;
    }
}


if ( !function_exists( 'yith_wcbk_simple_object_to_array' ) ) {
    /**
     *  simple object to array
     *
     * @param YITH_WCBK_Simple_Object $object
     * @return array|mixed
     * @since 2.1
     */
    function yith_wcbk_simple_object_to_array( $object ) {
        return $object instanceof YITH_WCBK_Simple_Object ? $object->to_array() : $object;
    }
}

if ( !function_exists( 'yith_wcbk_simple_objects_to_array' ) ) {
    /**
     *  simple object array to array of array
     *
     * @param array $array
     * @return array
     * @since 2.1
     */
    function yith_wcbk_simple_objects_to_array( $array ) {
        return is_array( $array ) ? array_map( 'yith_wcbk_simple_object_to_array', $array ) : array();
    }
}