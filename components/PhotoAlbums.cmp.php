<?php

class component_PhotoAlbums extends SK_Component
{
	private $profile_id;
	
	private $per_page = 21;
	
	private $edit_mode = false;
	
	public function __construct( array $params = null )
	{
		if (isset($params['profile_id'])) {
			if ( !($this->profile_id = intval($params['profile_id'])) ) {
				$this->profile_id = SK_HttpUser::profile_id();
			}
		}
	
		if (isset($params['edit_mode'])) {
			$this->edit_mode = true;
		}
		
		parent::__construct('photo_albums');
	}
	
	public static function getPage() {
		$page = isset(SK_HttpRequest::$GET['page']) 
			? intval(SK_HttpRequest::$GET['page'])
			: 1;
		return $page; 
	}
	
	public function items() {
		$limit = array(
			'count' => $this->per_page,
			'offset' => $this->per_page * ( self::getPage()-1 )
		);
		return array(
			'items'	=> app_PhotoAlbums::getAlbums($this->profile_id, $limit),
			'total' => app_PhotoAlbums::getAlbumsCount($this->profile_id)
		);
	}
	
	public function render( SK_Layout $Layout )
	{
		$username = app_Profile::username($this->profile_id);
		SK_Language::defineGlobal('username', $username);
		SK_Navigation::addBreadCrumbItem($username, app_Profile::getUrl($this->profile_id));
		SK_Navigation::addBreadCrumbItem(SK_Language::text('components.photo_albums.header'));
		$list = $this->items();
		
		$paging = array(
			'pages'	=> 5,
			'on_page'	=> $this->per_page,
			'total'		=> $list['total'], 
		);
		$Layout->assign('paging', $paging);
		
		$Layout->assign('edit_mode', $this->edit_mode);
		
		$Layout->assign('items', $list['items']);			
		return parent::render($Layout);
	}

}
