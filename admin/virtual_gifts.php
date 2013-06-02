<?php

$file_key = 'gifts';
$active_tab = 'gifts';

require_once '../internals/Header.inc.php';

// Admin auth
require_once( DIR_ADMIN_INC . 'inc.auth.php' );

require_once( DIR_ADMIN_INC . 'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC . 'class.virtual_gift.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

//Get and adapt input data
if ( isset($_POST['action']) )
{
    switch ( $_POST['action'] )
    {
        case 'edit': // update virtual gift template:

            $hash = null;
            $tpl_id = (int) $_POST['id'];

            if ( strlen($_FILES['picture']['name']) )
            {
                // get old template
                $tpl = VirtualGift::getGiftTemplate($tpl_id);

                $hash = time();
                $fileName = app_VirtualGift::getGiftPicturePath($tpl_id, $hash);

                try
                {
                    app_Image::resize($_FILES['picture']['tmp_name'], 100, 100, true, $fileName, true);
                    app_Image::convert($fileName, IMAGETYPE_JPEG);
                }
                catch ( SK_ImageException $e )
                {
                    $code = $e->getCode();

                    switch ( $code )
                    {
                        case app_Image::ERROR_WRONG_IMAGE_TYPE:
                            $frontend->RegisterMessage('Can not create undefined type image, only jpeg, gif and png allowed', 'notice');
                            break;
                        case app_Image::ERROR_GD_LIB_NOT_INSTALLED:
                            $frontend->RegisterMessage('Your server does not support GD library', 'notice');
                            break;
                        default:
                            $frontend->RegisterMessage('System file upload error', 'notice');
                            break;
                    }
                    redirect($_SERVER['REQUEST_URI']);
                }

                @unlink($_FILES['picture']['tmp_name']);
                @unlink($tpl['picture']);
            }
            $result = VirtualGift::updateGiftTemplate($tpl_id, $_POST['status'], $_POST['credits'], $_POST['category'], $hash);

            if ( $result )
                $frontend->RegisterMessage('Virtual gift template updated');
            else
                $frontend->RegisterMessage('Template was not updated');

            break;

        case 'add': // Create new virtual gift template:

            if ( $_FILES['picture']['name'] )
            {
                $tpl = VirtualGift::createGiftTemplate($_POST['status'], $_POST['credits'], $_POST['category']);

                if ( $tpl['tpl_id'] )
                {
                    $hash = $tpl['hash'];
                    $fileName = app_VirtualGift::getGiftPicturePath($tpl['tpl_id'], $hash);

                    try
                    {
                        app_Image::resize($_FILES['picture']['tmp_name'], 100, 100, true, $fileName, true);

                        app_Image::convert($fileName, IMAGETYPE_JPEG);
                    }
                    catch ( SK_ImageException $e )
                    {
                        $code = $e->getCode();

                        switch ( $code )
                        {
                            case app_Image::ERROR_WRONG_IMAGE_TYPE:
                                $frontend->RegisterMessage('Can not create undefined type image, only jpeg, gif and png allowed', 'notice');
                                break;
                            case app_Image::ERROR_GD_LIB_NOT_INSTALLED:
                                $frontend->RegisterMessage('Your server does not support GD library', 'notice');
                                break;
                            case app_Image::ERROR_MAX_RESOLUTION:
                                $frontend->RegisterMessage('Image resolution is too big', 'notice');
                                break;
                            default:
                                $frontend->RegisterMessage('System file upload error', 'notice');
                                break;
                        }
                        redirect($_SERVER['REQUEST_URI']);
                    }

                    @unlink($_FILES['picture']['tmp_name']);
                    $result = true;
                }
            }

            if ( $result )
                $frontend->RegisterMessage('Virtual gift template created');
            else
                $frontend->RegisterMessage('Virtual gift template was not created', 'notice');
            break;
    }

    redirect($_SERVER['REQUEST_URI']);
}
else if ( $_GET['del_tpl_id'] ) // Delete virtual gift template:
{
    if ( !isAdminAuthed( false ) )
    {
        $frontend->RegisterMessage("No changes made. Demo mode.", 'notice');
    }
    else
    {
        if ( VirtualGift::deleteGiftTemplate($_GET['del_tpl_id']) )
        {
            $frontend->RegisterMessage('Virtual gift template deleted');
        }
        else
        {
            $frontend->RegisterMessage('Template was not deleted', 'error');
        }
    }
}

$cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : null;
$frontend->assign('cat_id', $cat_id);

// Get list of virtual gift templates:
$gifts_list = VirtualGift::getGiftTemplateList($cat_id);
$frontend->assign('gifts_list', $gifts_list);

$categories = VirtualGift::getCategoryList();
$frontend->assign('categories', $categories);

// include js modules
$frontend->IncludeJsFile(URL_ADMIN_JS . 'frontend.js');
$frontend->IncludeJsFile(URL_ADMIN_JS . 'gifts.js');

$_page['title'] = 'Virtual Gifts';

$template = 'virtual_gifts.html';

$frontend->display($template);
