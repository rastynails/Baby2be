<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$service_key = isset($_GET['service_key']) ? $_GET['service_key'] : (isset($_POST['service_key']) ? $_POST['service_key'] : ''); 
$custom =      isset($_GET['custom']) ? $_GET['custom'] : (isset($_POST['custom']) ? $_POST['custom'] : '');

if ( !strlen($service_key) || !strlen($custom) )
{
    exit("Incorrect parameters passed");
}

$Layout = SK_Layout::getInstance();

$httpdoc = new component_sms_ZaypayWidget(array('service_key' => $service_key, 'custom' => $custom));

$Layout->display($httpdoc);
