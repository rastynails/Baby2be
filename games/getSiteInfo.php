<?php
require_once('config.php');
require_once('common.php');
require_once('Site.php');
require_once('Game.php');
require_once('Room.php');
require_once('Player.php');
require_once('Table.php');


$siteID = $_POST['siteID'];


if(!isset($siteID)) exit;


$site = new Site($siteID);
$site->readGames();
$site->removeInactivePlayers();


echo '<SITEINFO>';

for($i=0;$i<count($site->games);$i++) {
	echo '<GAMEINFO gameID="' . $site->games[$i]->gameID . '" noOfPlayers="' . $site->games[$i]->getNoOfPlayers() . '" />';
}

echo '</SITEINFO>';

?>