<?php

$file_key = 'config_media';
$active_tab = 'config_media';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

if ( $_GET['del_cat'] )
{
    app_VideoList::deleteVideoCategory($_GET['del_cat']);
    
    component_VideoEdit::clearCompile();
    component_VideoUpload::clearCompile();
        
    $frontend->registerMessage('Video category has been deleted');
    redirect(URL_ADMIN.'config_media.php');    
}
if ( isset($_POST['action']) && $_POST['action'] == 'add_cat' && isset( $_POST['cat_label']) )
{
    if ( app_VideoList::addVideoCategory($_POST['cat_label']) )
    {
        component_VideoEdit::clearCompile();
        component_VideoUpload::clearCompile();
        
        $frontend->registerMessage('Video category has been added');
        redirect(URL_ADMIN.'config_media.php');
    }
}

if ( $_POST['set_global_media_mode'] ) {
	if (adminConfig::SaveConfig('video','media_mode', $_POST['media_mode'] ))
		$frontend->registerMessage( 'Global video mode was changed' );
	else
		$frontend->registerMessage( 'Global video mode was not changed', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] );
}
elseif( $_POST['set_watermark'] ) {
	if ( adminConfig::SaveConfig('video.watermark', 'enable_video_watermark', $_POST['enable_video_watermark'] ? 1:0 ) )
		$frontend->registerMessage( 'Video watermark config was changed' );
	else 
		$frontend->registerMessage( 'Video watermark config was not changed', 'notice' );
	
	redirect( $_SERVER['REQUEST_URI'] );
}
elseif ( $_POST['upload_videowatermark'] )
{
	if( strlen($_FILES['video_watermark_file']['name']) )
	{
		$rand_index = rand();
		$fileName = DIR_USERFILES.'video_watermark_img_'.$rand_index.'.jpg';
		
		if ( move_uploaded_file($_FILES['video_watermark_file']['tmp_name'], $fileName) )
		{
			adminConfig::SaveConfig('video.watermark', 'watermark_img', $rand_index);
			$frontend->registerMessage( 'Watermark was uploaded to server' );
		}
		else
			$frontend->registerMessage( 'There was an error during upload. Watermark was not uploaded.', 'error' );
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

else 
	{
	adminConfig::SaveConfigs($_POST);
	component_VideoUpload::clearCompile();
	component_VideoEdit::clearCompile();
	adminConfig::getResult($frontend);
}

adminConfig::SaveConfigs($_POST);

adminConfig::getResult($frontend, false);

$global_media_mode = SK_Config::section('video')->get('media_mode');
$frontend->assign_by_ref( 'global_media_mode', $global_media_mode );

if ( $global_media_mode == 'flash_video' )
	$frontend->assign('watermark_conf', adminConfig::ConfigList('video.watermark'));

$catList = app_VideoList::getVideoCategories(false);
$frontend->assign_by_ref('categories', $catList);
$enableCat = SK_Config::section('video')->Section('other_settings')->get('enable_categories');
$frontend->assign_by_ref('enable_categories', $enableCat);
	
// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

// register meta tags
$frontend->RegisterMetaTags( array( 'http_equiv' => 'pragma', 'content' => 'no-cache' ) );

$template = 'config_media.html';

$_page['title'] = "Video Configuration";

// display template
$frontend->display( $template );
?>
