<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$defaultRoomNames = null; // TODO: fetch from DB
$defaultRoomCapacities = null; // TODO: fetch from DB

$gameRoomNames = null; // TODO: fetch from DB
$gameRoomCapacities = null; // TODO: fetch from DB

$controlUsername = SK_Config::section('site')->Section('admin')->admin_username;
$controlPassword = SK_Config::section('site')->Section('admin')->admin_password;

$hashKey = "Bm5rqt8UDbN3kWhIrbDkZKliPioHfR8C";
$extraKeyLength = 16;

$filesPath = DIR_USERFILES."lobbyFiles";

$inactiveTimeout = 30;

$emailSubject = "Challenge to play GAMENAME"; // TODO: fetch from DB
// TODO: fetch from DB
$emailContent = "Dear FRIENDNAME,\n\n\tI want to challenge you to play GAMENAME with me, I bet you cannot beat me in this game. You can play the game here:\n\nURL\n\nBest Regards,\n\nOWNNAME";
$emailDefaultURL = "";

