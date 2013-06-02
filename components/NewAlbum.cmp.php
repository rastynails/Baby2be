<?php

class component_NewAlbum extends SK_Component
{
	public function __construct(array $params = null) {
		parent::__construct('new_album');
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ($Layout,$Frontend ) {
		$mac = SK_Config::section('photo.general')->max_albums;
		$ac = app_PhotoAlbums::getAlbumsCount();
		
		$exceeded = ($ac >= $mac);
		$Layout->assign('exceeded', $exceeded);
	}

}