<?php
define('MENU_ACTIVE_BRANCH',1);
define('MENU_FULL_TREE',2);
define('MENU_ROOT',0);

class SK_Navigation
{
	const	MENU_ACTIVE_BRANCH = 1,
			MENU_FULL_TREE = 2,
			MENU_ROOT = 0;

	private static $modules = array();

	private static $custom_breadcrumb = array();

	private static $nodes = array();

    private static $menus;
    private static $menuItems;
    private static $membershipLinks;
    public static $documents;

    public static function init()
    {
        if( !empty(self::$menuItems) )
        {
            return;
        }
        app_Profile::getFieldValues(136);
        self::$menuItems = array();

        $query = "SELECT * FROM `".TBL_MENU."`";
		self::$menus = SK_MySQL::queryForList($query);

        $menuArr = array();

        foreach ( self::$menus as $item )
        {
            $menuArr[$item['menu_id']] = $item['menu_name'];
        }

        $query = "SELECT * FROM `".TBL_MENU_ITEM."` ORDER BY `order`";
        $result = SK_MySQL::queryForList($query);

        foreach ( $result as $item )
        {
            self::$menuItems[$item['menu_item_id']] = $item;
            self::$menuItems[$item['menu_item_id']]['menu_name'] = $menuArr[$item['menu_id']];
        }

        self::$documents = array();

        $result = SK_MySQL::queryForList('SELECT * FROM `' . TBL_DOCUMENT . "`");

        foreach ( $result as $item )
        {
            self::$documents[$item['document_key']] = $item;
        }
        
        $query = "SELECT * FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."`";
        $result = SK_MySQL::queryForList($query);

        foreach ( $result as $item )
        {
            if(!isset(self::$membershipLinks[$item['membership_type_id']]))
            {
                self::$membershipLinks[$item['membership_type_id']] = array();
            }

            self::$membershipLinks[$item['membership_type_id']][] = $item['menu_item_id'];
        }
    }

    public static function href( $document_key, $params = null , $mod_rewrite_ignore = false)
	{
		if ( !($document_key = trim($document_key)) ) {
			return '';
		}

		if ( !$mod_rewrite_ignore && isset(self::$nodes[$document_key]) && SK_Config::Section('navigation')->Section("settings")->mod_rewrite){

			if ( is_string($params) && strlen(trim($params))){
				parse_str($params, $params);

			}elseif (!is_array($params)){
				$params = array();
			}
			$result = call_user_func(self::$nodes[$document_key], $params);
			$url = isset($result) ? $result : sk_make_url(self::getDocument($document_key)->url, $params);

		}else {
			$url = sk_make_url(self::getDocument($document_key)->url, $params);
		}

		if ( SK_Config::section('navigation.settings')->display_index )
		{
            $url = str_replace('/index.php', '/', $url);
		}

		return $url;
	}


	/**
	 * Loads a module which must parse mod_rewrite url
	 *
	 * @param string $module_name
	 * @return boolean
	 */
	public static function LoadModule( $module_name )
	{
		if ( !SK_Config::Section('navigation')->Section("settings")->mod_rewrite )
			return false;

		if (!($module_name = trim($module_name))){
			return false;
		}

		if (isset(self::$modules[$module_name])){
			return true;
		}

		$class = 'nav_'.$module_name;
		self::$modules[$module_name] = new $class();
		return true;
	}

	public static function register_node( $document_key, $calback )
	{
		if(isset(self::$nodes[$document_key])){
			trigger_error('The node "'.$document_key.'" already registered in "'.__CLASS__.'! ',E_USER_NOTICE);
			return false;
		}
		self::$nodes[$document_key] = $calback;
		return true;
	}

	public static function getRealUrl( $url )
	{
		if(count(self::$modules))
		{
			foreach (self::$modules as $module)
			{
				$_url = $module->parseUrl($url);
				if ( (isset($_url) && strlen( trim($_url) )) && $_url !=$url){

					return $_url;
				}
			}
		}
		return null;
	}

	/**
	 * Returns an object of SK_NavigationDocument.
	 *
	 * @param string $document_key
	 * @return SK_NavigationDocument
	 */
	public static function getDocument( $document_key )
	{
		return new SK_NavigationDocument($document_key);
	}


	private static function prepare_meta_info()
	{
		if (!$this->document) return false;
		try {
			$section = SK_Language::section('nav_doc_item');
			$this->document['title'] = $section->section('titles')->key_exists($this->document['document_key']) ?
										$section->section('titles')->text($this->document['document_key']) :
										$section->section('titles')->text('_default');

			$this->document['header'] = $section->section('headers')->key_exists($this->document['document_key']) ?
										$section->section('headers')->text($this->document['document_key']) :
										$section->section('headers')->text('_default');

			$this->document['keywords'] = $section->section('keywords')->key_exists($this->document['document_key']) ?
										$section->section('keywords')->text($this->document['document_key']) :
										$section->section('keywords')->text('_default');

			$this->document['description'] = $section->section('descriptions')->key_exists($this->document['document_key']) ?
										$section->section('descriptions')->text($this->document['document_key']) :
										$section->section('descriptions')->text('_default');
		}
		catch (SK_LanguageException $e)
		{
			if(DEV_MODE)
			{
				printArr($e->getMessage());
			}
			return false;
		}
		return true;
	}

	private static $removed_bread_crumb_items_count = 0;

	public static function removeBreadCrumbItem() {
		return self::$removed_bread_crumb_items_count++;
	}

	public static function addBreadCrumbItem($label, $url = null) {
		return self::$custom_breadcrumb[] = array('label' => $label, 'url' => $url);
	}

	public static function getCrumbActions() {
		return array(
			'add' 	=> self::$custom_breadcrumb,
			'remove'=> self::$removed_bread_crumb_items_count
		);
	}

	public static function BreadCrumb()
	{
		$document_key = SK_HttpRequest::getDocument()->document_key;

		if (!isset($document_key)){
			return array();
		}

		$_breadcrumb_arr = array();
		$_breadcrumb_arr[] = $document_key;

		while ( true )
		{
            $documentInfo = self::$documents[$document_key];
            $_parent_doc_key = $documentInfo['parent_document_key'];

			if ( !$_parent_doc_key )
				break;
			else
				$_breadcrumb_arr[] = $_parent_doc_key;

			$document_key = $_parent_doc_key;
		}

		return $_breadcrumb_arr;

	}


	public static function documentsStructure( $parent_document='' )
	{
		$_query = "SELECT * FROM `".TBL_DOCUMENT."`
			WHERE `parent_document_key` = '".$parent_document_key."' AND `status`='1' ORDER BY `order`";
		$_result = SK_MySQL::query($_query );


		$_docs =array();
		if ( $_result )
		{
			while( $document_info = $_result->fetch_assoc() )
			{
				$_docs[$document_info['document_key']] = $document_info;

				// check if subdocuments exists
				$_query = "SELECT `document_key` FROM `".TBL_DOCUMENT."`
				WHERE `parent_document_key`='".$document_info['document_key']."' AND `status` = '1'
				ORDER BY `order`
				LIMIT 1";

				if ( SK_MySQL::query($_query )->fetch_cell() )
				{
					$_docs[$document_info['document_key']]['sub_docs'] = self::getDocumentStructure( $document_info['document_key'] );
				}
			}
		}

		return $_docs;
	}



	//======================================================================================

	private static function get_active_menu_item( $menu_name, $document_key = null )
	{
		$document_key = isset($document_key) ? $document_key : SK_HttpRequest::getDocument()->document_key;

        $result = self::findMenuItemByDocKeyAndMenuName($document_key,$menu_name);

		if(empty($result)){
            $docInfo = self::$documents[$document_key];
			$parent_doc_key = $docInfo['parent_document_key'];

			if ($parent_doc_key){
				return self::get_active_menu_item($menu_name, $parent_doc_key);
			}

		}

		return	$result;
	}

    private static function findMenuItemByDocKeyAndMenuName( $docKey, $menuName )
    {
        $result = null;

        foreach ( self::$menuItems as $item )
        {
            if( $item['document_key'] == $docKey && $item['menu_name'] == $menuName )
            {
                if( $result == null )
                {
                    $result = $item;
                }
                else
                {
                    if( $item['parent_menu_item_id'] > $result['parent_menu_item_id'] )
                    {
                        $result =  $item;
                    }
                }
            }
        }

        if( $result )
        {
            $result = Object($result);
        }

        return $result;
    }

    private static function get_active_items_branch( $active_menu_item_id )
	{
		$branch = array();

		if( !$active_menu_item_id ) return array();

        if( empty(self::$menuItems[$active_menu_item_id]) )
        {
            return $branch;
        }

		$parent_id = self::$menuItems[$active_menu_item_id]['parent_menu_item_id'];

		$branch[] = $active_menu_item_id;

		return array_merge(self::get_active_items_branch($parent_id), $branch);
	}

	private static function get_menu( $menu_name, $recursive = 0, $parent_menu_item_id = 0 )
	{
		static $active_items_branch = array(), $menu_ids = array(), $membership_type_id = null;

		if ( !isset($menu_ids[$menu_name]) ) {

			if($active_menu_item = self::get_active_menu_item($menu_name))
            {
				$active_items_branch = self::get_active_items_branch($active_menu_item->menu_item_id);
            }

            foreach ( self::$menus as $id => $name )
            {
                if( $name == $menu_name )
                {
                    $menu_ids[$menu_name] = $id;
                }
            }
		}

		if( $membership_type_id == null )
        {
            $membership_type_id = app_Profile::MembershipTypeId();
        }

		$menu_items = array();

        $myResult = array();

        foreach ( self::$menuItems as $item )
        {
            $id = $item['menu_item_id'];
            
            if( in_array($id, self::$membershipLinks[$membership_type_id]) && 
                self::$menuItems[$id]['parent_menu_item_id'] == $parent_menu_item_id &&
                self::$menuItems[$id]['menu_name'] == $menu_name )
            {
                $myResult[] = array(
                    'name' => self::$menuItems[$id]['name'],
                    'menu_item_id' => self::$menuItems[$id]['menu_item_id'],
                    'document_key' => self::$menuItems[$id]['document_key'],
                    'url' => self::$documents[self::$menuItems[$id]['document_key']]['url']
                );
            }
        }

        $docKeys = array();

		foreach ( $myResult as $row )
		{
			$is_active = in_array($row['menu_item_id'], $active_items_branch);

			$docKeys[$row['menu_item_id']] = $row['document_key'];

			$menu_items[$row['menu_item_id']] =
				array(
					'label'		=>	SK_Language::text('nav_menu_item.'. $row['name']),
					'active'	=>	$is_active,
                    'href'		=>	self::href($row['document_key']),
					'class'		=>	$row['name'],
					'sub_menu'	=>	($recursive === 2 || ($recursive && $is_active))
										? self::get_menu($menu_name, $recursive, $row['menu_item_id'])
										: array()
				);
		}

		return $menu_items;
	}

	//==========================================================================================================


	public static function getActiveMenuItemId($menu_name, $level=0)
	{
		$active_branch = self::get_active_items_branch(@self::get_active_menu_item($menu_name)->menu_item_id);
		return $active_branch[$level];
	}

	public static function menu( $menu, $type=self::MENU_ROOT, $level=0 )
	{
		if($type == self::MENU_ACTIVE_BRANCH && $level )
		{
			$level--;
			$active_branch = self::get_active_items_branch(@self::get_active_menu_item($menu)->menu_item_id);

			if(count($active_branch))
				return self::get_menu($menu,self::MENU_ACTIVE_BRANCH,@$active_branch[$level]);
		}
		else{
			return self::get_menu($menu,$type);
		}

	}

	private static $trusted_dirs = array();

	private static function area_dir($url) {
		$tmp = explode("/", $url);
		if (array_pop($tmp)) {
			$tmp[] ="";
			$url = implode($tmp, "/");
		}
		return $url;
	}

	public static function setTrustedDir($url) {
		self::$trusted_dirs[] = self::area_dir($url);
	}

	public static function setTrustedFile($file_url) {
		self::$trusted_dirs[] = $file_url;
	}

	public static function isAreaTrusted($url) {
		foreach (self::$trusted_dirs as $item) {
			if (strpos($url, $item) === 0) {
				return true;
			}
		}
		return false;
	}

	public static function isCurrentArea($url) {
		return (strpos(sk_make_url(), $url) === 0);
	}


}


SK_Navigation::init();