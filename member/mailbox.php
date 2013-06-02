<?php

require_once '../internals/Header.inc.php';

$Layout = SK_Layout::getInstance();

$mailbox = new component_MailboxConversationsList;

$Layout->display($mailbox);
