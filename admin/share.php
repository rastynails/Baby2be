<?php
$file_key = 'share';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

$active_tab = $share = in_array( @$_GET['share'], array( 'facebook_share', 'twitter_share', 'google_share' ) ) ? $_GET['share'] : 'facebook_share';

$share_settings = adminConfig::ConfigList( 'share.'.$share );

if ( $_POST['share'] )
{
    $config = SK_Config::section( 'share.'.$share );
    
    switch( $_POST['share'] )
    {
        case 'facebook_share':
            try
            {
                $config->set( 'enabled', $_POST['enabled'] );
                $config->set( 'app_id', $_POST['app_id'] );
                $config->set( 'send_button', $_POST['send_button'] );
                $config->set( 'layout_style', $_POST['layout_style'] );
                $config->set( 'width', $_POST['width'] );
                $config->set( 'show_faces', $_POST['show_faces'] );
                $config->set( 'verb_to_display', $_POST['verb_to_display'] );
                $config->set( 'color_scheme', $_POST['color_scheme'] );
                $config->set( 'font', $_POST['font'] );
                $frontend->registerMessage( 'Settings saved!' );
            }
            catch( Exception $e )
            {
                $frontend->RegisterMessage( 'Empty data', 'error' );
            }

            redirect( $_SERVER['REQUEST_URI'] );
            break;
            
        case 'twitter_share':
            try
            {
                $config->set( 'enabled', $_POST['enabled'] );
                $config->set( 'show_count', $_POST['show_count'] );
                $config->set( 'large_button', $_POST['large_button'] );
                $config->set( 'opt_out', $_POST['opt_out'] );
                $frontend->registerMessage( 'Settings saved!' );
            }
            catch( Exception $e )
            {
                $frontend->RegisterMessage( 'Empty data', 'error' );
            }
            
            redirect( $_SERVER['REQUEST_URI'] );
            break;
        
        case 'google_share':
            try
            {
                $config->set( 'enabled', $_POST['enabled'] );
                $config->set( 'size', $_POST['size'] );
                $config->set( 'annotation', $_POST['annotation'] );
                $config->set( 'width', $_POST['width'] );
                $frontend->registerMessage( 'Settings saved!' );
            }
            catch( Exception $e )
            {
                $frontend->RegisterMessage( 'Empty data', 'error' );
            }
            
            redirect( $_SERVER['REQUEST_URI'] );
            break;
    }
}

require_once( 'inc.admin_menu.php' );

$frontend->assign( 'share', $share );


switch( $share )
{
    case 'facebook_share':
        $frontend->assign( 'enabled', $share_settings['enabled']['value'] );
        $frontend->assign( 'app_id', $share_settings['app_id']['value'] );
        $frontend->assign( 'send_button', $share_settings['send_button']['value'] );
        $frontend->assign( 'layout_style', $share_settings['layout_style'] );
        $frontend->assign( 'width', $share_settings['width']['value'] );
        $frontend->assign( 'show_faces', $share_settings['show_faces']['value'] );
        $frontend->assign( 'verb_to_dislpay', $share_settings['verb_to_display'] );
        $frontend->assign( 'color_scheme', $share_settings['color_scheme'] );
        $frontend->assign( 'font', $share_settings['font'] );
        break;

    case 'twitter_share':
        $frontend->assign( 'enabled', $share_settings['enabled']['value'] );
        $frontend->assign( 'show_count', $share_settings['show_count']['value'] );
        $frontend->assign( 'large_button', $share_settings['large_button']['value'] );
        $frontend->assign( 'opt_out', $share_settings['opt_out']['value'] );
        break;
    
    case 'google_share':
        $frontend->assign( 'enabled', $share_settings['enabled']['value'] );
        $frontend->assign( 'size', $share_settings['size'] );
        $frontend->assign( 'annotation', $share_settings['annotation'] );
        $frontend->assign( 'width', $share_settings['width']['value'] );
}

$_page['title'] = "Share";

$frontend->display( 'share.html' );