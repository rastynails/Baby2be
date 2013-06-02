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


if(!isset($playerUID) || !isset($playerUID) || !isset($roomID) || !isset($playerUID)) {
	echo '<INVALIDINPUT />';
	exit;
}


$room = new Room($siteID, $gameID, $roomID);

$room->lock();

$room->loadInfo();

$player = $room->getPlayer($playerUID);
if($player) $player->lastActivityTime = 0;

$room->removeInactivePlayers();

$room->unlock();


echo ' ';
?>