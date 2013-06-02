<?php

class component_PhotoGallery extends SK_Component
{
	private $profile_id;

	public function __construct( array $params = null )
	{
		parent::__construct('photo_gallery');
		$this->profile_id = isset($params["profile_id"]) ? intval($params["profile_id"]) : SK_HttpUser::profile_id();
		if (!$this->profile_id) {
			$this->annul();
		}
	}


	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{


		$Frontend->include_js_file(URL_STATIC . 'jquery.jcarousel.js');
		$Frontend->include_js_file(URL_STATIC . 'tooltip.js');

		$handler = new SK_ComponentFrontendHandler('PhotoGallery');

		$handler->construct();

		$photos = app_ProfilePhoto::getPhotos($this->profile_id);
		
		foreach ( array_keys($photos) as $key) {
			$photo = & $photos[$key];
			$photo["description"] = app_TextService::stCensor(nl2br(SK_Language::htmlspecialchars($photo["description"])), 'photo', true);
            $photo["title"] = app_TextService::stCensor($photo["title"], 'photo', true);
			$handler->registerPhoto($photo);
		}

		$handler->display();

		$this->frontend_handler = $handler;
		$first_photo = current($photos);
		$Layout->assign("rate", new component_Rate(array('entity_id'=>$first_photo["id"], 'feature'=>'photo', "block"=>false)));

		return parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{
	    $photoVerificationOn = false;

        if (app_Features::isAvailable(42))
        {
            $photoVerificationOn = true;
            $Layout->assign('photo_auth_icon', app_PhotoAuthenticate::getIconUrl());
        }

        $Layout->assign('photo_ver_avaliable', $photoVerificationOn);

		return parent::render($Layout);
	}


	public static function ajax_unlock($params, SK_ComponentFrontendHandler $handler) {

		$photo_id = $params->photo_id;
		$photo_info = app_ProfilePhoto::getPhoto($photo_id);

		if ($params->password != $photo_info->password) {
			$handler->error(SK_Language::text("%components.photo_gallery.errors.incorrect_password"));
			return false;
		}
		app_ProfilePhoto::setUnlocked($photo_id);
		return array(
			'description' => nl2br( SK_Language::htmlspecialchars($photo_info->description) ),
			'thumb_src' => app_ProfilePhoto::getUrl($photo_id, app_ProfilePhoto::PHOTOTYPE_THUMB ),
			'preview_src' => app_ProfilePhoto::getUrl($photo_id, app_ProfilePhoto::PHOTOTYPE_PREVIEW ),
			'unlocked' => true
		);
	}

	public static function ajax_trackView($params, SK_ComponentFrontendHandler $handler) {
		$photo_id = $params->photo_id;
		$viewer_id = SK_HttpUser::profile_id();
		$viewer_id = $viewer_id ? $viewer_id : null;

		if (app_ProfilePhoto::photoOwnerId($photo_id) != $viewer_id) {

			$service = new SK_Service('view_photo');
			if ($service->checkPermissions() != SK_Service::SERVICE_FULL ) {
				return array('result'=>false, "message"=>$service->permission_message["message"]);
			}
			if (app_ProfilePhoto::trackPhotoView($photo_id, $viewer_id)) {
				$service->trackServiceUse();
			}
		}

		return array('result'=>true);
	}

}
