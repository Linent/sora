/* global bk, yith_booking_form_params, yith_wcbk_dates */
jQuery( function ( $ ) {
	"use strict";


	$.fn.yith_wcbk_serializeFormJSON = function () {

		var o = {};
		var a = this.serializeArray();
		$.each( a, function () {
			if ( o[ this.name ] ) {
				if ( !o[ this.name ].push ) {
					o[ this.name ] = [o[ this.name ]];
				}
				o[ this.name ].push( this.value || '' );
			} else {
				o[ this.name ] = this.value || '';
			}
		} );
		return o;
	};

	$.fn.yith_booking_form = function () {
		var $form                    = $( this ).closest( '.yith-wcbk-booking-form' ),
			product_id               = $form.data( 'product-id' ),
			booking_data             = $form.data( 'booking_data' ),
			duration_field           = $form.find( '.yith-wcbk-booking-duration' ),
			has_duration             = duration_field.length > 0,
			person_field             = $form.find( '.yith-wcbk-booking-persons' ),
			has_persons              = person_field.length > 0,
			has_time                 = 'hour' === booking_data.duration_unit || 'minute' === booking_data.duration_unit,
			person_types_fields      = $form.find( '.yith-wcbk-booking-person-types' ),
			has_person_types         = person_types_fields.length > 0,
			optional_services_fields = $form.find( 'input[type=checkbox].yith-wcbk-booking-service' ),
			has_optional_services    = optional_services_fields.length > 0,
			service_quantities       = $form.find( 'input.yith-wcbk-booking-service-quantity' ),
			date_field               = $form.find( '.yith-wcbk-booking-start-date' ),
			from_field               = $form.find( '.yith-wcbk-booking-hidden-from' ),
			end_date_field           = $form.find( '.yith-wcbk-booking-end-date' ),
			message                  = $form.find( '.yith-wcbk-booking-form-message' ),
			totals                   = $form.find( '.yith-wcbk-booking-form-totals' ),
			additional_data_fields   = $form.find( '.yith-wcbk-booking-form-additional-data' ),
			messages                 = {
				messages     : [],
				addMessage   : function ( message ) {
					this.messages.push( message );
				},
				getMessages  : function ( reset ) {
					var current_messages = this.messages;
					reset                = reset || 1;
					if ( reset ) {
						this.resetMessages();
					}

					return current_messages;
				},
				resetMessages: function () {
					this.messages = [];
				},
				hasMessages  : function () {
					return !!this.messages.length;
				},
				showAll      : function () {
					var current_messages      = this.getMessages(),
						current_messages_html = '';
					for ( var i = 0; current_messages[ i ]; i++ ) {
						current_messages_html += current_messages[ i ];
					}
					message.html( current_messages_html );
				},
				showFirst    : function () {
					var current_messages      = this.getMessages( 0 ),
						current_messages_html = '';
					if ( current_messages.length > 0 ) {
						current_messages_html = current_messages.shift();
					}
					message.html( current_messages_html );
				},
				hideMessages : function () {
					message.html( '' );
				}
			},
			time_field               = $form.find( '.yith-wcbk-booking-start-date-time' ),
			prices                   = $form.closest( yith_booking_form_params.dom.product_container ).find( yith_booking_form_params.dom.price ),
			price                    = yith_booking_form_params.price_first_only !== 'yes' ? prices : prices.first(),
			add_to_cart              = $form.closest( 'form' ).find( 'button[type=submit]' ),
			add_to_quote             = $( '.add-request-quote-button' ).first(),
			block_params             = {
				message        : bk.loader_svg,
				blockMsgClass  : 'yith-wcbk-block-ui-element',
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
			block_params_none        = {
				message        : null,
				overlayCSS     : {
					opacity: 0
				},
				ignoreIfBlocked: true
			},
			_n                       = function ( number, singular, plural ) {
				if ( isNaN( number ) ) {
					number = 0;
				}

				if ( number < 2 ) {
					return singular;
				} else {
					return plural;
				}
			},
			_onLoad                  = function () {
				if ( date_field.val().length > 0 ) {
					date_field.trigger( 'change' );
				} else {
					if ( yith_booking_form_params.update_form_on_load === 'yes' ) {
						$form.trigger( 'yith_wcbk_booking_form_update' );
					}
				}
			};

		var booking_form_ajax_call = null;
		add_to_cart.attr( 'disabled', true );
		add_to_quote.addClass( 'disabled' );

		$form.on( 'yith_wcbk_booking_form_update', function () {
			var duration      = duration_field.val(),
				persons       = person_field.val(),
				date          = date_field.val(),
				from          = date,
				end_date      = end_date_field.val(),
				person_tot    = 0,
				time          = time_field.val(),
				error_message = '',
				load_data     = true,
				post_data     = {};

			if ( from_field.length > 0 ) {
				if ( time ) {
					from_field.val( date + ' ' + time );
				} else {
					from_field.val( date );
				}
				from = from_field.val();
			}

			if ( has_person_types ) {
				person_types_fields.each( function () {
					person_tot += ( $( this ).val() < 1 ) ? 0 : parseInt( $( this ).val() );
				} );
			} else {
				person_tot = persons < 1 ? 0 : parseInt( persons );
			}

			if ( !duration && has_duration ) {
				error_message += yith_booking_form_params.i18n_empty_duration + '<br />';
				load_data = false;
			}

			if ( !date || ( end_date_field.length > 0 && !end_date ) ) {
				if ( 'yes' === yith_booking_form_params.show_empty_date_time_messages ) {
					if ( !has_time ) {
						error_message += yith_booking_form_params.i18n_empty_date + '<br />';
					} else {
						error_message += yith_booking_form_params.i18n_empty_date_for_time + '<br />';
					}
				}
				load_data = false;
			}

			if ( has_time && date && !time ) {
				if ( 'yes' === yith_booking_form_params.show_empty_date_time_messages ) {
					error_message += yith_booking_form_params.i18n_empty_time + '<br />';
				}
				load_data = false;
			}

			if ( person_tot < booking_data.minimum_number_of_people ) {
				error_message += yith_booking_form_params.i18n_min_persons.replace( /%s/g, booking_data.minimum_number_of_people ) + '<br />';
				load_data = false;
			}

			if ( booking_data.maximum_number_of_people > 0 && person_tot > booking_data.maximum_number_of_people ) {
				error_message += yith_booking_form_params.i18n_max_persons.replace( /%s/g, booking_data.maximum_number_of_people ) + '<br />';
				load_data = false;
			}

			if ( from && end_date && 'day' === booking_data.duration_unit ) {
				duration = yith_wcbk_dates.date_diff( end_date, from, 'days' );

				if ( 'yes' === booking_data.full_day ) {
					duration += 1;
				}

				if ( booking_data.minimum_duration > 0 ) {
					if ( duration < booking_data.minimum_duration ) {
						var minimum_duration_days = _n( booking_data.minimum_duration, yith_booking_form_params.i18n_days.singular, yith_booking_form_params.i18n_days.plural ).replace( /%s/g, booking_data.minimum_duration );

						error_message += yith_booking_form_params.i18n_min_duration.replace( /%s/g, minimum_duration_days ) + '<br />';
						load_data = false;
					}
				}

				if ( booking_data.maximum_duration > 0 ) {
					if ( duration > booking_data.maximum_duration ) {
						var maximum_duration_days = _n( booking_data.maximum_duration, yith_booking_form_params.i18n_days.singular, yith_booking_form_params.i18n_days.plural ).replace( /%s/g, booking_data.maximum_duration );

						error_message += yith_booking_form_params.i18n_max_duration.replace( /%s/g, maximum_duration_days ) + '<br />';
						load_data = false;
					}
				}
			}

			if ( !load_data ) {
				if ( error_message ) {
					messages.addMessage( '<p>' + error_message + '</p>' );
				}

				if ( messages.hasMessages() ) {
					messages.showAll();
				} else {
					messages.resetMessages();
					messages.hideMessages();
				}
				add_to_cart.attr( 'disabled', true );
				add_to_quote.addClass( 'disabled' );
			} else {
				message.block( block_params );
				price.block( block_params );
				add_to_cart.block( block_params_none );
				add_to_quote.block( block_params_none );

				var person_types      = [],
					optional_services = [];
				if ( has_person_types ) {
					person_types_fields.each( function () {
						var person_type_id     = $( this ).data( 'person-type-id' ),
							person_type_number = $( this ).val();

						person_types.push( {
											   id    : person_type_id,
											   number: person_type_number
										   } );
					} );
				}

				if ( has_optional_services ) {
					optional_services_fields.each( function () {
						if ( $( this ).is( ':checked' ) ) {
							var service_id = $( this ).data( 'service-id' );
							optional_services.push( service_id );
						}
					} );
				}

				post_data = {
					product_id      : product_id,
					duration        : duration,
					from            : from,
					time            : time,
					to              : end_date,
					persons         : persons,
					person_types    : person_types,
					booking_services: optional_services,
					action          : 'yith_wcbk_get_booking_data',
					context         : 'frontend'
				};

				if ( service_quantities.length ) {
					service_quantities.each( function () {
						var _name  = $( this ).attr( 'name' ),
							_value = $( this ).val();

						if ( _name.length ) {
							post_data[ _name ] = _value;
						}
					} );
				}

				if ( additional_data_fields.length ) {
					additional_data_fields.each( function () {
						var _name  = $( this ).attr( 'name' ),
							_value = $( this ).val();

						if ( _name.length ) {
							post_data[ _name ] = _value;
							//$.extend(post_data, {[_name]:_value});
						}
					} );
				}

				if ( booking_form_ajax_call ) {
					booking_form_ajax_call.abort();
				}

				booking_form_ajax_call = $.ajax( {
													 type    : "POST",
													 data    : post_data,
													 url     : yith_booking_form_params.ajaxurl,
													 success : function ( response ) {
														 try {
															 if ( response.error ) {
																 messages.addMessage( '<p class="error">' + response.error + '</p>' );
															 } else {
																 if ( response.message ) {
																	 messages.addMessage( response.message );
																 }

																 if ( response.totals_html ) {
																	 totals.html( response.totals_html );
																 } else {
																	 totals.html( '' );
																 }

																 if ( response.price && response.price.length > 0 ) {
																	 price.html( response.price );
																 }

																 if ( response.is_available ) {
																	 add_to_cart.attr( 'disabled', false );
																	 add_to_quote.removeClass( 'disabled' );
																 } else {
																	 add_to_cart.attr( 'disabled', true );
																	 add_to_quote.addClass( 'disabled' );
																 }

																 $form.trigger( 'yith_wcbk_form_update_response', response );
															 }
														 } catch ( err ) {
															 console.log( err.message );
														 }

													 },
													 complete: function ( jqXHR, textStatus ) {
														 if ( textStatus !== 'abort' ) {
															 messages.showFirst();
															 message.unblock();
															 add_to_cart.unblock();
															 add_to_quote.unblock();
															 price.unblock();
														 }
													 }
												 } );

			}


		} );

		$form.on( 'change', 'input, select, .yith-wcbk-date-picker--inline', function ( event ) {
			var $target   = $( event.target ),
				duration  = duration_field.val(),
				date      = date_field.val(),
				time      = time_field.val(),
				post_data = {};

			if ( has_time && date && duration ) {

				if ( $target.is( '.yith-wcbk-booking-start-date' ) || $target.is( '.yith-wcbk-booking-duration' ) ) {
					// get the available times
					time_field.parent().block( block_params );
					post_data = {
						product_id: product_id,
						duration  : duration,
						from      : date,
						action    : 'yith_wcbk_get_booking_available_times',
						context   : 'frontend'
					};

					if ( booking_form_ajax_call ) {
						booking_form_ajax_call.abort();
					}

					booking_form_ajax_call = $.ajax( {
														 type    : "POST",
														 data    : post_data,
														 url     : yith_booking_form_params.ajaxurl,
														 success : function ( response ) {
															 try {
																 if ( response.error ) {
																	 messages.addMessage( '<p class="error">' + response.error + '</p>' );
																 } else {
																	 if ( response.time_data_html ) {
																		 time_field.html( response.time_data_html );
																	 }
																	 if ( time ) {
																		 var $option_selected = time_field.find( 'option[value="' + time + '"]' );
																		 if ( $option_selected ) {
																			 $option_selected.attr( 'selected', 'selected' );
																		 }
																	 }
																	 if ( response.message ) {
																		 messages.addMessage( '<p>' + response.message + '</p>' );
																	 }

																	 $form.trigger( 'yith_wcbk_form_update_time', response );
																 }
															 } catch ( err ) {
																 console.log( err );
															 }

														 },
														 complete: function () {
															 time_field.parent().unblock();
															 $form.trigger( 'yith_wcbk_booking_form_update' );
														 }
													 } );
					// return, since trigger the update on AJAX complete
					return false;
				}
			}

			$form.trigger( 'yith_wcbk_booking_form_update' );

		} );


		$form.on( 'yith_wcbk_booking_form_loaded', _onLoad );


		if ( !yith_booking_form_params.is_admin && 'yes' === yith_booking_form_params.ajax_update_non_available_dates_on_load && date_field.is( '.yith-wcbk-date-picker' ) ) {
			date_field.parent().block( block_params );
			date_field.trigger( 'yith_wcbk_datepicker_load_non_available_dates', {
				callback: function () {
					date_field.parent().unblock();
					$form.trigger( 'yith_wcbk_booking_form_loaded' );
				}
			} );
		} else {
			$form.trigger( 'yith_wcbk_booking_form_loaded' );
		}

	};

	$( document ).on( 'yith-wcbk-init-booking-form', function () {
		var datepicker           = $( '.yith-wcbk-date-picker' ),
			booking_form         = $( '.yith-wcbk-booking-form' ),
			people_selector      = $( '.yith-wcbk-people-selector' ),
			month_picker_wrapper = $( '.yith-wcbk-month-picker-wrapper' );

		/** =============================
		 *      Date Picker
		 * ==============================
		 */
		datepicker.yith_wcbk_datepicker();

		/** =============================
		 *      Month Picker
		 * ==============================
		 */
		month_picker_wrapper.yith_wcbk_monthpicker();

		/** =============================
		 *      People Selector
		 * ==============================
		 */
		people_selector.each( function () {
			$( this ).yith_wcbk_people_selector();
		} );


		/** =============================
		 *      Booking Form
		 * ==============================
		 */
		booking_form.each( function () {
			$( this ).yith_booking_form();
		} );


		$( document ).trigger( 'yith-wcbk-booking-form-loaded' );
	} ).trigger( 'yith-wcbk-init-booking-form' );

	// compatibility with YITH WooCommerce Quick View
	$( document ).on( 'qv_loader_stop', function () {
		$( document ).trigger( 'yith-wcbk-init-booking-form' );
	} );

} );