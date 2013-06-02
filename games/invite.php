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

$invitedPlayerUID = $parameters['invitedPlayerUID'];


if(!isset($siteID) || !isset($gameID) || !isset($roomID) || !isset($playerUID) || !isset($invitedPlayerUID)) {
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


$success = $room->playerInvitesPlayer($player, $invitedPlayerUID);

if($success) {
	$player->sendMessage('<INVITERESULT success="true" />');
} else {
	$player->sendMessage('<INVITERESULT success="false" />');
}


$room->unlock();


echo ' ';
?>