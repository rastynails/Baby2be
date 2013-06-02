<?php

class form_UploadPhoto extends SK_Form
{
    public $profile_id;

    public function __construct()
    {
        parent::__construct('upload_photo');
    }

    public function setup()
    {
        $field = new field_upload_photo();
        parent::registerField($field);

        $field = new fieldType_hidden('slot_number');
        parent::registerField($field);

        $field = new fieldType_hidden('album_id');
        parent::registerField($field);

        parent::registerAction('form_UploadPhoto_Process');
    }



}

class form_UploadPhoto_Process extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('upload');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('upload_photo', 'slot_number');

        parent::setup($form);
    }


    public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
    {
        $profile_id = SK_HttpUser::profile_id();

        $album_id = intval($post_data['album_id']);

        if ( !$profile_id )        {
            $response->addError(SK_Language::section("components.upload_photo.upload.message")->text("wrong_profile_upload_error"));
            return array();
        }

        $slot = intval($post_data["slot_number"]);

        $service = new SK_Service("upload_photo");
        if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )        {
            return array('slot' => $slot, 'uploaded' => false, 'no_permited' => true, 'no_permited_msg' => $service->permission_message["message"]);
        }

        $uploaded = true;
        try        {
            app_ProfilePhoto::setProcessAlbum($album_id);
            $phot_id = app_ProfilePhoto::upload($post_data["upload_photo"], $slot, $profile_id);
            app_ProfilePhoto::unsetProcessAlbum();
        }        catch ( SK_ProfilePhotoException $e )        {
            $response->addError(SK_Language::section("forms.upload_photo.errors")->text($e->getErrorKey()), 'upload_photo');
            $uploaded = false;
        }

        if ( $uploaded )        {
            $service->trackServiceUse();

            $userAction = new SK_UserAction('photo_upload', $profile_id);
            $userAction->item = $phot_id;

            $automodeCS = new SK_Config_Section('automode', 2);
            
            $userAction->status = ($automodeCS->get('set_active_photo_on_upload') == true) ? 'active' : 'approval';

            app_UserActivities::trace_action($userAction);

            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_PHOTO,
                        'entityType' => 'photo_upload',
                        'entityId' => $phot_id,
                        'userId' => $profile_id,
                        'status' => ($automodeCS->get('set_active_photo_on_upload') == true) ? 'active' : 'approval'
                    )
                );
                app_Newsfeed::newInstance()->action($newsfeedDataParams);
            }
        }

        $photo_info = app_ProfilePhoto::getPhotoInfo($phot_id, app_ProfilePhoto::PHOTOTYPE_PREVIEW);
        return array('slot' => $slot, 'image' => $photo_info, 'uploaded' => $uploaded, 'create_thumb' => (app_ProfilePhoto::getAllPhotoCount() == 1));
    }
}
