<?php

class nav_profiles
{
	
	public function __construct()
	{
		SK_Navigation::register_node('profile', array(&$this, 'profile'));
		SK_Navigation::register_node('profile_details', array(&$this, 'profile_details'));
		SK_Navigation::register_node('profile_photo_album', array(&$this, 'profile_photo_album'));
	}
	
	
	public function profile( array $params )
	{
						
	}
	
}

