<?php

class form_NewAlbum extends SK_Form
{

	public function __construct()
	{
		parent::__construct('new_album');
	}

	public function setup()
	{
            $field = new fieldType_text('label');
            $field->maxlength = 250;
            parent::registerField($field);

            $field = new fieldType_text('password');
            parent::registerField($field);

            parent::registerField(new fieldType_hidden('album_id'));

            $field = new fieldType_select('privacy');
            $values = array('public', 'friends_only');

            $passwordProtectedEnabled = app_Features::isAvailable(66);

            if ( $passwordProtectedEnabled )
            {
                $values[] = 'password_protected';
            }

            $field->setValues($values);
            parent::registerField($field);

            parent::registerAction('form_NewAlbum_Action');
	}

}

class form_NewAlbum_Action extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('create');
	}

	public function setup( SK_Form $form )
	{
		$this->required_fields = array('label');

		parent::setup($form);
	}

    public function checkData($data, SK_FormResponse $response, SK_Form $form )
    {
        if ( $data['privacy'] == 'password_protected' && !trim($data['password']) )
        {
           $response->addError(SK_Language::text('components.album_control.password_reqired'), 'password');
        }
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
    {
        if ( $post_data['privacy'] == 'password_protected' )
        {
            $service = new SK_Service("password_protected_album");
            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $response->addError($service->permission_message["message"]);
                return;
            }

            $service->trackServiceUse();
        }

        $album = app_PhotoAlbums::createAlbum();
        $album->setLabel($post_data['label']);
        $album->setPrivacy($post_data['privacy']);

        if ( $post_data['privacy'] == 'password_protected' )
        {
            $album->setPassword($post_data['password']);
        }

        try {
            app_PhotoAlbums::SaveAlbum($album);
        }
        catch (app_PhotoAlbumCreation_Exception $e) {}

        $response->exec('window.location.reload(true)');
    }
}



