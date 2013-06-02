<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

/*
implement this function to record the result if needed
*/
function recordResult($siteID, $gameID, $roomID, $tableUID, $playerNames, $playerEmails, $playerIDs, $playerUsernames, $ranks, $playerIndex)
{
    $query = MySQL::placeholder( "SELECT `id` FROM `".TBL_NOVEL_GAME_RESULTS."` WHERE `siteID`=? AND `gameID`=? AND `roomID`=? AND `tableUID`='?'", $siteID, $gameID, $roomID, $tableUID);

    $result = MySQL::fetchField($query);

    if (!$result)
    {
        foreach ($playerNames as $id=>$playerName)
        {
            $query = MySQL::placeholder( "INSERT INTO `".TBL_NOVEL_GAME_RESULTS."` ( `siteID`, `gameID`, `roomID`, `tableUID`, `playerName`, `playerEmail`, `playerID`, `playerUsername`, `rank` )
                VALUES (?, ?, ?, '?', '?', '?', ?, '?', ?)", $siteID, $gameID, $roomID, $tableUID, $playerName, $playerEmails[$id], $playerIDs[$id], $playerUsernames[$id], $ranks[$id]);
            MySQL::query($query);
        }
    }
    else
    {
        foreach ($playerNames as $id=>$playerName)
        {
            $query = MySQL::placeholder( "UPDATE `".TBL_NOVEL_GAME_RESULTS."` SET `rank`=? WHERE `playerName`='?' ", $ranks[$id], $playerName);
            MySQL::query($query);
        }
    }
    
}


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

$ranks = explode(',', $parameters['ranks']);


if(!isset($siteID) || !isset($gameID) || !isset($roomID) || !isset($playerUID) || !isset($tableUID)) {
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

if($table->lockData()) {
	$table->loadInfo();
	$table->unlockData();
	
	for($i=0;$i<count($table->playingPlayerInfos);$i++) {
		$playerNames[] = $table->playingPlayerInfos[$i]->playerName;
		$playerEmails[] = $table->playingPlayerInfos[$i]->playerEmail;
		$playerIDs[] = $table->playingPlayerInfos[$i]->playerID;
		$playerUsernames[] = $table->playingPlayerInfos[$i]->playerUsername;
		
		if($table->playingPlayerInfos[$i]->playerUID == $playerUID) $playerIndex = $i;
	}
	
	recordResult($siteID, $gameID, $roomID, $tableUID, $playerNames, $playerEmails, $playerIDs, $playerUsernames, $ranks, $playerIndex);
	
	$player->sendMessage('<GAMEENDEDRESULT success="true" />');
} else {
	$player->sendMessage('<GAMEENDEDRESULT success="false" />');
}


echo ' ';
?>