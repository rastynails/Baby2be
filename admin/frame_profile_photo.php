<?php

$file_key = 'profile';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.profile.php' );

$profile_id = intval( $_GET['profile_id'] );

if ( !is_numeric( $_GET['profile_id'] ) || !intval( $_GET['profile_id'] ) )
	exit('Undefined profile id' );

if ( in_array( $_GET['status'], array( 'active', 'approval', 'suspended' ) ) )
	$photos_status = $_GET['status'];
	

if ( intval( $_GET['make_thumb'] ) )
{
	controlAdminGETActions();
	
	app_ProfilePhoto::createThumbnail( $_GET['make_thumb'] );
	unset( $_GET['make_thumb'] );
	
	$frontend->registerMessage( 'Thumbnail created' );
	
	redirect( sk_make_url(null, array('make_thumb'=>null)) );
}

if ( $_POST['delete_photo'] )
{
	if ( is_array( $_POST['photo_id_arr'] ) )
	{
		foreach ( $_POST['photo_id_arr'] as $photo_id )
			app_ProfilePhoto::delete( $photo_id );
			
		$frontend->registerMessage( 'Photo files deleted' );
	}
	else 
		$frontend->registerMessage( 'Select photo', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] . '&' . uniqid() );
}

if ( $_POST['set_status'] )
{
	if ( is_array( $_POST['photo_id_arr'] ) )
	{
		foreach ( $_POST['photo_id_arr'] as $photo_id ){
			adminProfile::setPhotoStatus( $photo_id, $_POST['photo_status'] );

            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $photo = app_ProfilePhoto::getPhoto($photo_id);

                if ( $photo->number == 0 )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_NEWSFEED,
                            'entityType' => 'profile_avatar_change',
                            'entityId' => $photo_id,
                            'userId' => $photo->profile_id,
                            'status' => $_POST['photo_status']
                        )
                    );
                }
                else
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_PHOTO,
                            'entityType' => 'photo_upload',
                            'entityId' => $photo_id,
                            'userId' => $photo->profile_id,
                            'status' => $_POST['photo_status']
                        )
                    );
                }
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
            }

			$list = app_UserActivities::getWhere("type = 'photo_upload' and item={$photo_id}");
			
			$action = (isset($list[0]))?$list[0] : false;
			
			if( is_array($action) && $action['status'] == 'approval' )
			{
				app_UserActivities::setStatus($action['skadate_user_activity_id'], 'active');
			}
		}
			
		app_ProfilePhoto::updateHasPhotoStatus($profile_id);
			
		$frontend->registerMessage( 'Selected photo files status set to : <code>'.$_POST['photo_status'].'</code>' );
	}
	else 
		$frontend->registerMessage( 'Select photo', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] . '&' . uniqid() );
}


class FileValidator {
	
	private $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
	
	private $allowed_mime_types = array();
	
	private $max_file_size;
	
	public function __construct() {
		$this->max_file_size = SK_Config::section("photo")->Section("general")->max_filesize * 1024 * 1024;
	}
	
	public function validateUserFile(SK_TemporaryFile $tmp_file) {
		
		if ( $this->allowed_extensions && !in_array($tmp_file->getExtension(), $this->allowed_extensions) ) {
			throw new FileValidatorException(
				'Unallowable file extension "'.$tmp_file->getExtension().'" only ' 
				. implode(', ', $this->allowed_extensions),
				1
			);
		}
		
		if ( $this->allowed_mime_types && !in_array($tmp_file->getType(), $this->allowed_mime_types) ) {
			throw new FileValidatorException(
				'Unallowable file mime-type "'.$tmp_file->getType().'"',
				2
			);
		}
		
		if ( $tmp_file->getSize() > $this->max_file_size ) {
			throw new FileValidatorException(
				"Max file size should be less that $this->max_file_size",
				3
			);
		}
		
		list($width, $height, $type) = getimagesize($tmp_file->getPath());
		$config = SK_Config::section("photo")->Section("general");
		
		if ($width > $config->max_width || $height > $config->max_height) {
			throw new FileValidatorException(
				"The image resolution should be less that ( $config->max_width x $config->max_height )",
				4
			);
		}
	}
}

class FileValidatorException extends Exception {}

if (isset($_FILES['photo_upload']['name'])) {
	try {
		$tmp_file_uniqid = SK_TemporaryFile::catchFile($_FILES['photo_upload'], new FileValidator())->getUniqid();
		if ( !isset($_POST['slot']) ) {
			$frontend->registerMessage( 'Select photo number', 'notice' );
		}
		else {
			app_ProfilePhoto::upload($tmp_file_uniqid, (int) $_POST['slot'], $profile_id, 'active');
			redirect( $_SERVER['REQUEST_URI'] . '&' . uniqid() );
		}
	} 
	catch (FileValidatorException $e) {
		$frontend->RegisterMessage( $e->getMessage(), 'error' );
	}
	
	catch (Exception $e) {
		$frontend->RegisterMessage( 'File upload system error', 'error' );
	}
	
	
}

$profile_photos = adminProfile::getPhotos( $profile_id, true, false, $photos_status );

$upload_photo_number_arr = array_fill( 1, SK_Config::section("photo")->Section("general")->max_count, 0 );

$all_profile_photos = app_ProfilePhoto::getPhotos( $profile_id, false);

if ( $all_profile_photos[0]['flag'] != 'no_photos' )
	foreach ( $all_profile_photos as $_photo_id => $_photo_info )
	{
		$upload_photo_number_arr[$_photo_info['number']] = $_photo_info;	
		$upload_photo_number_arr[$_photo_info['number']]['photo_id'] = $_photo_id;
	}

$frontend->assign_by_ref( 'upload_photo_number_arr', $upload_photo_number_arr );

if ( $profile_photos[0]['flag'] == 'no_photos' )
{
	if ( $photos_status )
		$no_photo_msg = 'No '.$photos_status.' photo';
	else 
		$no_photo_msg = 'No photo';
		
	$frontend->registerMessage( $no_photo_msg, 'notice' );
}

$frontend->assign_by_ref( 'photo_status', $photos_status );

$frontend->assign_by_ref( 'profile_photos', $profile_photos );

$frontend->display( 'frame_profile_photo.html' );
?>
