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

if ( !empty($_POST['menuList']) )
{
    foreach ( $_POST['menuList'] as $menu )
    {
        $query = SK_MySQL::placeholder( 'UPDATE `' . TBL_MENU_ITEM . '` 
            SET `menu_id` = ?, `parent_menu_item_id` = ?, `order` = ? WHERE `menu_item_id` = ?', 
            $menu['menu_id'], $menu['parent_menu_item_id'], $menu['order'], $menu['menu_item_id'] );

        SK_MySQL::query( $query );
    }
}
