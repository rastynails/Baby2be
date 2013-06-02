<?php
$file_key = 'profiles';
$active_tab = 'statistic';


require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC . 'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_profile.php' );


$frontend = new AdminFrontend($language);

$_page['title'] = 'User Photos';

require_once( 'inc.admin_menu.php' );


$per_page = 12;


if ( isset($_POST["delete_photo"]) ){
    if ( count($_POST["photos"]) )    {
        $photos = $_POST["photos"];
        foreach ( $photos as $item )        {
            app_ProfilePhoto::delete($item);
        }
        $frontend->registerMessage('Photo files deleted');
    }    else    {
        $frontend->registerMessage('Select photo', 'notice');
    }
    SK_HttpRequest::redirect($_SERVER['REQUEST_URI']);
}

if ( $_POST['set_status'] )
{
    if ( count($_POST['photos']) )
    {
        foreach ( $_POST['photos'] as $item )        {
            if ( $_POST['photo_status'] == "active" )
            {
                $list = app_UserActivities::getWhere("type = 'photo_upload' and item={$item}");

                $action = (isset($list[0])) ? $list[0] : false;

                if ( is_array($action) && $action['status'] == 'approval' )
                {
                    app_UserActivities::setStatus($action['skadate_user_activity_id'], 'active');
                }
            }
            adminProfile::setPhotoStatus($item, $_POST['photo_status']);

            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $photo = app_ProfilePhoto::getPhoto($item);

                if ( $photo->number == 0 )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_NEWSFEED,
                            'entityType' => 'profile_avatar_change',
                            'entityId' => $item,
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
                            'entityId' => $item,
                            'userId' => $photo->profile_id,
                            'status' => $_POST['photo_status']
                        )
                    );
                }
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
            }
        }

        $frontend->registerMessage('Selected photos status set to: <code>' . $_POST['photo_status'] . '</code>');
    }
    else
        $frontend->registerMessage('Select photo', 'notice');

    SK_HttpRequest::redirect($_SERVER['REQUEST_URI']);
}


function getItemList( $page, $status, $thumbs = false ){
    global $per_page;

    $out = array();

    $add_cond = $thumbs ? '`ph`.`number`=0' : '`ph`.`number`!=0';

    $query = SK_MySQL::placeholder("
		SELECT COUNT(*) 
		FROM `" . TBL_PROFILE_PHOTO . "` as ph LEFT JOIN `" . TBL_PROFILE . "` as pr ON ph.`profile_id`=pr.`profile_id` 
		WHERE ph.`status`='?' AND $add_cond;	
	", $status);

    $out['total'] = (int) SK_MySQL::query($query)->fetch_cell();

    $items = array();

    $limit = $per_page * ( $page - 1 ) . ", " . $per_page;

    $query = SK_MySQL::placeholder("
		SELECT `photo_id`,`index`,`username`,ph.`profile_id`,`number`, `publishing_status` 
		FROM `" . TBL_PROFILE_PHOTO . "` as ph LEFT JOIN `" . TBL_PROFILE . "` as pr ON ph.`profile_id`=pr.`profile_id` 
		WHERE ph.`status`='?' AND $add_cond ORDER BY `photo_id` DESC LIMIT $limit;	
	", $status);

    $result = SK_MySQL::query($query);

    while ( $item = $result->fetch_object() )    {
        $photo = array(
            'photo_id' => $item->photo_id,
            'type' => $item->publishing_status,
            'profile_username' => $item->username,
            'profile_url' => sk_make_url(URL_ADMIN . 'profile.php', array("profile_id" => $item->profile_id)),
            'photo_thumb_src' => app_ProfilePhoto::getUrl($item->photo_id, app_ProfilePhoto::PHOTOTYPE_THUMB),
            'photo_view_src' => app_ProfilePhoto::getUrl($item->photo_id, app_ProfilePhoto::PHOTOTYPE_VIEW)
        );

        $items[$item->photo_id] = $photo;
    }

    $out['items'] = $items;
    return $out;
}

$photo_page = isset($_GET["p_page"]) ? (int) $_GET["p_page"] : 1;
$thumb_page = isset($_GET["t_page"]) ? (int) $_GET["t_page"] : 1;

if ( in_array($_GET['photo_status'], array('active', 'approval', 'suspended')) ){
    $photos_status = $_GET['photo_status'];}
else{
    $photos_status = 'approval';
}


$thumb_list = getItemList($thumb_page, $photos_status, true);
$photo_list = getItemList($photo_page, $photos_status);

$paging['thumb']["total"] = $thumb_list['total'];
$paging['thumb']["on_page"] = $per_page;
$paging['thumb']["pages"] = 10;

$paging['photo']["total"] = $photo_list['total'];
$paging['photo']["on_page"] = $per_page;
$paging['photo']["pages"] = 10;


$frontend->IncludeJsFile(URL_STATIC . 'jquery.dimensions.js');
$frontend->IncludeJsFile(URL_ADMIN_JS . 'photo_list.js');
$frontend->assign("paging", $paging);
$frontend->assign("photos", $photo_list['items']);
$frontend->assign("thumbs", $thumb_list['items']);
$frontend->display('allphotos.html');

?>