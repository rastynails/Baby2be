<?php

class component_UploadPhoto extends SK_Component
{
    private $album_id;

    public function __construct( array $params = null )
    {
        if ( isset($params['album_id']) )        {
            $this->album_id = intval($params['album_id']);
        }

        parent::__construct('upload_photo');
    }


    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('UploadPhoto');
        $handler->construct();

        $configs = SK_Config::section('photo')->Section('general');


        if ( isset($this->album_id) )        {
            app_ProfilePhoto::setProcessAlbum($this->album_id);
            $handler->addSlotRange($configs->max_photos_in_album);
        }        else        {
            $handler->addSlotRange($configs->max_count);
        }

        $photos = app_ProfilePhoto::getUploadedPhotos();

        app_ProfilePhoto::unsetProcessAlbum();

        foreach ( $photos as $photo )
        {
            $handler->setSlotImage($photo['slot'], $photo);
        }

        $service = new SK_Service("upload_photo");
        if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )        {
            $handler->noPermited($service->permission_message["message"]);
        }

        $this->frontend_handler = $handler;

        $publ_statuses[] = "public";

        if ( app_Features::isAvailable(9) && app_Features::isAvailable(14) )        {
            $publ_statuses[] = "friends_only";
        }

        if ( app_Features::isAvailable(24) )        {
            $publ_statuses[] = "password_protected";
        }

        $Layout->assign("publishing_statuses", $publ_statuses);


        parent::prepare($Layout, $Frontend);
    }

    public function render( SK_Layout $Layout )
    {
        if ( app_PhotoAlbums::isFeatureActive() )        {
            $albums = app_PhotoAlbums::getAlbums();
        }        else        {
            $albums = array();
        }

        $is_general = false;

        foreach ( $albums as $key => $album )
        {
            /* @var $album dto_PhotoAlbum */
            if ( app_PhotoAlbums::IsFull($album->getId()) )
            {
                unset($albums[$key]);
            }
        }

        if ( isset($this->album_id) )        {
            unset($albums[$this->album_id]);
        }        else        {
            $is_general = true;
        }

        $general_full = app_PhotoAlbums::IsFull();
        $display_general = !($is_general || $general_full);

        $move_to = $display_general || count($albums);

        $Layout->assign('move_to', $move_to);
        $Layout->assign('display_general', $display_general);
        $Layout->assign('is_general_album', $is_general);

        $Layout->assign('albums', $albums);

        $Layout->assign('allow_rotate', SK_Config::section('photo.general')->allow_rotate);


        $Layout->assign('photo_ver_avaliable', app_Features::isAvailable(42));

        $tagsEnabled = app_Features::isAvailable(17);

        if ( $tagsEnabled )
        {
            $Layout->assign('tags', new component_TagEdit(array('entity_id' => $this->video_id, 'feature' => 'video')));
        }
        else
        {
            $Layout->assign('tags', false);
        }

        return parent::render($Layout);
    }


    public function handleForm( SK_Form $Form )
    {
        if ( isset($this->album_id) )        {
            $Form->getField('album_id')->setValue($this->album_id);
        }

        $Form->frontend_handler->bind("success", "function(data) {
			if (data.uploaded) {
				this.ownerComponent.setSlotImage(data.slot, data.image);
				this.ownerComponent.showDetails(data.slot);
				if (data.create_thumb) {
					this.ownerComponent.showConsoleThumbPreloader();
					this.ownerComponent.ajaxCall('ajax_CreateThumb', {photo_id: data.image.id});
				}
			} else {
				if (data.no_permited!=undefined && data.no_permited) {
					this.ownerComponent.noPermited(data.no_permited_msg);
				}
				this.ownerComponent.refreshCurrentSlot();
			}

		}");

        $Form->frontend_handler->bind("error", "function() {
			this.ownerComponent.refreshCurrentSlot();
		}");
    }

    public static function ajax_DeletePhoto( $params, SK_ComponentFrontendHandler $handler )
    {
        $photo_id = $params->photo_id;

        $profile_id = app_ProfilePhoto::getPhoto($photo_id)->profile_id;
        if ( $profile_id != SK_HttpUser::profile_id() )        {
            $username = app_Profile::getFieldValues($profile_id, "username");
            $handler->error(SK_Language::section("components.upload_photo.upload.message")->text("profile_error", array("username" => $username)));
            return;
        }

        if ( app_ProfilePhoto::delete($photo_id) )        {
            $handler->emptySlot();
            $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("photo_delete_success"));
        }
        else        {
            $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("photo_delete_error"));
        }
    }

    public static function ajax_SaveInfo( $params, SK_ComponentFrontendHandler $handler )
    {
        $photo_id = $params->photo_id;

        $profile_id = app_ProfilePhoto::getPhoto($photo_id)->profile_id;
        if ( $profile_id != SK_HttpUser::profile_id() )        {
            $username = app_Profile::getFieldValues($profile_id, "username");
            $handler->error(SK_Language::section("components.upload_photo.upload.message")->text("profile_error", array("username" => $username)));
            return false;
        }

        $query = SK_MySQL::placeholder('UPDATE `' . TBL_PROFILE_PHOTO . '` SET `description`="?", `title`="?" WHERE `photo_id`=?',
                trim($params->description), $params->title, $photo_id);

        SK_MySQL::query($query);


        if ( SK_MySQL::affected_rows() )        {
            $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("info_change_success"));
        }
        else        {
            $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("info_change_error"));
        }
        return array(
            'txt_description' => app_TextService::stCensor($params->description, 'photo'),
            'html_description' => app_TextService::stCensor(nl2br(SK_Language::htmlspecialchars($params->description)), 'photo'),
            'title' => app_TextService::stCensor($params->title, 'photo', true)
        );
    }

    public static function ajax_getPassword( $params, SK_ComponentFrontendHandler $handler )    {
        $photo_id = $params->photo_id;

        $photo_info = app_ProfilePhoto::getPhoto($photo_id);
        if ( $photo_info->profile_id != SK_HttpUser::profile_id() )        {
            $username = app_Profile::getFieldValues($profile_id, "username");
            $handler->error(SK_Language::section("components.upload_photo.upload.message")->text("profile_error", array("username" => $username)));
            return false;
        }
        return $photo_info->password;
    }

    public static function ajax_ChangeStatus( SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler )    {

        $params->validate(array(
            'password' => 'string',
            'photo_id' => 'int',
            'status' => 'string'
            ), true);

        $photo_id = $params->photo_id;

        $profile_id = app_ProfilePhoto::getPhoto($photo_id)->profile_id;
        if ( $profile_id != SK_HttpUser::profile_id() )        {
            $username = app_Profile::getFieldValues($profile_id, "username");
            $handler->error(SK_Language::section("components.upload_photo.upload.message")->text("profile_error", array("username" => $username)));
            return false;
        }


        if ( in_array($params->status, array("friends_only", "password_protected")) )        {

            switch ( $params->status )
            {
                case 'friends_only':
                    $service = new SK_Service("set_friends_only_photo");
                    break;
                case 'password_protected':
                    $service = new SK_Service("set_password_protected_photo");
                    break;
            }

            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $handler->error($service->permission_message["message"]);
                return false;
            }

            $service->trackServiceUse();
        }

        $password_sql_str = $params->has('password') ? SK_MySQL::placeholder(', `password`="?"', $params->password) : '';

        $status = $params->has('status') ? $params->status : 'public';

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            switch ( $status )
            {
                case 'friends_only':
                    $visibility = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW;
                    break;
                case 'public':
                    $visibility = app_Newsfeed::VISIBILITY_FULL;
                    break;
                default:
                    $visibility = app_Newsfeed::VISIBILITY_AUTHOR;
                    break;
            }

            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_PHOTO,
                    'entityType' => 'photo_upload',
                    'entityId' => $photo_id,
                    'userId' => $profile_id,
                    'status' => app_ProfilePhoto::getPhoto($photo_id)->status,
                    'visibility' => $visibility
                )
            );

            app_Newsfeed::newInstance()->updateVisibility($newsfeedDataParams);
        }


        $query = SK_MySQL::placeholder('UPDATE `' . TBL_PROFILE_PHOTO . '`
					SET `publishing_status`="?"' . $password_sql_str . ' WHERE `photo_id`=?',
                $status, $photo_id);

        SK_MySQL::query($query);

        $result = (bool) SK_MySQL::affected_rows();

        if ( $result )        {
            $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("status_change_success"));
            return true;
        }

        $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("status_change_error"));
    }

    public static function ajax_CreateThumb( $params, SK_ComponentFrontendHandler $handler )    {
        $photo_id = $params->photo_id;

        $profile_id = app_ProfilePhoto::getPhoto($photo_id)->profile_id;
        if ( $profile_id != SK_HttpUser::profile_id() )        {
            $username = app_Profile::getFieldValues($profile_id, "username");
            $handler->error(SK_Language::section("components.upload_photo.upload.message")->text("profile_error", array("username" => $username)));
            return false;
        }
        try        {
            app_ProfilePhoto::createThumbnail($photo_id);
        }
        catch ( SK_ProfilePhotoException $e )        {
            $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("make_thumb_error"));
            $handler->changeConsoleThumb();
        }

        $handler->message(SK_Language::section("components.upload_photo.upload.message")->text("make_thumb_success"));
        $handler->changeConsoleThumb(app_ProfilePhoto::getThumbUrl($profile_id));

        app_UserPoints::earnCreditsForActionAlternative($profile_id, 'avatar_add', true);
    }

    public static function ajax_MoveTo( $params, SK_ComponentFrontendHandler $handler )    {
        $album_id = $params->album_id;
        $photo_id = $params->photo_id;
        if ( app_PhotoAlbums::moveTo($photo_id, $album_id) )        {
            $handler->reloadPage();
        }
    }

    public static function ajax_Rotate( $params, SK_ComponentFrontendHandler $handler )    {
        $photoId = $params->photo_id;
        $original_path = app_ProfilePhoto::getPath($photo_id, app_ProfilePhoto::PHOTOTYPE_ORIGINAL);

        if ( app_ProfilePhoto::rotatePhoto($photoId, $params->angle) )
        {
            return app_ProfilePhoto::getUrl($photoId, app_ProfilePhoto::PHOTOTYPE_PREVIEW) . '?' . uniqid();
        }

        return false;
    }


    public static function clearCompile( $tpl_file = null, $compile_id = null )    {
        $cmp = new self;
        return $cmp->clear_compiled_tpl();
    }


}
