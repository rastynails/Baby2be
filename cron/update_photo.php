<?php

define("IS_CRON", true);

// Detect DOCUMENT_ROOT var
$document_root = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

require_once $document_root.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$count = 500;
$start = 0;

while ( true )
{
    $query = SK_MySQL::placeholder(" SELECT * FROM `" . TBL_PROFILE_PHOTO . "` WHERE 1 ORDER BY `photo_id` LIMIT {$start}, {$count} ");
    $result = SK_MySQL::query($query);

    $start += $count;

    $itemList = array();

    while ( $item = $result->fetch_assoc() )
    {
        $itemList[] = $item;
    }

    if ( empty($itemList) )
    {
        break;
    }

    foreach ( $itemList as $item )
    {
        $photo_id = $item['photo_id'];

        $query = SK_MySQL::placeholder(" SELECT COUNT(*) as `COUNT` FROM `" . TBL_PHOTO_VIEW . "` WHERE `photo_id`=? "
            , $photo_id);

        $view_count = SK_MySQL::query($query)->fetch_cell();

        $query = SK_MySQL::placeholder(" UPDATE `" . TBL_PROFILE_PHOTO . "` SET `view_count` = ".($view_count)." WHERE `photo_id`=? ", $photo_id);
        SK_MySQL::query($query);
    }
}
