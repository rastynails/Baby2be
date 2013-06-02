<?php

class component_SmallPhotoAlbumList extends SK_Component
{
	private $profile_id;
	
	private $per_page = 10;
	
	private $exclude;
	
	private $title;
	
	public function __construct( array $params = null )
	{
		if (!app_PhotoAlbums::isFeatureActive()) {
			$this->annul();
		}
		
		if (isset($params['profile_id'])) {
			if ( !($this->profile_id = intval($params['profile_id'])) ) {
				$this->profile_id = SK_HttpUser::profile_id();
			}
		}
	
		if ($params['count'] === false) {
			$this->per_page = false;
		} elseif ( isset($params['count']) && $count = intval($params['count'])) {
			$this->per_page = $count;
		}
		
		if ( isset($params['exclude']) && $exclude = intval($params['exclude'])) {
			$this->exclude = $exclude;
		}
		
		if (isset($params['title']) && $title = trim($params['title'])) {
			$this->title = $title;
		} else {
			$this->title = SK_Language::text('components.small_photo_album_list.title', array(
				'username'	=> app_Profile::username($this->profile_id)
			));
		}
		
		parent::__construct('small_photo_album_list');
	}
	
	public function items() {
		$limit = array(
			'count' => $this->per_page,
			'offset' => 0
		);
		
		if ($this->per_page === false) {
			$limit = null;
		}
		
		$albums = app_PhotoAlbums::getAlbums($this->profile_id, $limit, true);
		if (isset($this->exclude)) {
			unset($albums[$this->exclude]);
		}
		return $albums;
	}
	
	public function render( SK_Layout $Layout )
	{
		$list = $this->items();
		$total = app_PhotoAlbums::getAlbumsCount($this->profile_id, true);
		$show_all = ($this->per_page !== false) && ($total > $this->per_page);
		$Layout->assign('show_all_btn', $show_all);
		$Layout->assign('active', $this->active);
		$Layout->assign('items', $list);
		$Layout->assign('title', $this->title);
		$Layout->assign('profile_id', $this->profile_id);
		return parent::render($Layout);
	}

}
