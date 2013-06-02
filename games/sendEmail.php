<?php
require('config.php');


$gameID = $_POST['gameID'];
$gameName = $_POST['gameName'];
$playerRanks = $_POST['playerRanks'];
$playerSelfIndex = $_POST['playerSelfIndex'];
$url = $_POST['url'];
$ownEmail = $_POST['ownEmail'];
$friendEmails = $_POST['friendEmails'];
$date = $_POST['date'];
$hash = $_POST['hash'];


$stringToHash = $gameID;
$stringToHash .= $gameName;
$stringToHash .= $playerRanks;
$stringToHash .= $playerSelfIndex;
$stringToHash .= $url;
$stringToHash .= $ownEmail;
$stringToHash .= $friendEmails;
$stringToHash .= $date;
$stringToHash .= $hashKey;

if($hash != md5($stringToHash)) {
	echo 'success=false&reason=hashIncorrect';
	exit;
}


if($url == '' || $url == 'null') $url = $emailDefaultURL;


preg_match('/([^<]*)<([^>]*)>/', $ownEmail, $matches);

$fromName = cleanName(trim($matches[1]));
$fromEmail = trim($matches[2]);

if(!checkEmailIsValid($fromEmail)) {
	echo 'success=false&reason=ownEmailInvalid';
	exit;
}


$friendNameEmails = preg_split('/[\r\n]+/', $friendEmails);

for($i=0;$i<count($friendNameEmails);$i++) {
	preg_match('/([^<]*)<([^>]*)>/', $friendNameEmails[$i], $matches);
	
	if(!checkEmailIsValid(trim($matches[2]))) continue;
	
	$toNames[] = cleanName(trim($matches[1]));
	$toEmails[] = trim($matches[2]);
}


for($i=0;$i<count($toEmails);$i++) {
	$theEmailSubject = preg_replace('/FRIENDNAME/', $toNames[$i], $emailSubject);
	$theEmailSubject = preg_replace('/GAMENAME/', $gameName, $theEmailSubject);
	$theEmailSubject = preg_replace('/URL/', $url, $theEmailSubject);
	$theEmailSubject = preg_replace('/OWNNAME/', $fromName, $theEmailSubject);
	$theEmailContent = preg_replace('/FRIENDNAME/', $toNames[$i], $emailContent);
	$theEmailContent = preg_replace('/GAMENAME/', $gameName, $theEmailContent);
	$theEmailContent = preg_replace('/URL/', $url, $theEmailContent);
	$theEmailContent = preg_replace('/OWNNAME/', $fromName, $theEmailContent);

	mail("$toNames[$i] <$toEmails[$i]>", $theEmailSubject, $theEmailContent, "From: $fromName <$fromEmail>\r\nContent-Type:text/plain; charset=\"utf-8\"");
}


function cleanName($name) {
	return preg_replace('/[\r\n]/', ' ', $name);
}

function checkEmailIsValid($email) {
	return preg_match('/[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+\.[0-9a-zA-Z_\-\.]+/', $email) == 1;
}

?>success=true