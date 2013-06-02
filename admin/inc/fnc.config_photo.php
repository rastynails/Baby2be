<?php

define("REDRAW_PHOTO_CRON_COUNT", 200);

function runRedrawPhotos()
{
	if (PhotoReDrawInProgress()) {
		return false;
	}
	$query = "SELECT `photo_id` FROM `".TBL_PROFILE_PHOTO."` WHERE `number`!='0'";
	
	$result = SK_MySQL::query($query);
	
	while ($id = $result->fetch_cell())	{
		addToRedrawQueue($id);
	}
			
	return true;
}

function addToRedrawQueue($photo_id) {
	$query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . TBL_TMP_PHOTO . "` WHERE `photo_id`=?", $photo_id);
	if (!(bool)SK_MySQL::query($query)->fetch_cell()) {
		$query = SK_MySQL::placeholder("INSERT INTO `" . TBL_TMP_PHOTO . "` VALUES(?)", $photo_id);
		SK_MySQL::query($query);
		return (bool) SK_MySQL::affected_rows();
	}
}

function PhotoReDrawInProgress() {
	return (bool) SK_MySQL::query("SELECT COUNT(*) FROM `" . TBL_TMP_PHOTO)->fetch_cell();
}


function UnProcessedPhoto() {
	return (int)SK_MySQL::query("SELECT COUNT(*) FROM `" . TBL_TMP_PHOTO)->fetch_cell();
}

function cron_RedrawPhotos()
{
	$result = SK_MySQL::query("SELECT `photo_id` FROM `" . TBL_TMP_PHOTO . "` LIMIT 0, " . REDRAW_PHOTO_CRON_COUNT);
	
	$configs = SK_Config::section('photo')->Section('general');

    $idListToDelete = array();

	while ($id = (int)$result->fetch_cell()) {
			
			if (!($original_path = app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_ORIGINAL ))) {
				continue;
			}
			
			try {
				app_Image::resize($original_path, $configs->preview_width, $configs->preview_height, false, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_PREVIEW ));
				
				app_Image::resize($original_path, $configs->view_width, $configs->view_height, false, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_VIEW ));

				copy($original_path, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_FULL_SIZE ));
				
				$view_path = app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_VIEW );
				
				app_Image::resize($view_path, $configs->thumb_width, $configs->thumb_width, true, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_THUMB ));
				
				if (SK_Config::section('photo')->Section("watermark")->watermark) {
					app_ProfilePhoto::setWatermark($original_path, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_VIEW ) );
					app_ProfilePhoto::setWatermark($original_path, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_PREVIEW ) );
					app_ProfilePhoto::setWatermark($original_path, app_ProfilePhoto::getPath($id, app_ProfilePhoto::PHOTOTYPE_FULL_SIZE ) );
				}
			} catch (Exception $e) {}

            $idListToDelete[] = $id;
	}

    if( !empty ($idListToDelete) )
    {
        SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM `" . TBL_TMP_PHOTO . "` WHERE `photo_id` IN (?@)", $idListToDelete));
    }
}



function deleteNeedlessPhotos()
{  
	$top_photo_number = SK_Config::section("photo")->Section('general')->max_count;
	
	$query = SK_MySQL::placeholder("SELECT `photo_id` FROM `".TBL_PROFILE_PHOTO."` WHERE `number`>?", $top_photo_number);
	$result = SK_MySQL::query($query);
	
	if ( (bool)$result->num_rows() )
	{	
		while ($id = $result->fetch_cell()) {
			app_ProfilePhoto::delete($id);
		}
			
		return true;
	}
	else 
		return false;
}
?>