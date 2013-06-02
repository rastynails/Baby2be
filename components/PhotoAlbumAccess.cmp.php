<?php

class component_PhotoAlbumAccess extends SK_Component
{
    /**
     *
     * @var dto_PhotoAlbum
     */
    private $album;

    public function __construct( dto_PhotoAlbum $album )
    {
        parent::__construct('photo_album_access');

        $this->album = $album;
        $this->tpl_file = $album->getPrivacy() . '.tpl';

        SK_Language::defineGlobal('phototitle', '');
    }

    public function render( SK_Layout $layout )
    {
        $title = SK_Language::text('components.photo_album_access.title_' . $this->album->getPrivacy(), array( 'label' => $this->album->getView_label() ));
        $layout->assign('title', $title);

        return parent::render($layout);
    }

    public function handleForm( SK_Form $form )
    {
        $form->getField('albumId')->setValue($this->album->getId());
    }
}
