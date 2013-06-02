<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 23, 2008
 * 
 */

class component_ThumbedProfileList extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $profile_id_list;
	
	/**
	 * Class constructor
	 *
	 * @param array $profile_id_list
	 */
	public function __construct( $profile_id_list )
	{
		parent::__construct( 'thumbed_profile_list' );
		
		$this->profile_id_list = $profile_id_list;
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		
		$profile_array = $this->profile_id_list;

		$assign_array = array();
		
		foreach ( $profile_array as $key => $value )
		{
			$assign_array[$key]['username'] = $value['username'];
			$assign_array[$key]['url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['id'] ) );
			$assign_array[$key]['id'] = $value['id'];
		}

		$Layout->assign( 'profiles', $assign_array );
		
		return parent::render( $Layout );		
	}
}