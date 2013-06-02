<?php

$providers = MySQL::fetchArray( "SELECT * FROM `".TBL_FIN_PAYMENT_PROVIDERS."` WHERE `request_time_frame`>0 AND `is_available`='y' 
    AND `status` = 'active' AND UNIX_TIMESTAMP()-`last_request_time`>`request_time_frame`" );

if ( !$providers )
	return;

$__CRON_CONFIRMATION = 'yes';

foreach ( $providers as $provider )
{
	app_Finance::updateLastRequestTime( $provider['fin_payment_provider_id'] );
	include( DIR_SITE_ROOT.'checkout/'.$provider['name'].'/cron_checkout.php' );
}

