<?php

class component_PhotoView extends SK_Component
{
	private $owner_id;

	private $photo_id;

	private $album_id;

	/**
	 * @var dto_PhotoAlbum
	 */
	private $album;

	private $ownerMode;

	public $not_avaliable = false;

	public function __construct( array $params = null )
	{
		parent::__construct('photo_view');

		if ( isset(SK_HttpRequest::$GET["photo_id"]) ) {
			$this->photo_id = intval(SK_HttpRequest::$GET["photo_id"]) ;
			$this->owner_id = app_ProfilePhoto::photoOwnerId($this->photo_id);
			if ($this->album_id = app_PhotoAlbums::getPhotoAlbum($this->photo_id)) {
				$this->album = app_PhotoAlbums::getAlbum($this->album_id);
			}
		} elseif (isset(SK_HttpRequest::$GET["profile_id"])) {
			$this->owner_id = (int)SK_HttpRequest::$GET["profile_id"];
			$this->photo_id = 0;
		} elseif (isset(SK_HttpRequest::$GET["album"])) {
			$this->album_id = SK_HttpRequest::$GET["album"];
			$this->album = app_PhotoAlbums::getAlbum($this->album_id);
			$this->owner_id = $this->album->getProfile_id();
			$this->photo_id = 0;
		} else {
			SK_HttpRequest::showFalsePage();
		}

		app_ProfilePhoto::setProcessAlbum($this->album_id);

		$this->profile_id = isset($params["profile_id"])
			? intval($params["profile_id"])
			: SK_HttpUser::profile_id();

		$this->ownerMode = $this->profile_id == $this->owner_id;
	}

	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		if (!(bool)$this->owner_id) {
			return false;
		}

		$Frontend->include_js_file(URL_STATIC.'jquery.jcarousel.js');

		$handler = new SK_ComponentFrontendHandler("PhotoView");

		$photos = app_ProfilePhoto::getPhotos($this->owner_id);

		if ( empty($photos) )
		{
		    SK_HttpRequest::showFalsePage();
		}

		if (!$this->photo_id) {
                        $fp = reset($photos);
			$this->photo_id = $fp["id"];
		}

		if ($this->owner_id != SK_HttpUser::profile_id()) {

			// track photo view
			$service = new SK_Service('view_photo');
			if ($service->checkPermissions() != SK_Service::SERVICE_FULL ) {
				$this->not_avaliable = true;
				$Layout->assign("permission", array('avaliable' => !$this->not_avaliable, 'msg'=>$service->permission_message["message"]));
				return parent::prepare($Layout, $Frontend);
			} else {

				if (app_ProfilePhoto::trackPhotoView($this->photo_id, $this->profile_id)) {
					$service->trackServiceUse();
				}
			}
		}
		$Layout->assign("permission", array('avaliable' => true));

		$inc = 1;
		foreach ($photos as $item) {
			if ($item["id"] == $this->photo_id) {
				$handler->construct($this->photo_id, $inc);
				break;
			}
			$inc++;
		}

		$Layout->assign('photos', $photos);

		$this->frontend_handler = $handler;

		$photoVerificationOn = false;

		if (app_Features::isAvailable(42))
		{
    		$photoVerificationOn = true;
    		$Layout->assign('photo_auth_icon', app_PhotoAuthenticate::getIconUrl());
		}

		$Layout->assign('photo_ver_avaliable', $photoVerificationOn);

		return parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{
		$photo = app_ProfilePhoto::getPhotoInfo($this->photo_id);
		$photo["owner_name"] = app_Profile::username($this->owner_id);
		$photo["owner_url"] = SK_Navigation::href("profile", "profile_id=" . $this->owner_id);


		$photo["views"] = app_ProfilePhoto::getViewCount($this->photo_id);

		$photo["locked"] = ($photo["publishing_status"] == "friends_only"
			&& !app_FriendNetwork::isProfileFriend($this->owner_id, SK_HttpUser::profile_id()))
			|| ($photo["publishing_status"] == "password_protected" && !app_ProfilePhoto::isUnlocked($this->photo_id));

		$photo["src"] = app_ProfilePhoto::getUrl($this->photo_id, app_ProfilePhoto::PHOTOTYPE_VIEW );
		$photo["fullsize_src"] = app_ProfilePhoto::getUrl($this->photo_id, app_ProfilePhoto::PHOTOTYPE_FULL_SIZE );

		if ($photo["locked"]) {
			switch ($photo["publishing_status"]) {
				case "password_protected":
					$photo["fullsize_src"] = app_ProfilePhoto::password_protected_url();
					$photo["src"] = app_ProfilePhoto::password_protected_url();
					break;

				case "friends_only":
					$photo["fullsize_src"] = app_ProfilePhoto::friend_only_url();
					$photo["src"] = app_ProfilePhoto::friend_only_url();
					break;
			}
		}

		$photo['locked'] = $photo['locked'] && !$this->ownerMode;

		if (app_Features::isAvailable(30)) {
                $comments = app_CommentService::stGetCommentsCount('photo', $this->photo_id, ENTITY_TYPE_PHOTO_UPLOAD);
		} else {
			$comments = false;
		}
		$photo["comments"] = $comments;

		$Layout->assign('photo', $photo);

        $owner_name = app_Profile::username($this->owner_id);
        $owner_url = SK_Navigation::href("profile", array('profile_id'=> $this->owner_id));

        SK_Language::defineGlobal("username",$owner_name);
       	$title = isset($photo["title"]) && strlen(trim($photo["title"]))
       		? $photo["title"]
       		: SK_Language::text('components.photo_view.photo_title', array('photo_id' => $this->photo_id));

        $title = app_TextService::stCensor($title, 'photo', true);

		SK_Navigation::removeBreadCrumbItem();
        SK_Navigation::addBreadCrumbItem($owner_name, $owner_url);

		if ($this->album) {
			SK_Navigation::addBreadCrumbItem($this->album->getView_label(), $this->album->getUrl());

			SK_Language::defineGlobal("phototitle", SK_Language::text(
				'components.photo_view.header_album',
				 array(
				 	'album'	=> $this->album->getView_label(),
				 	'photo'	=> strip_tags($title)
				 )));
		} else {
			SK_Language::defineGlobal("phototitle", $title);
		}

        if ( !empty($photo['description']) )
        {
            $this->getDocumentMeta()->description = strip_tags(app_TextService::stCensor($photo['description'], 'photo'));
        }

        SK_Navigation::removeBreadCrumbItem();
        SK_Navigation::addBreadCrumbItem($title, app_ProfilePhoto::getPermalink($this->photo_id));

        // Comments
        $Layout->assign("comments", new component_AddComment($this->photo_id, 'photo', 'photo_upload'));

        $Layout->assign('album_id', $this->album_id);
        $Layout->assign('owner_id', $this->owner_id);

        //Rates
        $Layout->assign("rates", new component_Rate(array('entity_id'=>$this->photo_id, 'feature'=>'photo')));
        //Tags
        $tagsEnabled = app_Features::isAvailable(17);
        $tagsCmp = false;
        if ( $tagsEnabled )
        {
            if ( $this->ownerMode )
            {
                $tagsCmp = new component_TagEdit( array( 'entity_id' => $this->photo_id, 'feature' => 'photo' ) );
            }
            else
            {
                $tagsCmp = new component_EntityItemTagNavigator('photo', SK_Navigation::href('photos'), $this->photo_id);
            }

        }

        $Layout->assign('tagsCmp', $tagsCmp);

        //Report
        $Layout->assign("report", new component_Report(array(
        	'entity_id'=>$this->photo_id,
        	'type'=>'photo',
        	'reporter_id'=>SK_HttpUser::profile_id()
        	)));

        $Layout->assign('ownerMode', $this->ownerMode);

        return parent::render($Layout);
	}

	public static function ajax_unlock($params, SK_ComponentFrontendHandler $handler) {

		$photo_id = $params->photo_id;
		$photo_info = app_ProfilePhoto::getPhoto($photo_id);

		if ($params->password != $photo_info->password) {
			$handler->error(SK_Language::text("%components.photo_view.incorrect_password"));
			return false;
		}
		app_ProfilePhoto::setUnlocked($photo_id);
		$handler->refresh();
	}



}
