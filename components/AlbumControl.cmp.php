<?php

class component_AlbumControl extends SK_Component
{
	private $album_id;

	public function __construct( array $params = null )
	{
		if (isset($params['album_id'])) {
			if ( !($this->album_id = intval($params['album_id'])) ) {
				$this->annul();
			}
		}

		parent::__construct('album_control');
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ($Layout,$Frontend ) {

		$this->frontend_handler = new SK_ComponentFrontendHandler('AlbumControl');
		$this->frontend_handler->construct($this->album_id);

		return parent::prepare($Layout,$Frontend);
	}

	public function handleForm( SK_Form $form )
	{
	    $album = app_PhotoAlbums::getAlbum($this->album_id);

	    $form->getField('password')->setValue($album->getPassword());
            $passwordProtectedEnabled = app_Features::isAvailable(66);
            $privacy = $album->getPrivacy();
            if ( $album->getPrivacy() == 'password_protected' && !$passwordProtectedEnabled )
            {
                $privacy = 'public';
            }
	    $form->getField('privacy')->setValue($privacy);
	    $form->getField('label')->setValue($album->getLabel());
	    $form->getField('album_id')->setValue($album->getId());
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render ($Layout ) {
		$album = app_PhotoAlbums::getAlbum($this->album_id);
		if (!$album) {
			return false;
		}
                
		$Layout->assign('album', $album);

		return parent::render($Layout);
	}

	public static function ajax_Remove($params, SK_ComponentFrontendHandler $handler) {
		$album_id = $params->album_id;

		if (app_PhotoAlbums::deleteAlbum($album_id)) {
			$handler->redirect(SK_Navigation::href('profile_photo_upload', array('tab'=>'albums')));
		}
	}

}
