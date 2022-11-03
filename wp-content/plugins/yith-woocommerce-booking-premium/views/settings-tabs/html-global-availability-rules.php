<?php
! defined( 'YITH_WCBK' ) && exit();
?>

<form id="yith-wcbk-global-availability" method="post" class="yith-plugin-fw">
	<div id="yith-wcbk-settings-tab-wrapper" class="global-availability">

		<div class="yith-wcbk-settings-section">
			<div class="yith-wcbk-settings-section__title">
				<h3><?php _e( 'Global availability rules', 'yith-booking-for-woocommerce' ) ?></h3>
				<div class="yith-wcbk-availability-rules__expand-collapse">
					<span class="yith-wcbk-availability-rules__expand"><?php _e( 'Expand all', 'yith-booking-for-woocommerce' ) ?></span>
					<span class="yith-wcbk-availability-rules__collapse"><?php _e( 'Collapse all', 'yith-booking-for-woocommerce' ) ?></span>
				</div>
			</div>
			<div class="yith-wcbk-settings-section__content">
				<div class="yith-wcbk-settings-section__description"><?php _e( "You can create advanced rules to enable/disable booking availability for specific dates or months", 'yith-booking-for-woocommerce' ) ?></div>
				<?php
				$field_name         = 'yith_booking_global_availability_range';
				$availability_rules = YITH_WCBK()->settings->get_global_availability_rules();
				include YITH_WCBK_VIEWS_PATH . 'product-tabs/utility/html-availability-rules.php';
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'yith_wcbk_settings_fields', 'yith_wcbk_nonce', false ); ?>
	<input type="hidden" name="yith-wcbk-settings-page" value="global-availability-rules">
	<div class="yith-wcbk-settings-tab__actions">
		<input type="submit" id="yith-wcbk-settings-tab-actions-save"
				class="yith-wcbk-admin-button" value="<?php _e( 'Save rules', 'yith-booking-for-woocommerce' ) ?>">
	</div>
</form>
