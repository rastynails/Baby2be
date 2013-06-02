<?php

$file_key = 'user_points';
$active_tab = 'point_packages';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( isset($_POST['delete_packages']) )
    $_POST['command'] = 'delete_packages';
    
if ( isset($_POST['update_packages']) )
    $_POST['command'] = 'update_packages';    

    
if ( isset($_POST['command']) )
{    
    switch ( $_POST['command'] )
    {
        case 'add_package':
            $lang_id = SK_Language::current_lang_id();
            if ( empty($_POST['package_desc'][$lang_id]) )
            {
                $frontend->registerMessage('Package description is required', 'notice');
                redirect( $_SERVER['REQUEST_URI'] );
            }
            
            $add = app_UserPoints::addPackage($_POST['price'], $_POST['points'], $_POST['package_desc']);            
            
            if ( $add )
            {
                $frontend->registerMessage('User Credits package added');
            }
            else
                $frontend->registerMessage('Package was not added, flease fill all required fields', 'notice');
            
            component_PointsPurchase::clearCompile();
            
            redirect( $_SERVER['REQUEST_URI'] );
            
            break;
            
        case 'delete_packages':
            
            if ( count($_POST['packages_arr']) )
            {
                $del = app_UserPoints::deletePackages($_POST['packages_arr']);
                
                if ( $del )
                {
                    $frontend->registerMessage('User credits packages have been deleted');
                }
                else
                    $frontend->registerMessage('No packages deleted', 'notice');
            }
            else 
                $frontend->registerMessage('No packages selected', 'notice');

            component_PointsPurchase::clearCompile();
            
            redirect( $_SERVER['REQUEST_URI'] );

            break;
            
        case 'update_packages':

            if ( count($_POST['packages_arr']) )
            {
                $packages = array();
                $i = 0;
                
                foreach ( $_POST['packages_arr'] as $pack_id )
                {
                    $packages[$i]['package_id'] = $pack_id;
                    $packages[$i]['price'] = $_POST['prices'][$pack_id];
                    $packages[$i]['points'] = $_POST['points'][$pack_id];
                    $i++;
                }
                
                if ( $i > 0 )
                {
                    $updated = app_UserPoints::updatePackages($packages);                    
                }
                
                if ( $updated )
                {
                    $frontend->registerMessage('User credits packages have been updated');
                }
                else
                    $frontend->registerMessage('No packages updated', 'notice');
            }
            else 
                $frontend->registerMessage('No packages selected', 'notice');

            component_PointsPurchase::clearCompile();
            
            redirect( $_SERVER['REQUEST_URI'] );
            
            break;
    }
}

$packages = app_UserPoints::getPackages();
$frontend->assign('packages', $packages);

$frontend->assign('curr', SK_Language::text('%label.currency_sign'));

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );

$template = 'point_packages.html';

$_page['title'] = 'User Credits Packages';

$frontend->display( $template );