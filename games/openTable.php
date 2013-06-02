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

$possibleNoOfPlayers = explode(",", $parameters['possibleNoOfPlayers']);


if(!isset($siteID) || !isset($gameID) || !isset($roomID) || !isset($playerUID)) {
	echo '<INVALIDINPUT />';
	exit;
}


$room = new Room($siteID, $gameID, $roomID);

$room->lock();

$room->loadInfo();


$player = $room->getPlayer($playerUID);

if($player == null) {
	echo '<DISCONNECTED />';

	$room->unlock();

	exit;
}

$player->lockData();
$player->updateLastActivityTime();
$player->saveInfo();
$player->unlockData();


$room->removeInactivePlayers();


$table = $room->openTable($player, $possibleNoOfPlayers);

if($table != null) {
	$player->sendMessage('<OPENTABLERESULT success="true" tableUID="' . $table->tableUID . '" />');
} else {
	$player->sendMessage('<OPENTABLERESULT success="false" />');
}


$room->unlock();


echo ' ';
?>