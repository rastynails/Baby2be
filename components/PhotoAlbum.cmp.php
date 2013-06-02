<?php

class component_PhotoAlbum extends SK_Component
{
	/**
	 * @var dto_PhotoAlbum 
	 */
	private $album;
	
	public function __construct( array $params = null )
	{
		if (isset($params['album_id'])) {
			try {
				$this->album = app_PhotoAlbums::getAlbum(intval($params['album_id']));
			} catch (app_PhotoAlbumSystem_Exception $e) {
				$this->annul();
			}
		}
		parent::__construct('photo_album');
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render ($Layout ) {
		$Layout->assign('album_id', $this->album->getId());
		return parent::render($Layout);
	}


	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm ($form ) {
		$form->getField('album_id')->setValue($this->album->getId());
		$form->getField('label')->setValue($this->album->getLabel());
	}


}
