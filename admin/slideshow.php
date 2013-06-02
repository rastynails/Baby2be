<?php

$file_key = 'index_page';
$active_tab = 'slideshow';

require_once '../internals/Header.inc.php';

// Admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';
require_once DIR_ADMIN_INC.'class.page_builder.php';

require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

require_once 'inc.admin_menu.php';

$conf = new adminConfig();
$conf->SaveConfigs($_POST);
$conf->getResult($frontend);

if ( isset($_GET['move']) && $_GET['id'] )
{
    $dir = in_array($_GET['move'], array('up', 'down')) ? $_GET['move'] : 'down';
    app_Slideshow::changeSlidesOrder($_GET['id'], $dir);

    redirect(URL_ADMIN . 'slideshow.php');
}

if ( !empty($_POST['add_slide']) )
{
    $label = !empty($_POST['label']) ? trim($_POST['label']) : null;
    $url = !empty($_POST['url']) ? trim($_POST['url']) : null;
    $slide = $_FILES['slide_image'];

    if ( is_uploaded_file($slide['tmp_name']) )
    {
        $iniValue = floatval(ini_get('upload_max_filesize'));
        $maxSize = 1024 * 1024 * ($iniValue ? $iniValue : 4);

        if ( !app_Slideshow::validateImage($slide['name']) )
        {
            $frontend->registerMessage('File extension is not allowed', 'error');
        }
        else if ( $slide['size'] > $maxSize )
        {
            $frontend->registerMessage('File size exceeded', 'error');
        }
        elseif ( app_Slideshow::addSlide($_FILES['slide_image'], $label, $url) )
        {
            $frontend->registerMessage('Slide image has been added');
        }
    }
    else
    {
        $frontend->registerMessage('Slide image was not uploaded', 'error');
    }

    redirect($_SERVER['REQUEST_URI']);
}

if ( !empty($_GET['delete-slide-id']) )
{
    if ( app_Slideshow::deleteSlide((int) $_GET['delete-slide-id']) )
    {
        $frontend->registerMessage('Slide image has been deleted');
    }

    redirect(URL_ADMIN . 'slideshow.php');
}

$frontend->assign('slides', app_Slideshow::getSlideList());
$frontend->includeCSSFile( URL_ADMIN_CSS . 'drag_and_drop.css' );
$_page['title'] = "Index Page Slideshow";

$frontend->display('slideshow.html');
