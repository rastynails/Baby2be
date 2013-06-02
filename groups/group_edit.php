<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

switch (SK_HttpRequest::$GET['action'])
{
	case 'invite':
		$httpdoc = new component_GroupInvitation;
		break;
		
	case 'mails':
		$httpdoc = new component_GroupMailing;
		break;
		
	case 'claims':
		$httpdoc = new component_GroupClaims;
		break;
	
	default:
		$httpdoc = new component_GroupEdit;
}

$Layout->display($httpdoc);