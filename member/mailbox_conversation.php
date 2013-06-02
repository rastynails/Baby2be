<?php

require_once '../internals/Header.inc.php';

$Layout = SK_Layout::getInstance();

$MailboxConversation = new component_MailboxConversation();

$Layout->display($MailboxConversation);
