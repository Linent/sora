/* globals wcbk_admin, google, ajaxurl */
jQuery( function ( $ ) {
    "use strict";

    var block_params          = {
            message        : '',
            css            : {
                border    : 'none',
                background: 'transparent'
            },
            overlayCSS     : {
                background: '#fff',
                opacity   : 0.7
            },
            ignoreIfBlocked: true
        },
        block_params_disable  = {
            message        : ' ',
            css            : {
                border    : 'none',
                background: '#fff',
                top       : '0',
                left      : '0',
                height    : '100%',
                width     : '100%',
                opacity   : 0.7,
                cursor    : 'default'
            },
            overlayCSS     : {
                opacity: 0
            },
            ignoreIfBlocked: true
        },
        yith_wcbk_fake_form   = {
            reset_fields : function ( fields ) {

                fields.each( function () {
                    var field = $( this );

                    if ( field.is( 'input' ) ) {
                        if ( field.attr( 'type' ) === 'checkbox' ) {
                            field.removeAttr( 'checked' );
                        } else if ( field.attr( 'type' ) === 'text' || field.attr( 'type' ) === 'number' ) {
                            field.val( '' );
                        }
                    } else if ( field.is( 'textarea' ) ) {
                        field.val( '' );
                    }
                } );
            },
            get_form_data: function ( fields ) {
                var data = !!fields.length ? {} : false;

                fields.each( function () {
                    if ( data !== false ) {
                        var field    = $( this ),
                            name     = field.data( 'name' ) || false,
                            required = field.data( 'required' ) || 'no';

                        if ( name ) {
                            if ( field.is( 'input[type=checkbox]' ) ) {
                                if ( field.is( ':checked' ) ) {
                                    data[ name ] = field.val();
                                }
                            } else {
                                data[ name ] = field.val();
                            }
                        }

                        if ( required === 'yes' && field.val() === '' ) {
                            field.focus();
                            data = false;
                        }
                    }
                } );

                return data;
            }
        },
        booking_duration_unit = $( 'select#_yith_booking_duration_unit' ),
        booking_duration      = $( '#_yith_booking_duration' );

    /** ------------------------------------------------------------------------
     *  Show/Hide Fields
     * ------------------------------------------------------------------------- */

    var bkFieldsVisibility = {
        showPrefix        : '.bk_show_if_',
        enablePrefix      : '.bk_enable_if_',
        conditions        : {
            customer_chooses_blocks: 'customer_chooses_blocks',
            can_be_cancelled       : 'can_be_cancelled',
            customer_one_day       : 'customer_one_day',
            booking_has_persons    : 'booking_has_persons',
            day                    : 'day',
            time                   : 'time',
            fixed_and_time         : 'fixed_and_time',
            unit_is_month          : 'unit_is_month',
            unit_is_day            : 'unit_is_day',
            unit_is_hour           : 'unit_is_hour',
            unit_is_minute         : 'unit_is_minute',
            people_and_people_types: 'people_and_people_types'
        },
        dom               : {
            minuteSelect       : $( '#_yith_booking_duration_minute_select' ),
            bookingDuration    : booking_duration,
            bookingDurationType: $( 'select#_yith_booking_duration_type' ),
            bookingDurationUnit: booking_duration_unit,
            peopleEnabled      : $( '#_yith_booking_has_persons input' ),
            peopleTypesEnabled : $( '#_yith_booking_enable_person_types input' ),
            productType        : $( 'select#product-type' )
        },
        initialProductType: 'simple',
        init              : function () {
            var self = bkFieldsVisibility;

            self.initialProductType = self.dom.productType.val();

            self.dom.minuteSelect.hide().on( 'change', function () {
                self.dom.bookingDuration.val( $( this ).val() );
            } );

            // show TAX if product is Booking
            $( document ).find( '._tax_status_field' ).closest( 'div' ).addClass( 'show_if_' + wcbk_admin.prod_type );

            // on select booking product set Virtual as checked
            self.dom.productType.on( 'change', function () {
                if ( bkFieldsVisibility.initialProductType === wcbk_admin.prod_type ) {
                    return;
                }
                if ( wcbk_admin.prod_type === $( this ).val() ) {
                    $( '#_virtual' ).attr( 'checked', 'checked' ).trigger( 'change' );
                }
            } ).trigger( 'change' ); // trigger change to product type select to call WC show_and_hide_panels()

            // Duration Type
            self.dom.bookingDurationType.on( 'change', function () {
                self.handle( self.conditions.customer_chooses_blocks, 'customer' === $( this ).val() );
            } ).trigger( 'change' );

            // Can be cancelled
            $( '#_yith_booking_can_be_cancelled input' ).on( 'change', function () {
                self.handle( self.conditions.can_be_cancelled, 'yes' === $( this ).val() );
            } ).trigger( 'change' );

            // Show enable calendar picker if customer one day
            $( document ).on( 'bk_check_if_customer_one_day', function () {
                var duration_type = self.dom.bookingDurationType.val(),
                    duration      = self.dom.bookingDuration.val(),
                    duration_unit = self.dom.bookingDurationUnit.val(),
                    show          = duration_type === 'customer' && duration === '1' && duration_unit === 'day';

                self.handle( self.conditions.customer_one_day, show );
            } ).trigger( 'bk_check_if_customer_one_day' );

            $( document ).on( 'change', '#_yith_booking_duration_type, #_yith_booking_duration, #_yith_booking_duration_unit', function () {
                $( document ).trigger( 'bk_check_if_customer_one_day' );
            } );


            // Show/Hide if fixed and time
            $( document ).on( 'bk_check_if_fixed_and_time', function () {
                var _is_fixed      = 'fixed' === self.dom.bookingDurationType.val(),
                    _duration_unit = self.dom.bookingDurationUnit.val(),
                    _is_time       = 'hour' === _duration_unit || 'minute' === _duration_unit;

                self.handle( self.conditions.fixed_and_time, _is_fixed && _is_time );
            } ).trigger( 'bk_check_if_fixed_and_time' );

            $( document ).on( 'change', '#_yith_booking_duration_type, #_yith_booking_duration_unit', function () {
                $( document ).trigger( 'bk_check_if_fixed_and_time' );
            } );

            // People and People Types enabled
            self.dom.peopleEnabled.add( self.dom.peopleTypesEnabled ).on( 'change', function () {
                self.handle( self.conditions.booking_has_persons, 'yes' === self.dom.peopleEnabled.val() );
                self.handle( self.conditions.people_and_people_types, 'yes' === self.dom.peopleEnabled.val() && 'yes' === self.dom.peopleTypesEnabled.val() );
            } ).trigger( 'change' );

            // Booking has times or is day
            self.dom.bookingDurationUnit.on( 'change', function () {
                var value    = $( this ).val(),
                    has_time = value === 'hour' || value === 'minute',
                    is_day   = value === 'day';
                self.handle( self.conditions.time, has_time );
                self.handle( self.conditions.day, is_day );

                self.handle( self.conditions.unit_is_month, 'month' === value );
                self.handle( self.conditions.unit_is_day, 'day' === value );
                self.handle( self.conditions.unit_is_hour, 'hour' === value );
                self.handle( self.conditions.unit_is_minute, 'minute' === value );

                // Minute Select
                if ( value === 'minute' ) {
                    self.dom.bookingDuration.hide();
                    self.dom.minuteSelect.show().trigger( 'change' );
                } else {
                    self.dom.minuteSelect.hide();
                    self.dom.bookingDuration.show();
                }

                $( document ).trigger( 'yith_wcbk_booking_product_duration_unit_changed', [ {
                    value   : value,
                    has_time: has_time,
                    is_day  : is_day
                } ] );
            } ).trigger( 'change' );

        },
        handle            : function ( target, condition ) {
            var targetDisable = bkFieldsVisibility.enablePrefix + target,
                targetHide    = bkFieldsVisibility.showPrefix + target;

            if ( condition ) {
                $( targetDisable ).unblock();
                $( targetHide ).show();
            } else {
                $( targetDisable ).block( block_params_disable );
                $( targetHide ).hide();
            }
        }
    };

    bkFieldsVisibility.init();


    /** ------------------------------------------------------------------------
     *  Costs Table
     * ------------------------------------------------------------------------- */
    var costs_table            = $( '#yith-wcbk-booking-costs-table' ),
        costs_default_row      = costs_table.find( 'tr.yith-wcbk-costs-default-row' ).first(),
        costs_add_range        = $( '#yith-wcbk-costs-add-range' ),
        costs_fields_container = $( '#' + costs_table.data( 'fields-container-id' ) ),
        costs_input            = costs_fields_container.find( '.yith-wcbk-admin-input-range' ),
        costs_number           = costs_fields_container.find( '.yith-wcbk-number-range' ),
        costs_monthselect      = costs_fields_container.find( '.yith-wcbk-month-range-select' ),
        costs_weekselect       = costs_fields_container.find( '.yith-wcbk-week-range-select' ),
        costs_dayselect        = costs_fields_container.find( '.yith-wcbk-day-range-select' );

    costs_table
        .on( 'change', 'select.yith-wcbk-costs-range-type-select', function ( event ) {
            var select     = $( event.target ),
                row        = select.closest( 'tr' ),
                from       = row.find( 'td.yith-wcbk-costs-from' ),
                to         = row.find( 'td.yith-wcbk-costs-to' ),
                range_type = select.val(),
                from_input, to_input;

            switch ( range_type ) {
                case 'custom':
                    from_input = costs_input.clone().addClass( 'yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
                    to_input   = costs_input.clone().addClass( 'yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
                    break;
                case 'month':
                    from_input = costs_monthselect.clone();
                    to_input   = costs_monthselect.clone();
                    break;
                case 'week':
                    from_input = costs_weekselect.clone();
                    to_input   = costs_weekselect.clone();
                    break;
                case 'day':
                    from_input = costs_dayselect.clone();
                    to_input   = costs_dayselect.clone();
                    break;
                case 'time':
                    break;
                default:
                    from_input = costs_number.clone();
                    to_input   = costs_number.clone();
                    break;
            }

            if ( 'time' === range_type ) {
                from_input.find( 'input' ).attr( 'name', '_yith_booking_costs_range[from][]' );
                to_input.find( 'input' ).attr( 'name', '_yith_booking_costs_range[to][]' );
            } else {
                from_input.attr( 'name', '_yith_booking_costs_range[from][]' );
                to_input.attr( 'name', '_yith_booking_costs_range[to][]' );
            }

            from.html( from_input );
            to.html( to_input );

        } )
        /* ----  D e l e t e   R o w  ---- */
        .on( 'click', '.yith-wcbk-delete', function ( event ) {
            var delete_btn = $( event.target ),
                target_row = delete_btn.closest( 'tr' );

            target_row.remove();
        } )

        /* ----  S o r t a b l e  ---- */
        .find( 'tbody' ).sortable( {
                                       items               : 'tr',
                                       cursor              : 'move',
                                       handle              : '.yith-wcbk-anchor',
                                       axis                : 'y',
                                       scrollSensitivity   : 40,
                                       forcePlaceholderSize: true,
                                       opacity             : 0.65,
                                       helper              : function ( e, tr ) {
                                           var originals = tr.children(),
                                               helper    = tr.clone();
                                           helper.children().each( function ( index ) {
                                               // Set helper cell sizes to match the original sizes
                                               $( this ).width( originals.eq( index ).width() );
                                           } );
                                           return helper;
                                       }
                                   } );

    costs_add_range.on( 'click', function () {
        var added_row = costs_default_row.clone().removeClass( 'yith-wcbk-costs-default-row' );
        costs_table.append( added_row );
        added_row.find( 'select.yith-wcbk-costs-range-type-select' ).trigger( 'change' );
    } );

    /** ------------------------------------------------------------------------
     *  Google Maps Auto-complete
     * ------------------------------------------------------------------------- */
    var maps_places_inputs = $( '.yith-wcbk-google-maps-places-autocomplete' );
    maps_places_inputs.each( function () {
        if ( typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.places !== 'undefined' && typeof google.maps.places.Autocomplete !== 'undefined' ) {
            new google.maps.places.Autocomplete( this );
        } else {
            console.error( "Google Maps Javascript API error while using Autocomplete. Probably other plugins (or your theme) include Google Maps Javascript API without support to the Places library" );
        }
    } );


    /** ------------------------------------------------------------------------
     *  Booking Sync - Imported Calendars Table
     * ------------------------------------------------------------------------- */
    $( '.yith-wcbk-product-sync-imported-calendars-table' ).on( 'click', '.insert', function ( e ) {
        e.preventDefault();
        var button = $( e.target ),
            row    = button.data( 'row' ),
            table  = button.closest( '.yith-wcbk-product-sync-imported-calendars-table' ),
            body   = table.find( 'tbody' ), index;

        if ( table.data( 'last-index' ) ) {
            index = table.data( 'last-index' ) + 1;
        } else {
            index = body.find( 'tr' ).length || 0;
            index += 1;
        }

        table.data( 'last-index', index );
        row = row.replace( new RegExp( '{{INDEX}}', 'g' ), index );
        body.append( $( row ) );
    } )
        .on( 'click', '.delete', function ( e ) {
            e.preventDefault();
            var button = $( e.target ),
                row    = button.closest( 'tr' );

            row.remove();
        } );

    /** ------------------------------------------------------------------------
     *  Dynamic Duration
     * ------------------------------------------------------------------------- */
    var yith_wcbk_product_metabox_dynamic_durations = function () {
        var _duration_unit = booking_duration_unit.val(),
            _duration      = booking_duration.val(),
            _duration_label, _duration_label_qty, _duration_unit_label;

        if ( _duration < 2 ) {
            _duration_label     = wcbk_admin.i18n_durations[ _duration_unit ].singular.replace( '%s', _duration );
            _duration_label_qty = wcbk_admin.i18n_durations[ _duration_unit ].singular_qty.replace( '%s', _duration );
        } else {
            _duration_label     = wcbk_admin.i18n_durations[ _duration_unit ].plural.replace( '%s', _duration );
            _duration_label_qty = wcbk_admin.i18n_durations[ _duration_unit ].plural_qty.replace( '%s', _duration );
        }

        _duration_unit_label = wcbk_admin.i18n_durations[ _duration_unit ].plural_unit;

        $( '.yith-wcbk-product-metabox-dynamic-duration' ).html( _duration_label );
        $( '.yith-wcbk-product-metabox-dynamic-duration-qty' ).html( _duration_label_qty );
        $( '.yith-wcbk-product-metabox-dynamic-duration-unit' ).html( _duration_unit_label );
    };
    $( document ).on( 'change', '#_yith_booking_duration, #_yith_booking_duration_unit', yith_wcbk_product_metabox_dynamic_durations );
    $( document ).on( 'yith_wcbk_product_metabox_dynamic_durations', yith_wcbk_product_metabox_dynamic_durations );
    yith_wcbk_product_metabox_dynamic_durations();


    /** ------------------------------------------------------------------------
     *  Create People Type
     * ------------------------------------------------------------------------- */
    var yith_wcbk_product_metabox_create_people_type = {
        container              : $( '#yith-wcbk-people-types__create' ),
        list                   : $( '#yith-wcbk-people-types__list' ),
        fields                 : $( '#yith-wcbk-people-types__create .yith-wcbk-fake-form-field' ),
        init                   : function () {
            var self = yith_wcbk_product_metabox_create_people_type;

            self.container
                .on( 'keydown', self.prevent_submit_on_enter )
                .on( 'click', '.yith-wcbk-people-types__create-submit', self.create )
                .hide();

            $( '#yith-wcbk-people-types__create-btn' ).on( 'click', self.toggle );
        },
        close                  : function () {
            yith_wcbk_product_metabox_create_people_type.container.slideUp( 200 );
        },
        toggle                 : function () {
            yith_wcbk_product_metabox_create_people_type.container.slideToggle( 200 );
        },
        prevent_submit_on_enter: function ( event ) {
            if ( event.keyCode === 13 ) {
                event.preventDefault();
                return false;
            }
        },
        reset_form             : function () {
            var fields = yith_wcbk_product_metabox_create_people_type.fields;
            yith_wcbk_fake_form.reset_fields( fields );
        },
        get_form_data          : function () {
            var fields = yith_wcbk_product_metabox_create_people_type.fields;
            return yith_wcbk_fake_form.get_form_data( fields );
        },
        create                 : function () {
            var container = yith_wcbk_product_metabox_create_people_type.container,
                data      = yith_wcbk_product_metabox_create_people_type.get_form_data();

            if ( data ) {
                data.action = 'yith_wcbk_create_people_type';
                container.block( block_params );


                $.ajax( {
                            type    : "POST",
                            data    : data,
                            url     : ajaxurl,
                            success : function ( response ) {
                                if ( response.message ) {
                                    alert( response.message );
                                }

                                if ( response.id && response.title ) {
                                    yith_wcbk_product_metabox_create_people_type.add_row( response.id, response.title );

                                    yith_wcbk_product_metabox_create_people_type.reset_form();

                                    yith_wcbk_product_metabox_create_people_type.close();
                                }
                            },
                            complete: function () {
                                container.unblock();
                            }
                        } );
            }
        },
        add_row                : function ( id, title ) {
            var template = wp.template( 'yith-wcbk-people-type-row' );
            yith_wcbk_product_metabox_create_people_type.list.append( $( template( { id: id, title: title } ) ) );
        }
    };

    yith_wcbk_product_metabox_create_people_type.init();


    /** ------------------------------------------------------------------------
     *  Create Service
     * ------------------------------------------------------------------------- */
    var yith_wcbk_product_metabox_create_service = {
        container              : $( '#yith-wcbk-services__create' ),
        fields                 : $( '#yith-wcbk-services__create .yith-wcbk-fake-form-field' ),
        services               : $( '#_yith_wcbk_booking_services' ),
        init                   : function () {
            var self = yith_wcbk_product_metabox_create_service;

            self.container
                .on( 'keydown', self.prevent_submit_on_enter )
                .on( 'click', '.yith-wcbk-services__create-submit', self.create )
                .hide();

            $( '#yith-wcbk-services__create-btn' ).on( 'click', self.toggle );
        },
        close                  : function () {
            yith_wcbk_product_metabox_create_service.container.slideUp( 400 );
        },
        toggle                 : function () {
            yith_wcbk_product_metabox_create_service.container.slideToggle( 400 );
        },
        prevent_submit_on_enter: function ( event ) {
            if ( event.keyCode === 13 ) {
                event.preventDefault();
                return false;
            }
        },
        reset_form             : function () {
            var fields = yith_wcbk_product_metabox_create_service.fields;
            yith_wcbk_fake_form.reset_fields( fields );
        },
        get_form_data          : function () {
            var fields = yith_wcbk_product_metabox_create_service.fields;
            return yith_wcbk_fake_form.get_form_data( fields );
        },
        create                 : function () {
            var container = yith_wcbk_product_metabox_create_service.container,
                data      = yith_wcbk_product_metabox_create_service.get_form_data();

            if ( data ) {
                data.action = 'yith_wcbk_create_service';
                container.block( block_params );

                $.ajax( {
                            type    : "POST",
                            data    : data,
                            url     : ajaxurl,
                            success : function ( response ) {
                                if ( response.message ) {
                                    alert( response.message );
                                }

                                if ( response.id && response.title ) {

                                    var _option = $( '<option value="' + response.id + '" selected="selected">' + response.title + '</option>' );
                                    yith_wcbk_product_metabox_create_service.services.append( _option );
                                    yith_wcbk_product_metabox_create_service.services.removeClass( 'enhanced' );
                                    $( document.body ).trigger( 'wc-enhanced-select-init' );
                                    yith_wcbk_product_metabox_create_service.services.focus();

                                    yith_wcbk_product_metabox_create_service.reset_form();
                                    yith_wcbk_product_metabox_create_service.close();
                                }

                                if ( response.debug ) {
                                    console.log( response.debug );
                                }
                            },
                            complete: function () {
                                container.unblock();
                            }
                        } );

            }
        }
    };

    yith_wcbk_product_metabox_create_service.init();


    /** ------------------------------------------------------------------------
     *  Create Extra Cost
     * ------------------------------------------------------------------------- */
    var yith_wcbk_product_metabox_extra_costs = {
        list            : $( '#yith-wcbk-extra-costs__list' ),
        customExtraCosts: false,
        lastIndex       : 1,
        init            : function () {
            this._initParams();

            $( '#yith-wcbk-extra-costs__new-extra-cost' ).on( 'click', this.addExtraCost );
            $( document ).on( 'click', '.yith-wcbk-extra-cost__delete', this.deleteExtraCost );
        },
        _initParams     : function () {
            this.customExtraCosts = this.list.find( '.yith-wcbk-extra-cost--custom' );
            this.lastIndex        = this.customExtraCosts.length || 0;
        },
        nextIndex       : function () {
            return ++this.lastIndex;
        },
        addExtraCost    : function ( event ) {
            event.preventDefault();

            var button   = $( event.target ),
                template = button.data( 'template' ),
                index    = yith_wcbk_product_metabox_extra_costs.nextIndex(),
                new_extra_cost;

            template       = template.replace( new RegExp( '{{INDEX}}', 'g' ), index );
            new_extra_cost = $( template );
            yith_wcbk_product_metabox_extra_costs.list.append( new_extra_cost );
            new_extra_cost.find( '.yith-wcbk-extra-cost__name' ).first().focus();
        },
        deleteExtraCost : function ( event ) {
            $( event.target ).closest( '.yith-wcbk-extra-cost' ).remove();
        }
    };

    yith_wcbk_product_metabox_extra_costs.init();

    /**
     * Expand/Collapse People Types
     */
    var yith_wcbk_product_people_types = {
        expandCollapseButton: $( '.yith-wcbk-people-types__expand-collapse' ),
        list                : $( '#yith-wcbk-people-types__list' ),
        init                : function () {
            this.expandCollapseButton.on( 'click', this.expandCollapseAll );
        },
        expandCollapseAll   : function ( event ) {
            var button = $( event.target ).closest( '.yith-wcbk-people-types__expand-collapse' ),
                list   = yith_wcbk_product_people_types.list;

            if ( button.is( '.yith-wcbk-people-types__expand-collapse--collapse' ) ) {
                button.removeClass( 'yith-wcbk-people-types__expand-collapse--collapse' );
                list.find( '.yith-wcbk-settings-section-box:not(.yith-wcbk-settings-section-box--closed) .yith-wcbk-settings-section-box__toggle' ).click();
            } else {
                button.addClass( 'yith-wcbk-people-types__expand-collapse--collapse' );
                list.find( '.yith-wcbk-settings-section-box.yith-wcbk-settings-section-box--closed .yith-wcbk-settings-section-box__toggle' ).click();
            }
        }
    };

    yith_wcbk_product_people_types.init();

} );