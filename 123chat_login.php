<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$username = trim( $_GET['username'] );
$password = trim( $_GET['password'] );

$query = SK_MySQL::placeholder( "SELECT `p`.`profile_id` FROM `" . TBL_PROFILE_ONLINE . "` AS `o`
								LEFT JOIN `" . TBL_PROFILE . "` AS `p`
									ON `o`.`profile_id`= `p`.`profile_id`
								WHERE `p`.`username`='?' AND (MD5(`p`.`password`)='?' OR `p`.`password`='?')", $username, $password, $password );
$profile_id = SK_MySQL::query( $query )->fetch_cell();

if ( !$profile_id )
{
	echo 4;
	exit();
}

$profile_info = app_Profile::getFieldValues($profile_id, array('birthdate', 'sex'));

$age = app_Profile::getAge( $profile_info['birthdate'] );

$gender = SK_Language::section('profile_fields.value')->text('sex_'.$profile_info['sex']);

$locations = app_Profile::getFieldValues( $profile_id, array( 'city', 'state', 'country' ) );

$location_str = '';

foreach ( $locations as $location )
{
	$location_str .= ( $location_str ) ? ', '.$location : $location;
}

echo "0|gender=$gender&Age=$age&Location=$location_str";