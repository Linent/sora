<?php
/**
 * @var array              $coordinates
 * @var WC_Product_Booking $product
 * @var string             $width
 * @var string             $height
 * @var string             $zoom
 * @var string             $type
 */
wp_enqueue_script( 'yith-wcbk-booking-map' );

$latitude  = isset( $coordinates[ 'lat' ] ) ? $coordinates[ 'lat' ] : false;
$longitude = isset( $coordinates[ 'lng' ] ) ? $coordinates[ 'lng' ] : false;

if ( !$latitude || !$longitude )
    return;
?>

<div class="yith-wcbk-booking-map-container">
    <div class="yith-wcbk-booking-map"
         style="width:<?php echo $width ?>; height:<?php echo $height ?>"
         data-latitude="<?php echo $latitude ?>"
         data-longitude="<?php echo $longitude ?>"
         data-zoom="<?php echo $zoom ?>"
         data-type="<?php echo $type ?>">
    </div>
</div>