<?php
$file_key = 'profiles';
$active_tab = 'statistic';


require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_profile.php' );


$frontend = new AdminFrontend( $language);

$_page['title'] = 'User Background Images';

require_once( 'inc.admin_menu.php' );



if (isset($_POST["delete_image"])) {
	if (count($_POST["images"])) {
		$images = $_POST["images"];
		$modes = $_POST['modes'];
		
		$service = app_ProfileComponentService::newInstance(); 
		
		foreach ($images as $profile_id) {
			if ( $modes[$profile_id] ) {
				$service->deleteBgImage( $profile_id );	
			}
			else {
				$service->saveBgImageUrl( $profile_id, null );
			}
			
			$service->saveBgImageMode( $profile_id, null );
			$service->setBGImageStatus( $profile_id, 'active' );
		}
		$frontend->registerMessage( 'Background images deleted' );
	} else {
		$frontend->registerMessage( 'Select image', 'notice' );
	}
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI']);
}

if ( $_POST['set_status'] )
{	
	if ( count( $_POST['images'] ) )
	{
		foreach ( $_POST['images'] as $profile_id ) 
		{	
			$service = app_ProfileComponentService::newInstance(); 
			
			$service->setBGImageStatus( $profile_id, $_POST['image_status'] );
		}
			
		$frontend->registerMessage( 'Selected images status set to : <code>'.$_POST['image_status'].'</code>' );
	}
	else 
		$frontend->registerMessage( 'Select image', 'notice' );
		
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI']);
}

$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

if ( in_array( $_GET['photo_status'], array( 'active', 'approval' ) ) ){
	$photos_status = $_GET['photo_status'];}
else {
	$photos_status='approval';
}

$per_page = 12;

$limit = $per_page *( $page-1 ).", ".$per_page;

$query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `".TBL_PROFILE."` 
	WHERE `bg_image_status`='approval' AND ( 
	(`bg_image_mode`=0 AND `bg_image_url`!='null' AND `bg_image_url` IS NOT NULL ) 
		OR
	(`bg_image_mode`=1 AND `bg_image`!='null' AND `bg_image` IS NOT NULL )
	)");

$paging["total"] = SK_MySQL::query($query)->fetch_cell();
$paging["on_page"] = $per_page;
$paging["pages"] = 10;


$query = SK_MySQL::placeholder("SELECT `username`, `profile_id`, `bg_image`, `bg_image_url`, `bg_image_mode` 
	FROM `".TBL_PROFILE."` WHERE `bg_image_status`='approval' AND ( 
	(`bg_image_mode`=0 AND `bg_image_url`!='null' AND `bg_image_url` IS NOT NULL ) 
		OR
	(`bg_image_mode`=1 AND `bg_image`!='null' AND `bg_image` IS NOT NULL )
	) LIMIT $limit");

$result = SK_MySQL::query($query);
$images = array();
while ( $item = $result->fetch_object()) {
	$images[$item->profile_id] = array(
		'profile_id' => $item->profile_id,
		'profile_username' => $item->username,
		'profile_url' => sk_make_url(URL_ADMIN . 'profile.php', array("profile_id"=>$item->profile_id)),
		'bg_image'	=> ( $item->bg_image_mode ) ? URL_USERFILES.$item->bg_image : $item->bg_image_url,
		'bg_image_mode' => $item->bg_image_mode
	);
}

$frontend->assign("paging", $paging);
$frontend->assign("images", $images);
$frontend->display( 'bg_images.html' );

?>