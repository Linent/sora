jQuery( function ( $ ) {
    "use strict";

    var yith_wcbk_availability_rules = {
        lastIndex               : 1,
        rulesContainer          : false,
        rulesList               : false,
        hasTime                 : false,
        preNewRuleContainer     : $( '#yith-wcbk-availability-rules__pre-new-rule' ),
        expandCollapseButton    : $( '.yith-wcbk-availability-rules__expand-collapse' ),
        saveSettingsButton      : $( '#yith-wcbk-settings-tab-actions-save' ),
        init                    : function () {
            this._initParams();

            $( document ).on( 'yith_wcbk_booking_product_duration_unit_changed', this.updateHasTime );

            $( document ).on( 'click yith_wcbk_admin_booking_availability_rule_type_change', '.yith-wcbk-availability-rule__type', this.changeFromToFieldsByType );
            $( '.yith-wcbk-availability-rules__new-rule' ).on( 'click', this.preAddRule );
            this.preNewRuleContainer.on( 'click', '.yith-wcbk-availability-rules__add-rule', this.addRule );
            $( document ).on( 'click', '.yith-wcbk-availability-rules__delete-rule', this.deleteRule );

            $( 'form#post, form#yith-wcbk-global-availability' ).on( 'submit', this.emptyPreAddRule );

            this.expandCollapseButton.on( 'click', this.expandCollapseAll );

            $( document ).on( 'click', '.yith-wcbk-availability-rule__days-enabled, .yith-wcbk-availability-rule__times-enabled', function ( event ) {
                yith_wcbk_availability_rules.checkFieldVisibility( $( event.target ) );
            } );

            this.initAllRulesVisibility();

            this.expandCollapseVisibility();
        },
        _initParams             : function () {
            this.rulesContainer = $( '.yith-wcbk-availability-rules' ).first();
            this.rulesList      = this.rulesContainer.find( '.yith-wcbk-availability-rules__list' );
            this.lastIndex      = this.rulesList.find( '.yith-wcbk-availability-rule' ).length || 0;
        },
        initAllRulesVisibility  : function () {
            $( '.yith-wcbk-availability-rule' ).each( function () {
                yith_wcbk_availability_rules.checkFieldVisibility( $( this ) );
            } );
        },
        nextIndex               : function () {
            return ++yith_wcbk_availability_rules.lastIndex;
        },
        updateHasTime           : function ( e, data ) {
            yith_wcbk_availability_rules.hasTime = data.has_time || false;

            yith_wcbk_availability_rules.initAllRulesVisibility();
        },
        changeFromToFieldsByType: function ( event ) {
            var radio           = $( event.target ),
                radio_container = radio.parent(),
                value           = radio_container.find( 'input[type=radio]:checked' ).val() || 'month',
                rule_container  = radio_container.closest( '.yith-wcbk-availability-rule' ),
                from_to_section = rule_container.find( '.yith-wcbk-availability-rule__from-to-row' ),
                month_fields    = from_to_section.find( '.yith-wcbk-month-range-select' ),
                date_fields     = from_to_section.find( '.yith-wcbk-admin-date-picker' );

            if ( 'custom' === value ) {
                date_fields.removeAttr( 'disabled' );
                month_fields.attr( 'disabled', 'disabled' );
                date_fields.parent().show();
            } else {
                month_fields.removeAttr( 'disabled' );
                date_fields.attr( 'disabled', 'disabled' );
                date_fields.parent().hide();
            }
        },
        getRuleToAdd            : function () {
            var rule = yith_wcbk_availability_rules.preNewRuleContainer.find( '.yith-wcbk-availability-rule' ).first();
            return rule.length ? rule : false;
        },
        emptyPreAddRule         : function () {
            yith_wcbk_availability_rules.preNewRuleContainer.html( '' );
        },
        preAddRule              : function ( event ) {
            event.preventDefault();
            var button = $( event.target ), _offset;
            if ( !yith_wcbk_availability_rules.getRuleToAdd() ) {
                var template = button.data( 'template' ),
                    index    = yith_wcbk_availability_rules.nextIndex(),
                    new_rule;

                yith_wcbk_availability_rules.preNewRuleContainer.hide();

                template = template.replace( new RegExp( '{{INDEX}}', 'g' ), index );
                new_rule = $( template );
                yith_wcbk_availability_rules.preNewRuleContainer.append( new_rule );

                new_rule.find( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();

                yith_wcbk_availability_rules.checkFieldVisibility( new_rule );

                yith_wcbk_availability_rules.preNewRuleContainer.addClass( 'bk--open' ).slideDown();
            } else {
                if ( yith_wcbk_availability_rules.preNewRuleContainer.is( '.bk--open' ) ) {
                    yith_wcbk_availability_rules.preNewRuleContainer.removeClass( 'bk--open' ).slideUp();
                } else {
                    yith_wcbk_availability_rules.preNewRuleContainer.addClass( 'bk--open' ).slideDown();
                }
            }

            if ( yith_wcbk_availability_rules.preNewRuleContainer.is( '.bk--open' ) ) {
                _offset = button.offset();
                if ( _offset && _offset.top ) {
                    $( 'html, body' ).animate( { scrollTop: _offset.top - button.outerHeight() - 20 } );
                }
            }
        },
        addRule                 : function ( event ) {
            event.preventDefault();
            var rule = $( this ).closest( '.yith-wcbk-availability-rule' );
            rule.find( '.yith-wcbk-availability-rules__add-rule' ).remove();
            yith_wcbk_availability_rules.rulesList.append( rule );

            yith_wcbk_availability_rules.expandCollapseVisibility();
            yith_wcbk_availability_rules.attentionForSaving();
        },
        deleteRule              : function ( event ) {
            event.preventDefault();
            var rule = $( event.target ).closest( '.yith-wcbk-availability-rule' );
            rule
                .animate( { opacity: .3 }, 200 )
                .delay( 200 )
                .slideUp( 300, function () {
                    $( this ).remove();
                    yith_wcbk_availability_rules.expandCollapseVisibility();
                    yith_wcbk_availability_rules.attentionForSaving();
                } );
        },
        checkFieldVisibility    : function ( element ) {
            var rule = $( element ).closest( '.yith-wcbk-availability-rule' ),
                days_enabled, times_enabled, times_enabled_container, bookable, days, times;

            if ( rule.length ) {
                days_enabled            = rule.find( '.yith-wcbk-availability-rule__days-enabled' );
                times_enabled           = rule.find( '.yith-wcbk-availability-rule__times-enabled' );
                times_enabled_container = times_enabled.closest( '.yith-wcbk-form-field__container' );
                bookable                = rule.find( '.yith-wcbk-availability-rule__bookable-row' );
                days                    = rule.find( '.yith-wcbk-availability-rule__day' );
                times                   = rule.find( '.yith-wcbk-availability-rule__day-time' );

                if ( days_enabled.is( ':checked' ) ) {
                    bookable.hide();
                    days.show();
                } else {
                    bookable.show();
                    days.hide();
                }

                if ( days_enabled.is( ':checked' ) && yith_wcbk_availability_rules.hasTime ) {
                    times_enabled_container.show();
                } else {
                    times_enabled_container.hide();
                }

                if ( days_enabled.is( ':checked' ) && yith_wcbk_availability_rules.hasTime && times_enabled.length && times_enabled.is( ':checked' ) ) {
                    times.show();
                } else {
                    times.hide();
                }

                rule.find( '.yith-wcbk-availability-rule__type' ).trigger( 'yith_wcbk_admin_booking_availability_rule_type_change' );
            }
        },
        expandCollapseAll       : function ( event ) {
            var button     = $( event.target ).closest( '.yith-wcbk-availability-rules__expand-collapse' ),
                rules_list = yith_wcbk_availability_rules.rulesList;

            if ( button.is( '.yith-wcbk-availability-rules__expand-collapse--collapse' ) ) {
                button.removeClass( 'yith-wcbk-availability-rules__expand-collapse--collapse' );
                rules_list.find( '.yith-wcbk-settings-section-box:not(.yith-wcbk-settings-section-box--closed) .yith-wcbk-settings-section-box__toggle' ).click();
            } else {
                button.addClass( 'yith-wcbk-availability-rules__expand-collapse--collapse' );
                rules_list.find( '.yith-wcbk-settings-section-box.yith-wcbk-settings-section-box--closed .yith-wcbk-settings-section-box__toggle' ).click();
            }
        },
        expandCollapseVisibility: function () {
            if ( yith_wcbk_availability_rules.rulesList.find( '.yith-wcbk-availability-rule' ).length ) {
                yith_wcbk_availability_rules.expandCollapseButton.show();
            } else {
                yith_wcbk_availability_rules.expandCollapseButton.hide();
            }
        },
        attentionForSaving      : function () {
            if ( yith_wcbk_availability_rules.saveSettingsButton.length ) {
                yith_wcbk_availability_rules.saveSettingsButton.removeClass( 'yith-wcbk-effect--wiggle' );
                yith_wcbk_availability_rules.saveSettingsButton.outerWidth(); // this is useful to allow restarting animation
                yith_wcbk_availability_rules.saveSettingsButton.addClass( 'yith-wcbk-effect--wiggle' );
            }
        }
    };

    yith_wcbk_availability_rules.init();
} );