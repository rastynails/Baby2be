<?php
require_once '../internals/Header.inc.php';
$Layout = SK_Layout::getInstance();

$PaymentSelection = new component_PaymentSelection();

$Layout->display($PaymentSelection);
