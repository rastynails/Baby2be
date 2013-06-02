<?php

$file_key = 'gifts';
$active_tab = 'categories';

require_once '../internals/Header.inc.php';

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.virtual_gift.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( !empty($_POST['action']) )
{
    switch ( $_POST['action'] )
    {
        case 'add':
            if ( VirtualGift::addCategory($_POST['category']) )
            {
                $frontend->RegisterMessage('Category was added');
            }
            break;
            
        case 'update':
            if ( $_POST['cat_id'] )
            {
                if ( VirtualGift::updateCategory($_POST['cat_id'], $_POST['cat_title']) )
                {
                    $frontend->RegisterMessage('Category was updated');
                }
            }
            break;
    }
    
    redirect($_SERVER['REQUEST_URI']);
}

if ( isset($_GET['move']) )
{
    VirtualGift::moveCategoryOrder($_GET['cat_id'], $_GET['move']);
    
    redirect(URL_ADMIN . 'virtual_gift_categories.php');
}
elseif ( isset($_GET['del_cat_id']) )
{
    if ( VirtualGift::deleteCategory($_GET['del_cat_id']) )
    {
        $frontend->RegisterMessage('Category was deleted');
    }
    
    redirect(URL_ADMIN . 'virtual_gift_categories.php');
}

$categories = VirtualGift::getCategoryList();
$frontend->assign('categories', $categories);

// include js modules
$frontend->IncludeJsFile(URL_ADMIN_JS.'frontend.js');
$frontend->IncludeJsFile(URL_ADMIN_JS.'gifts.js');

$template = 'virtual_gift_categories.html';

$_page['title'] = 'Virtual Gifts';

$frontend->display( $template );
