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
    switch ($_POST['command'] )
    {
        case 'isEnable':
            exit( json_encode(SK_Config::section('languages')->auto_select) );
            break;
        
        case 'enable':
            SK_Config::section('languages')->set('auto_select', (bool)$_POST['checked']);
            break;
        
        case 'loadCountry':
            if ( !SK_Config::section('languages')->auto_select )
            {
                exit();
            }
            
            $query = '
SELECT `country`.`Country_str_ISO3166_2char_code` AS `code`, `lang`.`lang_id`, `country`.`Country_str_name` AS `country`
FROM `' . TBL_LANG_TO_COUNTRY . '` AS `lang`
    RIGHT JOIN `' . TBL_LOCATION_COUNTRY . '` AS `country`
        ON(`lang`.`Country_str_ISO3166_2char_code` = `country`.`Country_str_ISO3166_2char_code`)
WHERE `lang`.`lang_id` IS NULL OR `lang`.`lang_id` = ' . (int)$_POST['lang_id'] . '
ORDER BY `country`.`Country_str_name`';

            $resource = SK_MySQL::query( $query );

            $data = array();

            while ( $row = $resource->fetch_assoc() )
            {
                if ( $row['lang_id'] == $_POST['lang_id'] )
                {
                    $data['fixedCountry'][] = $row;
                }
                elseif ( empty($row['lang_id']) )
                {
                    $data['freeCountry'][] = $row;
                }
            }

            exit( json_encode($data) );
            break;

        case 'save':
            if ( !SK_Config::section('languages')->auto_select )
            {
                exit();
            }

            $data = json_decode( $_POST['data'] );
            
            foreach ( $data->fixedCountry as $code )
            {
                $query = SK_MySQL::placeholder( "REPLACE INTO `".TBL_LANG_TO_COUNTRY."`
                    SET `Country_str_ISO3166_2char_code` = '?', `lang_id` = ?", $code, (int)$data->lang_id );

                SK_MySQL::query( $query );
            }

            foreach ( $data->freeCountry as $code )
            {
                $query = SK_MySQL::placeholder( "REPLACE INTO `".TBL_LANG_TO_COUNTRY."`
                    SET `Country_str_ISO3166_2char_code` = '?', `lang_id` = NULL", $code, (int)$data->lang_id );

                SK_MySQL::query( $query );
            }
            break;
    }
}
