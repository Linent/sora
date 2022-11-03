<?php
! defined( 'YITH_WCBK' ) && exit();
/**
 * @var bool   $has_plugin
 * @var string $slug
 * @var array  $plugin
 */

$is_optional = isset( $plugin['optional'] ) && $plugin['optional'] === true;
$is_active   = $has_plugin && ( ! $is_optional || get_option( 'yith-wcbk-' . $slug . '-add-on-active', 'no' ) === 'yes' );
$class       = $is_active ? 'active' : '';
$is_new      = isset( $plugin['new'] ) && ! ! $plugin['new'];

$dectivation_link = add_query_arg( array( 'yith-wcbk-integration-action' => 'deactivate', 'integration' => $slug ) );
$activation_link  = add_query_arg( array( 'yith-wcbk-integration-action' => 'activate', 'integration' => $slug ) );

$badges = array();

if ( $is_new ) {
	$badges['new'] = _x( 'New', 'text of integration badge', 'yith-booking-for-woocommerce' );
}

?>

<div class="yith-wcbk-integration">
	<?php if ( ! ! $badges ) {
		foreach ( $badges as $type => $text ) {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-badge.php';
		}
	}
	?>
	<img class="yith-wcbk-integration-icon" src="<?php echo $plugin['icon'] ?>"/>
	<h5><?php echo $plugin['title'] ?></h5>
	<div class="yith-wcbk-integration-content">
		<div class="yith-wcbk-integration-description"><?php echo $plugin['description'] ?></div>
		<?php if ( ! $has_plugin ): ?>
			<div class="yith-wcbk-integration-needs-plugin">
				<?php echo sprintf( __( '(needs %1$s plugin – version %2$s or greater)', 'yith-booking-for-woocommerce' ), $plugin['name'], $plugin['min_version'] ); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="yith-wcbk-integration-actions">
		<?php if ( ! $has_plugin ): ?>
			<a href="<?php echo $plugin['landing_uri'] ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--dark yith-wcbk-admin-button--icon-download" target="_blank">
				<?php echo sprintf( __( 'Get Plugin', 'yith-booking-for-woocommerce' ) ); ?>
			</a>
		<?php else: ?>

			<?php if ( $is_active ): ?>
				<?php if ( $is_optional ): ?>
					<a href="<?php echo $dectivation_link ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-close yith-wcbk-integration-action deactivate"><?php echo sprintf( __( 'Deactivate integration', 'yith-booking-for-woocommerce' ) ); ?></a>
				<?php else: ?>
					<span class="yith-wcbk-integration-automatically-active"><?php _e( 'This integration is automatically active', 'yith-booking-for-woocommerce' ) ?></span>
				<?php endif; ?>
			<?php else: ?>
				<?php if ( $is_optional ): ?>
					<a href="<?php echo $activation_link ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-check yith-wcbk-integration-action activate"><?php echo sprintf( __( 'Activate integration', 'yith-booking-for-woocommerce' ) ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
