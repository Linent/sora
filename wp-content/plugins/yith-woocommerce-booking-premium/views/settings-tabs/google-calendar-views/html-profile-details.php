<?php
/**
 * @var Google_Service_Oauth2 $profile
 * @var string                $picture
 * @var string                $name
 */
!defined( 'YITH_WCBK' ) && exit();
?>

<div class='yith-wcbk-google-calendar-profile'>

    <div class='yith-wcbk-google-calendar-profile__image'>
        <img class='' src='<?php echo $picture ?>'/>
    </div>

    <div class='yith-wcbk-google-calendar-profile__data'>
        <span class='yith-wcbk-google-calendar-profile__title'><?php echo $name ?></span>
    </div>

    <div class='clear'></div>
</div>

