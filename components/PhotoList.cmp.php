<?php

abstract class component_PhotoList extends SK_Component
{
	private $list = array();

	public function __construct( array $params = null )
	{
		parent::__construct('photo_list');

		if (!app_Features::isAvailable(3)) {
			SK_HttpRequest::showFalsePage();
		}

	}

	public abstract function items();

	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend)
    {

		$this->list = $this->items();

		if (!isset($this->list["items"]) || !count($this->list["items"])) {
			$this->tpl_file = "no_items.tpl";
		}

		return parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{

		$list = array(
			'items'	=> $this->list["items"],
			'total'	=> $this->list["total"],
			'per_page'	=> SK_Config::section("photo")->Section("general")->per_page,
		);
		$ids = array();
        $profileIdList = array();
		foreach (array_keys($list["items"]) as $key)
        {
			$ids[] = $key;
            $item = & $list["items"][$key];
            $profileIdList[$item['profile_id']] = $item['profile_id'];
        }

        $photoCountList = app_ProfilePhoto::getViewCountByPhotoIdList($ids);

        $photoUrlList = app_ProfilePhoto::getUrlList($ids, app_ProfilePhoto::PHOTOTYPE_THUMB );

        $usernameList = app_Profile::getUsernamesForUsers($profileIdList, $fields);

        foreach (array_keys($list["items"]) as $key)
        {
			$item = & $list["items"][$key];
			$owner_id = $item['profile_id'];

			$item['viewed'] = isset($item['viewed']) ? $item['viewed'] : $photoCountList[$item["photo_id"]];
			if ($owner_id != SK_HttpUser::profile_id())
			{
				switch ($item["publishing_status"])
				{
					case 'password_protected':
						$item['src'] = app_ProfilePhoto::password_protected_url();
						break;
					case 'friends_only':
						if (app_FriendNetwork::isProfileFriend($owner_id, SK_HttpUser::profile_id())) {
							$item['src'] = $photoUrlList[$item["photo_id"]];
						} else {
							$item['src'] = app_ProfilePhoto::friend_only_url();
						}
						break;
					default:
						$item['src'] = $photoUrlList[$item["photo_id"]];
						break;
				}
			} else {
				$item['src'] = $photoUrlList[$item["photo_id"]];
			}

			$item['url'] = app_ProfilePhoto::getPermalink($item["photo_id"]);

			$item["owner_name"] = $usernameList[$owner_id];
			$item["owner_url"] = SK_Navigation::href("profile", "profile_id=" . $owner_id);
            $item["title"] = app_TextService::stCensor($item["title"], 'photo', true);
            $item["description"] = app_TextService::stCensor($item["description"], 'photo', true);
		}

		$rates = app_RateService::stFindRatesForEntityIds($ids, 'photo');
		$Layout->assign('rates', $rates);
		if (app_Features::isAvailable(30)) {
			$comments = app_CommentService::stFindCommentsCountForEntities($ids, 'photo', ENTITY_TYPE_PHOTO_UPLOAD);
		} else {
			$comments = false;
		}

		$Layout->assign('comments', $comments);

		$Layout->assign('tabs', $this->tabs());

		$Layout->assign('list', $list);

		return parent::render($Layout);
	}

	protected function tabs() {
		return array();
	}
}
