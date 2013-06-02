<?php

/**
 * Static class for working with site ads
 *
 * @package SkaDate5
 * @version 5.0
 * @since 5.0
 * @link http://www.skadate.com/
 */

class app_Advertisement
{
	/**
	 * Returns the Ads code for
	 * specified document position
	 *
	 * @param integer $position_id
	 * @return boolean|string
	 */
	public static function DocPositionTpl( $position_id )
	{
		$document_key = SK_HttpRequest::getDocument()->document_key;
		
		if ( !strlen( trim( $document_key ) ) || !intval( $position_id ) )
			return false;
		
		// check if linking exists
		$query = SK_MySQL::placeholder( "SELECT `link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
		WHERE `document_key`='?' AND `position_id`=?", $document_key, $position_id );
		
		$link_id = SK_MySQL::query( $query )->fetch_cell();
		
		if ( !$link_id )
			return false;
			

		$ip = $_SERVER['REMOTE_ADDR'];
		$country_str_code = app_Location::getCountryByIp($ip);

/*
		$assigned_country_code = SK_MySQL::query("SELECT `Country_str_code` FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` WHERE `Country_str_code`='".$country_str_code."' ")->fetch_cell();

		if (!$assigned_country_code)
			$country_str_code = "XX";*/

		$query = SK_MySQL::placeholder( "SELECT `tpl`.`code` FROM `".TBL_LINK_ADS_BINDING_TPL_SET."` AS `bind`
			LEFT JOIN `".TBL_LINK_ADS_BINDING_COUNTRY."` AS `tplc` 	USING ( `template_set_id` )
			LEFT JOIN `".TBL_ADS_TEMPLATE."` AS `tpl` USING ( `template_id` )
			WHERE `bind`.`link_id` = '?' 	AND ( `tplc`.`Country_str_code` = '?' OR `tplc`.`Country_str_code` = '?' )", $link_id, $country_str_code, "XX" );

		$ads = MySQL::fetchArray($query);

        return $ads[ rand( 0, count($ads)-1) ]['code'];
	}
}



?>