<?php
error_reporting(0);

if(!$_SESSION)
	session_start();

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
				WHERE username='?'", $_SESSION['%http_user%'][username]
		);

$result = SK_MySQL::query($query);

$user_data = $result->fetch_assoc();

if($user_data['username']!=''){
$user = "&init_user=" . rawurlencode ( $user_data['username'] ) . "&init_password=" . md5 ( $user_data['password']);
}

$config = str_replace('{"','', $config);
$config = str_replace('"}','', $config);
$config = split('","', $config);
foreach($config as $val)
{
	$val = split('":"', $val);
	$$val[0] = $val[1];

}

$extendserver = $fc_running_mode?$fc_running_mode:0;
$clientlocation = checkSlash($fc_client_location);
$chatroomname = $fc_room_name;
$chatwidth = '100%';
$chatheight = '100%';



if($extendserver == 2)
{
	$urlinfo = getHostParameters($clientlocation);
	$swfname = basename($urlinfo['path']);
	parse_str($urlinfo['query']);
}



switch($extendserver)
{
	case '0':
		chat_free(rawurlencode ( $user_data['username'] ));
		break;
	case '1':
		show_chat();
		break;
	case '2':
		show_chat();
		break;
	default:
	     echo '<script>window.location.href("index.php");</script>';
		 exit;break;
}

function chat_free($user)
{
	global $chatroomname;

	$chatroomname = empty($chatroomname)?$_SERVER['HTTP_HOST']:$chatroomname;
	$user = empty($user)?'':'&user='.$user;

	echo pageheader();
	echo '<!-- FROM 123FLASHCHAT CODE BEGIN -->

<div id="topcmm_123flashchat" style="width:728px;height:20;margin:0">
<table width="728">
<tr>
<td  class="menu" align="right"><a href="http://www.123flashchat.com/" target="_blank">flash chat</a> |
<a href="http://www.123flashchat.com/" target="_blank">chat software</a>
</tr></table>
</div>
<script language="javascript" src="http://free.123flashchat.com/js.php?room='.rawurlencode($chatroomname).$user.'&width=728&height=500"></script>

	<!-- 123FLASHCHAT CHAT ROOM CODE END -->';
	echo pagefooter();
}



function show_chat()
{
	global $swfname,$init_host,$init_port,$init_group,$chatwidth,$chatheight,$clientlocation,$user,$extendserver;


	if($extendserver == 2)
	{
		$client_location = checkSlash($host_address);

		$swfurl = $clientlocation.$swfname;

		if(!empty($init_host)){
			$swfurl .= (strpos($swfurl,"?"))?"&init_host=".$init_host:"?init_host=".$init_host;
		}
		if(!empty($init_port)){
			$swfurl .= (strpos($swfurl,"?"))?"&init_port=".$init_port:"?init_port=".$init_port;
		}
		if(!empty($init_group)){
			$swfurl .= (strpos($swfurl,"?"))?"&init_group=".$init_group:"?init_host=".$init_group;
		}

		}else{

			$swfurl = $clientlocation.'123flashchat.swf?init_group=default';

     	}


		$swfurl .= $user;

		echo pageheader();
		echo '<!-- FROM 123FLASHCHAT CODE BEGIN --><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="100%" height="100%">';
		echo '<param name=movie value="'.$swfurl.'">';
		echo '<param name=quality value=high>' ;
		echo '<param name=menu value=false>';
		echo '<param name=scale value=noscale>';
		echo '<param name="allowScriptAccess" value="always" />';
		echo '<embed src="'.$swfurl.'" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="100%" height="100%" menu="false" scale="noscale" allowScriptAccess="always">';
		echo '</embed>';
		echo '</object><!-- 123FLASHCHAT CODE END -->';
		echo pagefooter();
	}

function getHostParameters($client_location)
{
	$content = @file_get_contents($client_location);
	if(!empty($content))
	{
		$pattern = '|var urlValue="(.*)"|U';
		preg_match($pattern, $content, $matches);
		if(!empty($matches[1]))
		{
			$url = $matches[1];
			$urlinfo = parse_url($url);
			return $urlinfo;
		}
		else
		{
			$pattern = '|PARAM NAME=movie VALUE="(.*)"|U';
			preg_match($pattern, $content, $matches);
			if(!empty($matches[1]))
			{
				$url = $matches[1];
				$urlinfo = parse_url($url);
				return $urlinfo;
			}
		}
		return false;
	}
}


function checkSlash($path)
{
		if(substr($path,-1,1) != "/" && !empty($path)){
			$path = $path."/";
		}
		return $path;
}

function pageheader()
{
      global $running_mode;
		echo '<html>';
		echo '<head>';
		echo '<title>Chat Room - Powered by 123FlashChat</title>';
		echo '</head>';
		if($running_mode=='free')
		echo '<link rel="stylesheet" type="text/css" media="screen" href="http://www.123flashchat.com/stylesheet22.css" />';
		echo '<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" scroll="no">';
}

function pagefooter()
{
		echo '</body>';
		echo '</html>';
}
?>