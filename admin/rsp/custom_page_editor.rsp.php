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

if ( $_POST )
{
    switch ( $_POST['command'] )
    {
        case 'saveDocumentCustomPage':
            $query = SK_MySQL::placeholder('
UPDATE `' . TBL_LANG_VALUE . '`
SET `value` = "?" WHERE `lang_key_id` = ? AND `lang_id` = ?', json_decode($_POST['data']), $_POST['lang_key_id'], $_POST['lang_id'] );

            SK_MySQL::query( $query );
            break;
    }
}
