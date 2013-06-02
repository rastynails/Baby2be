<?php

class component_AlbumPhotoList extends component_PhotoList 
{
	private $album_id;
	
	public function __construct($params = array()) {
		if (!isset($params['album_id'])) {
			$this->annul();
		}
		$this->album_id = intval($params['album_id']);
		parent::__construct($params);
	}
	
	public function items()
	{
		return app_PhotoList::AlbumPhotos($this->album_id);
    }
	
}