<?php

$file_key = 'config_photo';
$active_tab = 'config_photo';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );


$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

// require fnc file
require_once(  DIR_ADMIN_INC . 'fnc.config_photo.php' );

if (isset($_GET["c"])) {
	require_once(DIR_ADMIN_INC . "fnc.config_photo.php");
	cron_RedrawPhotos();
}

// send header for clearing any image chache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
header( 'Pragma: no-cache' );

// alert if gd not installed
if ( !app_Image::getGdVersion() )
	$frontend->RegisterMessage( "Your server doesn't have GD Library installed", 'error' );


adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend, false);

component_UploadPhoto::clearCompile();
	
$image_configs = adminConfig::ConfigList('photo.general');


$max_height = (int) $image_configs['max_height']['value'];
$max_width = (int) $image_configs['max_width']['value'];
$miscalculation = 3.5;
$proccess_memory = (int) (($max_height * $max_width * 3 * $miscalculation) / (1024 * 1024));
$server_mem_limit = app_Image::getMemoryLimit();

if ($proccess_memory > $server_mem_limit) {
	$frontend->assign('notice',"You should reduce the value for the 'maximum resolution' field,
	    as your server settings do not allow uploading pictures of the current resolution ($max_width x $max_height).<br />");
    	
}

// Get and adapt input data
if ( $_POST )
{
	switch ( $_POST['command'] )
	{
		case 'pr_photo':
			// delete needless photos
			if ( deleteNeedlessPhotos() )
				$frontend->RegisterMessage( 'Extra photo files have been deleted' );
					
			break;
		case 'save_watermark':
			$config_data = $_POST;
			if ( $_POST['watermark'] == 2 && $_FILES['watermark_img']['name'])
			{
				$rand_index = rand(1, 999);
				
				$fileName = DIR_USERFILES.'photo_watermark_img_'.$rand_index.'.png';
		
				try {
					app_Image::convert($_FILES['watermark_img']['tmp_name'], IMAGETYPE_PNG, $fileName);
				}
				catch (SK_ImageException $e) {
					$code = $e->getCode();
					switch ($code) {
						case app_Image::ERROR_WRONG_IMAGE_TYPE : 
							$frontend->RegisterMessage( 'Can not create undefined type image, only jpeg, gif and png allowed', 'notice' );
							break;
						case app_Image::ERROR_GD_LIB_NOT_INSTALLED  : 
							$frontend->RegisterMessage( 'Your server does not support GD library', 'notice' );
							break;
						default:
							$frontend->RegisterMessage( 'System file upload error', 'notice' );
							break;
					}
					redirect( $_SERVER['PHP_SELF'] );
					
				}
				
				$last_img_id = SK_Config::section("photo")->Section("watermark")->img;
				@unlink( DIR_USERFILES.'photo_watermark_img_'.$last_img_id.'.png' );
				$config_data["img"] = $rand_index;
				$frontend->registerMessage( 'Watermark was uploaded to server' );
			}
			
			$config_data["section"] = "photo.watermark";
			$config_data["save_configs"] = true;
			
			adminConfig::SaveConfigs($config_data);
			
			break;
		case 'redraw_img':
			if ( runRedrawPhotos() )
				$frontend->RegisterMessage( "Photo convertation started" );
			else 
				$frontend->RegisterMessage( "Convertation in process", 'notice' );								
				
			break;
	}
	
	redirect( $_SERVER['PHP_SELF'].'?make_preview=1' );
}

if ( $_GET['make_preview'] )
{
	controlAdminGETActions();
	
	@copy( DIR_USERFILES.'photo_preview_backup.jpg', DIR_USERFILES.'photo_preview.jpg' );
	
	$result = app_ProfilePhoto::setWatermark( DIR_USERFILES . 'photo_preview_backup.jpg', DIR_USERFILES . 'photo_preview.jpg' );	
	
	redirect( $_SERVER['PHP_SELF'] );
}
$unprocessed_photo = UnProcessedPhoto();
$frontend->assign("convertation_in_process", (bool)$unprocessed_photo);
$frontend->assign("unprocessed_photo", $unprocessed_photo);

$frontend->assign("uniqid", uniqid());

// get watermark settings
$watermark_configs = adminConfig::ConfigList('photo.watermark');


$frontend->assign_by_ref( 'watermark_configs', $watermark_configs );


$frontend->assign_by_ref( 'image_configs', $image_configs );

$frontend->registerJsCode( $photo_configs->js_checker['pr_photo_watermark'] );

$_page['title'] = "Photo Configuration";

$template = 'config_photo.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'press_post.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'config_photo.js' );

// register meta tags
$frontend->RegisterMetaTags( array( 'http_equiv' => 'pragma', 'content' => 'no-cache' ) );

// display template
$frontend->display( $template );

?>
