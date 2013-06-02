<?php

class nav_custom_pages
{
	public function parseUrl($url='')
	{
		return SITE_URL.'member/location_list.php';
	}
	
	
	public function mailbox( $params = null )
	{
		
		return SITE_URL . 'member/mailbox/inbox';
				
	}
	
}

