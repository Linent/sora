<?php
! defined( 'YITH_WCBK' ) && exit();
?>


<form id="yith-wcbk-global-price-rules" method="post" class="yith-plugin-fw">
	<div id="yith-wcbk-settings-tab-wrapper" class="global-price-rules">

		<div class="yith-wcbk-settings-section">
			<div class="yith-wcbk-settings-section__title">
				<h3><?php _e( 'Global price rules', 'yith-booking-for-woocommerce' ) ?></h3>
				<div class="yith-wcbk-price-rules__expand-collapse">
					<span class="yith-wcbk-price-rules__expand"><?php _e( 'Expand all', 'yith-booking-for-woocommerce' ) ?></span>
					<span class="yith-wcbk-price-rules__collapse"><?php _e( 'Collapse all', 'yith-booking-for-woocommerce' ) ?></span>
				</div>
			</div>
			<div class="yith-wcbk-settings-section__content">
				<div class="yith-wcbk-settings-section__description"><?php _e( "You can create advanced rules to set different prices for specific conditions (dates, months, durations).", 'yith-booking-for-woocommerce' ) ?></div>
				<?php
				$field_name  = 'yith_booking_global_cost_ranges';
				$price_rules = YITH_WCBK()->settings->get_global_price_rules();
				include YITH_WCBK_VIEWS_PATH . 'product-tabs/utility/html-price-rules.php';
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'yith_wcbk_settings_fields', 'yith_wcbk_nonce', false ); ?>
	<input type="hidden" name="yith-wcbk-settings-page" value="global-price-rules">
	<div class="yith-wcbk-settings-tab__actions">
		<input type="submit" id="yith-wcbk-settings-tab-actions-save"
				class="yith-wcbk-admin-button" value="<?php _e( 'Save rules', 'yith-booking-for-woocommerce' ) ?>">
	</div>
</form>