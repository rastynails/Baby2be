<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 19, 2008
 * 
 * TO BE DELETED
 * 
 */


class httpdoc_Profile extends SK_HttpDocument
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'profile' );
	}
	
	public function render( SK_Layout $Layout )
	{	
		return parent::render($Layout);
	}
	
	/* static util methods */
	
	public static function getProfileUrl( array $params )
	{
//		if( isset( $params['username'] ) )
//			$params['username'] = 'no_name';
			
		return SK_Navigation::href( 'profile', array( 'profile_id' => $params['profile_id'] ) );
	}
}