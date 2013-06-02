<?php

require_once( DIR_SERVICE_AUTOCROP.'class.image_cropper_proxy.php' );

function getAutoCropStat( $username, $password, $start_stamp, $end_stamp )
{
	$username = trim( $username );
	$password = trim( $password );
	
	$cropper = new ImageCropperProxy( AUTOCROP_SERVICE_URL, $username, $password );
	return $cropper->GetCropStat( $start_stamp, $end_stamp );
}

function calculateAutocropStatTimeStamp( $start_date, $end_date, $year_name = 'Date_Year', $month_name = 'Date_Month', $day_name = 'Date_Day' )
{
	if ( !$start_date || !$end_date )
		return array();
		
	$_start_year = $start_date[$year_name];
	$_start_month = $start_date[$month_name];
	$_start_day = $start_date[$day_name];
	
	$_end_year = $end_date[$year_name];
	$_end_month = $end_date[$month_name];
	$_end_day = $end_date[$day_name];
		
	// detect if start and end day are tha same
	if ( $_start_day == $_end_day && $_start_month == $_end_month && $_start_year == $_end_year )
		$_end_day++;

	$_return['start_stamp'] = mktime( 0,0,0, $_start_month, $_start_day, $_start_year );
	$_return['end_stamp'] = mktime( 0,0,0, $_end_month, $_end_day, $_end_year );
	
	return $_return;
}

function isAutoCropAllow( $username, $password )
{
	$cropper = new ImageCropperProxy( AUTOCROP_SERVICE_URL, $username, $password );
	
	return $cropper->IsValidUser();
}

?>