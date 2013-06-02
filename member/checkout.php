<?php
require_once '../internals/Header.inc.php';
$Layout = SK_Layout::getInstance();

$Checkout = new httpdoc_CheckoutExtra();

$Layout->display($Checkout);
