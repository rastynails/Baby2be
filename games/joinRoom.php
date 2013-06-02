<?php

/*
implement this function if login is needed

return null if login failed
return the player ID and player name if succeed
*/
function checkLogin($username, $password) {
	$result = new stdClass();
	
	$result->playerID = '1';
	$result->playerName = $username;
//	$result->playerID = SK_HttpUser::profile_id();
//	$result->playerName = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'username');
	
	return $result;
}


require_once('config.php');
require_once('common.php');
require_once('Room.php');
require_once('Player.php');
require_once('Table.php');


$parameters = parseParameters();


$siteID = $parameters['siteID'];
$gameID = $parameters['gameID'];
$roomID = $parameters['roomID'];

$playerID = $parameters['playerID'];
$playerName = $parameters['playerName'];
$playerEmail = $parameters['playerEmail'];
$playerUsername = $parameters['playerUsername'];
$playerPassword = $parameters['playerPassword'];

$isRobot = $parameters['isRobot'] == 'true';


if(!isset($siteID) || !isset($gameID)) {
	echo '<JOINROOMRESULT success="false" />';
	exit;
}

if(!isset($roomID)) $roomID = 1;


if(isset($playerUsername) && !isset($playerID)) {
	$result = checkLogin($playerUsername, $playerPassword);
	
	if($result == null) {
		echo '<JOINROOMRESULT success="false" reason="loginFailed" />';
		exit;
	}
	
	$playerID = $result->playerID;
	$playerName = $result->playerName;
}


if(file_exists('obscene.txt')) {
	$nameMerged = preg_replace('/\s+/', '', $playerName);
	$obsceneWords = file('obscene.txt');
	$obsceneWordsCount = count($obsceneWords);

	for($i=0;$i<$obsceneWordsCount;$i++) {
		$obsceneWord = trim($obsceneWords[$i]);
		if($obsceneWord == '') continue;
		
		if(stristr($nameMerged, trim($obsceneWords[$i])) !== FALSE) {
			echo '<JOINROOMRESULT success="false" reason="nameRejected" />';
			exit;
		}
	}
}


$room = new Room($siteID, $gameID, $roomID);

$room->lock();

$room->loadInfo();
$room->removeInactivePlayers();

if($room->checkIsFull()) {
	$room->unlock();
	
	echo '<JOINROOMRESULT success="false" reason="roomFull" />';
	exit;
}

$player = $room->playerJoined($playerName, $isRobot);

$room->unlock();


echo '<JOINROOMRESULT success="true" playerUID="' . $player->playerUID . '" playerNameInput="' . htmlspecialchars($playerName) . '" playerName="' . htmlspecialchars($player->playerName) . '" extraKey="' . $player->extraKey . '" playerID="' . (isset($playerID) ? htmlspecialchars($playerID) : '') . '">';

for($i=0;$i<count($room->players);$i++) {
	echo '<PLAYERINFO playerUID="' . $room->players[$i]->playerUID . '" playerName="' . htmlspecialchars($room->players[$i]->playerName) . '" isRobot="' . ($room->players[$i]->isRobot ? 'true' : 'false') . '" />';
}

for($i=0;$i<count($room->tables);$i++) {
	echo '<TABLEINFO tableUID="' . $room->tables[$i]->tableUID . '" possibleNoOfPlayers="' . implode(',', $room->tables[$i]->possibleNoOfPlayers) . '" playerUIDs="' . implode(',', $room->tables[$i]->playerUIDs) . '" viewerUIDs="' . implode(',', $room->tables[$i]->viewerUIDs) . '" isPlaying="' . ($room->tables[$i]->isPlaying ? 'true' : 'false') . '" />';
}

echo '</JOINROOMRESULT>';

?>