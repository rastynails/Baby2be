<?php

$file_key = 'security';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC . 'inc.auth.php' );
require_once( DIR_ADMIN_INC . 'class.admin_frontend.php' );

$frontend = new AdminFrontend();

require_once( DIR_ADMIN_INC.'fnc.blocked_ip.php' );
require_once( DIR_ADMIN_INC.'fnc.profile_list.php' );

switch ( @$_GET['unit'] )
{
    case 'country':
        $unit = 'country';
        $active_tab = 'country';
        $_page['title'] = "Country Restrictions";
        
        $countries = array();

        $resource = SK_MySQL::query( '
        SELECT `country`.`Country_str_name` AS `name`, `country`.`Country_str_ISO3166_2char_code` AS `code`, `ac`.`Country_str_ISO3166_2char_code` AS `code2`
        FROM `' . TBL_LOCATION_COUNTRY . '` AS `country`
            LEFT JOIN `' . TBL_SECURITY_COUNTRIES . '` AS `ac` ON(`country`.`Country_str_ISO3166_2char_code` = `ac`.`Country_str_ISO3166_2char_code`)
        ORDER BY 1' );

        while ( $row = $resource->fetch_assoc() )
        {
            empty( $row['code2'] ) ? $countries['white'][] = $row : $countries['black'][] = $row;
        }

        $frontend->registerOnloadJS('securityCountry.getInstance().init()');
        $frontend->assign( 'countries', $countries );
        
        break;
//    case 'search':
//        if ( !empty($_POST['action']) )
//        {
//            switch ( $_POST['action'] )
//            {
//                case 'delete_ip':
//                    if ( !empty($_POST['ip_change']) )
//                    {
//                        switch ( $_POST['listType'] )
//                        {
//                            case 1:
//                                SK_MySQL::query( SK_MySQL::placeholder('
//                                    UPDATE `' . TBL_SECURITY_IP_LIST . '`
//                                    SET `listType` = 0
//                                    WHERE `ip` = "?"', $_POST['ip_value']) );
//
//                                $frontend->registerMessage('Ip address is added to white list!');
//                                break;
//                            case 0:
//                                SK_MySQL::query( SK_MySQL::placeholder('
//                                    UPDATE `' . TBL_SECURITY_IP_LIST . '`
//                                    SET `listType` = 1
//                                    WHERE `ip` = "?"', $_POST['ip_value']) );
//
//                                $frontend->registerMessage('IP address is moved to black list!');
//                                break;
//                        }
//                    }
//                    elseif ( !empty($_POST['delete_ip']) )
//                    {
//                        SK_MySQL::query( SK_MySQL::placeholder('
//                            DELETE FROM `' . TBL_SECURITY_IP_LIST . '`
//                            WHERE `ip` = "?"', $_POST['ip_value']) );
//
//                        $frontend->registerMessage( 'IP address is successfully removed!') ;
//                    }
//                    break;
//                case 'add_ip':
//                    if ( !filter_var( $_POST['ip_address'], FILTER_VALIDATE_IP) )
//                    {
//                        $frontend->registerMessage( 'Not valid IP address!', 'error');
//                    }
//                    else
//                    {
//                        $isExist = SK_MySQL::query('
//                            SELECT COUNT(*)
//                            FROM `' . TBL_SECURITY_IP_LIST . '`
//                            WHERE `ip` = "' . $_POST['ip_address'] . '"')->fetch_cell();
//
//                        if ( !empty($isExist) )
//                        {
//                            $frontend->registerMessage( 'Ip address already exist in database!');
//                        }
//                        else
//                        {
//                            SK_MySQL::query( SK_MySQL::placeholder('
//                                INSERT IGNORE INTO `' . TBL_SECURITY_IP_LIST . '`
//                                VALUES("?", ?)', $_POST['ip_address'], app_Security::WHITE_LIST) );
//
//                            $frontend->registerMessage( 'Ip address is added to white list!');
//                        }
//                    }
//                    break;
//                case 'empty_list':
//                    SK_MySQL::query( '
//                        DELETE FROM `' . TBL_SECURITY_IP_LIST . '`
//                        WHERE `listType` = ' . app_Security::WHITE_LIST );
//                    $frontend->registerMessage( 'List is empty!');
//                    break;
//            }
//        }
//
//        $unit = 'search';
//        $file_key = 'security';
//        $active_tab = 'search';
//        $_page['title'] = "Ip/E-mail list";
//        $frontend->registerOnloadJS('securitySearch.getInstance().init()');
//        break;
    case 'status':
        $unit = 'status';        
        $active_tab = 'status';
        $_page['title'] = 'Status';
        $frontend->assign( 'status', adminConfig::ConfigList('security') );
        break;
    case 'blocked_ip':
        $unit = 'blocked_ip';
        $active_tab = 'blocked_ip';
        $_page['title'] = "Blocked IP";
        break;
    default :
        $unit = 'security';
        $active_tab = 'security';
        $_page['title'] = "Site security";
        break;
}

if ( isset($_POST['search_ip']) )
{
    $find_ip = searchBlockedIp( $_POST['search_pattern'] );

    if ( $find_ip )
    {
        $frontend->registerMessage( 'IP was found' );
        $frontend->assign( 'find_ip', $find_ip );
    }
    else
    {
        $frontend->registerMessage( 'Specified IP not found', 'notice' );
        redirect( URL_ADMIN . 'security.php?unit=blocked_ip' );
    }
}

if ( isset($_GET['delete_ip']) )
{
    controlAdminGETActions();

    if ( deleteBlockedIp( $_GET['delete_ip'] ) )
    {
        $frontend->registerMessage( 'IP was deleted' );
    }
    else
    {
        $frontend->registerMessage( 'IP was not deleted', 'notice' );
    }
    redirect( URL_ADMIN . 'security.php?unit=blocked_ip' );
}

if ( isset($_POST['add_ip']) )
{
    switch ( @addBlockedIp( $_POST['add_pattern'] ) )
    {
        case -1:
            $frontend->registerMessage( 'Please, specify IP', 'error' );
            break;
        case -2:
            $frontend->registerMessage( 'Specified IP already exists', 'notice' );
            break;
        case 1:
            $frontend->registerMessage( 'IP was added' );
            break;
    }
    redirect( URL_ADMIN . 'security.php?unit=blocked_ip' );
}

require_once( DIR_ADMIN . 'inc.admin_menu.php' );
adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend, false);
$frontend->assign( 'unit', $unit );
$frontend->IncludeJsFile( URL_ADMIN_JS . 'security.js' );

$frontend->includeCSSFile( URL_ADMIN_CSS . 'langs.css' );

$frontend->display( 'security.html' );