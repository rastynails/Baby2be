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


if(!isset($siteID) || !isset($gameID) || !isset($roomID) || !isset($playerUID) || !isset($tableUID)) {
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


$success = $room->joinTable($player, $tableUID);

if($success) {
	$player->sendMessage('<JOINTABLERESULT success="true" />');
} else {
	$player->sendMessage('<JOINTABLERESULT success="false" reason="full" />');
}


$room->unlock();


echo ' ';
?>