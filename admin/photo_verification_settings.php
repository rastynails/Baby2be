<?php
$file_key = 'config_photo';
$active_tab = 'photo_verification';


require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_profile.php' );


$frontend = new AdminFrontend( $language);

$_page['title'] = 'Photo Verification Settings';

require_once( 'inc.admin_menu.php' );


class FileValidator {
    
    private $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    
    private $allowed_mime_types = array();
    
    private $max_file_size;
    
    public function __construct() {
        $this->max_file_size = 1024 * 1024;
    }
    
    public function validateUserFile(SK_TemporaryFile $tmp_file) {
        
        if ( $this->allowed_extensions && !in_array($tmp_file->getExtension(), $this->allowed_extensions) ) {
            throw new FileValidatorException(
                'Unallowable file extension "'.$tmp_file->getExtension().'" only ' 
                . implode(', ', $this->allowed_extensions),
                1
            );
        }
        
        if ( $tmp_file->getSize() > $this->max_file_size ) {
            throw new FileValidatorException(
                "Max file size should be less that $this->max_file_size",
                3
            );
        }
        
        list($width, $height, $type) = getimagesize($tmp_file->getPath());
                
        if ($width > 100 || $height > 100) {
            throw new FileValidatorException(
                "The image resolution should be less that ( 100 x 100 )",
                4
            );
        }
    }
}

class FileValidatorException extends Exception {}

if (!empty($_FILES['icon_file']['name'])) {
    
    try {
        $tmpFile = SK_TemporaryFile::catchFile($_FILES['icon_file'], new FileValidator());
        app_PhotoAuthenticate::deleteIcon();
        app_PhotoAuthenticate::saveIcon($tmpFile);        
    } 
    catch (FileValidatorException $e) {
        $frontend->RegisterMessage( $e->getMessage(), 'error' );
    }
    
    catch (Exception $e) {
        $frontend->RegisterMessage( 'File upload system error', 'error' );
    }
    
    SK_HttpRequest::redirect(sk_make_url());
}

if (isset($_GET['command']) && $_GET['command'] == 'delete-icon')
{
    app_PhotoAuthenticate::deleteIcon();
    
    SK_HttpRequest::redirect(sk_make_url(null, array('command'=>null)));
}

$frontend->assign('icon_url', app_PhotoAuthenticate::getIconUrl());

$frontend->display( 'photo_verification_settings.html' );
