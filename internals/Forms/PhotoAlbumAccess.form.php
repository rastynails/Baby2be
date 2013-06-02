<?php

class form_PhotoAlbumAccess extends SK_Form
{
    public function __construct()
    {
        parent::__construct('photo_album_access');
    }
    
    public function setup()
    {
        $password = new fieldType_password('password');
        $this->registerField($password);
        
        $this->registerField(new fieldType_hidden('albumId'));
        
        parent::registerAction('form_PhotoAlbumAccess_Process');
    }
}

class form_PhotoAlbumAccess_Process extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('unlock');
    }
    
    public function setup( SK_Form $form )
    {
        $this->required_fields = array('password');
            
        parent::setup($form);
    }
    
    
    public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
    {
        $album = app_PhotoAlbums::getAlbum($post_data['albumId']);
        
        if ( !$album->enterPassword($post_data['password']) )
        {
            $response->addError(SK_Language::text('components.photo_album_access.incorrect_password'), 'password');
            
            return;    
        }
        
        $response->reload();
    }
}
