<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 05, 2008
 * 
 */


class httpdoc_UploadPhotoPage extends SK_HttpDocument
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('upload_photo');
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		switch (self::getTab()) {
			case 'general':
				$cmp = new component_UploadPhoto();
				break;
			case 'albums':
								
				if ($album = self::getAlbumId()) {
					$label = app_PhotoAlbums::getAlbum($album)->getView_label();
					$album_txt = SK_Language::text('components.upload_photo.page.header_album', array('album' => $label));
					
					SK_Language::defineGlobal('header_album', $album_txt);
					
					SK_Navigation::addBreadCrumbItem($label);
					$cmp = new component_PhotoAlbum(array('album_id'=>$album));
				} else {
					SK_Language::defineGlobal('header_album', ' ');
					$cmp = new component_MyPhotoAlbums();
				}
				break;
		}
		
		
		$Layout->assign('content_component', $cmp);
		$Layout->assign('tabs', $this->tabs());
		return parent::render( $Layout );
	}
	
	public function prepareHeader($header) {
		
	}
	
	public static function getTab() {
		if (!app_PhotoAlbums::isFeatureActive()) {
			return 'general';
		}
		
		$album = self::getAlbumId();
		$tab = SK_HttpRequest::$GET['tab'];
		
		if (!in_array($tab, array(
			'general',
			'albums'
		))) {
			$tab = 'general';
		}
		
		return $tab;
	}
	
	public static function getAlbumId() {
		return  intval(@SK_HttpRequest::$GET['album']);
	}
	
	private function tabs() {
		if (!app_PhotoAlbums::isFeatureActive()) {
			return array();
		}
		
		
		$tabs = array();
		
		$tab = self::getTab();
		
		$tabs[] = array(
						'href'	=>SK_Navigation::href('profile_photo_upload', 'tab=general'),
						'label'	=>SK_Language::text('components.upload_photo.tabs.general'),
						'active'=> $tab == 'general'
						);
		$tabs[] = array(
						'href'	=>SK_Navigation::href('profile_photo_upload', 'tab=albums'),
						'label'	=>SK_Language::text('components.upload_photo.tabs.albums'),
						'active'=> $tab == 'albums'
						);
			
		return $tabs;
	}

}