<?php

define('DIR_INTERNALS', '../internals/');

include_once    '../internals/config.php';
include_once '../internals/db_tbl.const.php';
include_once '../internals/system.const.php';

include_once '../internals/API/MySQL.class.php';

SK_MySQL::connect();

include_once '../internals/API/Layout.class.php';
include_once '../internals/API/Config.class.php';
include_once "./123flashchat_config.php";

$query = SK_MySQL::placeholder(
			"SELECT `profile_id`,`username`,`password`
				FROM `".TBL_PROFILE."`
				WHERE username='?'", mysql_real_escape_string($_GET['username'])
		);


/**
 * Don't modify the code below unless you know what are you doing.
 */

// Define the output result
$LOGIN_SUCCESS = 0;
$LOGIN_PASSWD_ERROR = 1;
$LOGIN_NICK_EXIST = 2;
$LOGIN_ERROR = 3;
$LOGIN_ERROR_NOUSERID = 4;
$LOGIN_SUCCESS_ADMIN = 5;
$LOGIN_NOT_ALLOW_GUEST = 6;
$LOGIN_USER_BANED = 7;





$username = isset($_GET['username']) ? trim(htmlspecialchars($_GET['username'])) : '';
$username = substr(str_replace("\\'", "'", $username), 0, 25);
$username = str_replace("'", "\\'", $username);
$password = isset($_GET['password']) ? $_GET['password'] : '';
//$md5 = isset($_GET['md5']) ? $_GET['md5'] : '';



if ( !($result = SK_MySQL::query($query)) )
{
	echo $LOGIN_ERROR;
	exit;
}

if( $user_data = $result->fetch_assoc() )
{


	if (md5($user_data['password']) == $password || $user_data['password'] == $password)
	{
		echo $LOGIN_SUCCESS;
		exit;
	}
	else
	{
		echo $LOGIN_PASSWD_ERROR;
		exit;
	}

}
else
{
	echo $LOGIN_ERROR_NOUSERID;
	exit;
}



?>