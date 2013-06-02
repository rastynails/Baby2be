<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$chuppo_chat = SK_Config::section('chuppo')->get('enable_chuppo_chat');
$flash_chat = SK_Config::section('flash_123_chat')->get('enable_123_chat');

if ( $flash_chat ) {
	$httpdoc_class = '123Chat';
}
elseif ( $chuppo_chat ) {
	$httpdoc_class = 'ChuppoChat';
}
else {
	$httpdoc_class = 'Chat';
}

$httpdoc = SK_Component($httpdoc_class);

SK_Layout::getInstance()->display($httpdoc);