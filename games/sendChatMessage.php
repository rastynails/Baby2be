<?php
require_once('config.php');
require_once('common.php');
require_once('Room.php');
require_once('Player.php');
require_once('Table.php');


$parameters = parseParameters();

if($parameters == null) {
	echo '<DISCONNECTED />';
	exit;
}


$siteID = $parameters['siteID'];
$gameID = $parameters['gameID'];
$roomID = $parameters['roomID'];
$playerUID = $parameters['playerUID'];
$tableUID = $parameters['tableUID'];

$message = $parameters['message'];


if(!isset($siteID) || !isset($gameID) || !isset($roomID) || !isset($playerUID) || !isset($tableUID) || !isset($message)) {
	echo '<INVALIDINPUT />';
	exit;
}


$player = new Player($siteID, $gameID, $roomID, $playerUID);

if($player->lockData()) {
	$player->loadInfo();
	$player->updateLastActivityTime();
	$player->saveInfo();
	$player->unlockData();
} else {
	echo '<DISCONNECTED />';
	exit;
}


$table = new Table($siteID, $gameID, $roomID, $tableUID);

$table->lockData();
$table->loadInfo();

$table->sendChatMessage($message, $playerUID);

$table->unlockData();


echo ' ';
?>