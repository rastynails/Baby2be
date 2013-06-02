<?php

class nav_mailbox
{
	
	public function __construct()
	{
		SK_Navigation::register_node('mailbox', array(&$this, 'mailbox'));
	}
	
	public function parseUrl($url='')
	{
		return SITE_URL.'member/location_list.php';
	}
	
	
	public function mailbox( $params = null )
	{
		
		return SITE_URL . 'member/mailbox/inbox';
				
	}
	
}

