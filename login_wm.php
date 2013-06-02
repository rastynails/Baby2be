<?php
require_once './internals/config.php';

require_once './internals/Header.inc.php';

$u = $_GET['username'];
$p = $_GET['password'];

if($_POST['with'])
{
    $is_speed_dating = false;
    if( !empty($_SESSION['speed_dating_event']['opponent_id']) )
    {
        $esd_username = app_Profile::getFieldValues($_SESSION['speed_dating_event']['opponent_id'], 'username');
        if ( strtolower($esd_username ) == strtolower($_POST['with']) )
        {
            $is_speed_dating = true;
        }
    }

    if (!$is_speed_dating)
    {
        $service = new SK_Service('initiate_im_session');

        if ($service->checkPermissions() != SK_Service::SERVICE_FULL ){
            exit( "{allowed: 0, alert: '{$service->permission_message['alert']}' }" );
        }
    }

	exit( "{allowed: 1, username: '{$_POST['with']}'}" );
}

$query = sql_placeholder("SELECT IF( `password`= '$p', 1, 0 ) FROM `?#TBL_PROFILE` WHERE `username` = ? LIMIT 1", $u, $p);

$value = MySQL::fetchField($query);

if($value === false){
	exit('4'); // 4 - username doesn't exist
}
else if($value == 0){
	exit('1'); // 1 - wrong pass
}

exit('0'); // 0 - successfull authentication
?>
