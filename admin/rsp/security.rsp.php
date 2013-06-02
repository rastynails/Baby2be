<?php

if ( empty($_SERVER['HTTP_X_REQUESTED_WITH']) )
{
    exit();
}

require_once( '../../internals/Header.inc.php' );
require_once( DIR_ADMIN_INC.'inc.auth.php' );

if ( defined('SK_DEMO_MODE') && SK_DEMO_MODE && !isAdminAuthed(false) )
{
    exit();
}

if ( !empty($_POST) )
{
    $data = json_decode( $_POST['data'] );

    switch ( $_POST['command'] )
    {
        case 'countriesListSave':
            
            if ( empty($data->blackList) )
            {
                SK_MySQL::query( 'TRUNCATE `' . TBL_SECURITY_COUNTRIES . '`;' );
            }
            else
            {
                SK_MySQL::query( 'DELETE FROM `' . TBL_SECURITY_COUNTRIES . '` WHERE `Country_str_ISO3166_2char_code` IN ("' . implode('","', array_map('mysql_real_escape_string', $data->whiteList)) . '");' );
                SK_MySQL::query( 'INSERT IGNORE INTO `' . TBL_SECURITY_COUNTRIES . '` VALUES("' . implode( '"),("', array_map('mysql_real_escape_string', $data->blackList)) . '");' );
            }
            break;
        case 'searchIP':
            if ( empty($data) )
            {
                exit( json_encode(array()) );
            }

            exit( json_encode(SK_MySQL::query(SK_MySQL::placeholder('
                SELECT *
                FROM `' . TBL_SECURITY_IP_LIST . '` WHERE `ip` = "?"', $data->ip))->fetch_assoc()) );
            break;
    }
}
