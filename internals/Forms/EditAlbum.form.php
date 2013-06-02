<?php

class form_EditAlbum extends SK_Form
{

    public function __construct()
    {
        parent::__construct('edit_album');
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

        parent::registerAction('form_EditAlbum_Action');
    }
}

class form_EditAlbum_Action extends SK_FormAction
{

    public function __construct()
    {
        parent::__construct('save');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('label', 'album_id');

        parent::setup($form);
    }

    public function checkData( $data, SK_FormResponse $response, SK_Form $form )
    {
        if ( $data['privacy'] == 'password_protected' )
        {
            if ( !trim($data['password']) )
            {
                $response->addError(SK_Language::text('components.album_control.password_reqired'), 'password');

                return;
            }

            $service = new SK_Service("password_protected_album");
            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $response->addError($service->permission_message["message"]);
                return;
            }

            $service->trackServiceUse();
        }
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {
        $album = app_PhotoAlbums::getAlbum(intval($post_data['album_id']));
        $album->setLabel($post_data['label']);
        $album->setPrivacy($post_data['privacy']);

        if ( $post_data['privacy'] == 'password_protected' )
        {
            $album->setPassword($post_data['password']);
        }

        try
        {
            app_PhotoAlbums::SaveAlbum($album);


            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                switch ( $post_data['privacy'] )
                {
                    case 'friends_only':
                        $visibility = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW;
                        break;
                    case 'public':
                        $visibility = app_Newsfeed::VISIBILITY_FULL;
                        break;
                    default:
                        $visibility = app_Newsfeed::VISIBILITY_AUTHOR;
                        break;
                }

                $photos = app_PhotoAlbums::getAlbumPhotoIds($post_data['album_id']);
                foreach ( $photos as $photo_id )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_PHOTO,
                            'entityType' => 'photo_upload',
                            'entityId' => $photo_id,
                            'userId' => $profile_id,
                            'status' => app_ProfilePhoto::getPhoto($photo_id)->status,
                            'visibility' => $visibility
                        )
                    );

                    app_Newsfeed::newInstance()->updateVisibility($newsfeedDataParams);
                }
            }
        }
        catch ( app_PhotoAlbumCreation_Exception $e )
        {

        }

        $response->exec('window.location.reload(true)');
    }
}

