<?php

class form_MusicUpload extends SK_Form
{

    public function __construct()
    {
        parent::__construct('music_upload');
    }

    public function setup()
    {
        $profile_id = new fieldType_hidden('profile_id');
        parent::registerField($profile_id);

        $title = new fieldType_text('title');
        parent::registerField($title);

        $description = new fieldType_textarea('description');
        $description->maxlength = 1500;
        parent::registerField($description);

        $code = new fieldType_textarea('code');
        $code->maxlength = 3000;
        parent::registerField($code);

        $privacy_status = new fieldType_select('privacy_status');
        $privacy_status->setType('select');
        parent::registerField($privacy_status);

        $status_vals = SK_MySQL::describe(TBL_PROFILE_MUSIC, 'privacy_status');

        $statuses = array();
        $statuses = explode(",", $status_vals->size());

        $privacy_status->setValues($statuses);

        $music_file = new field_profile_music('music_file');
        parent::registerField($music_file);

        parent::registerAction('formMusicUpload_Upload');
        parent::registerAction('formMusicUpload_Embed');
    }

}

class formMusicUpload_Upload extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('upload');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('profile_id', 'title', 'privacy_status', 'music_file');

        parent::setup($form);
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {
        if ( $post_data['profile_id'] != SK_HttpUser::profile_id() )
        {
            return false;
        }

        $tmp_file = new SK_TemporaryFile($post_data['music_file']);

        $file_hash = md5(time());
        $file_ext = $tmp_file->getExtension();

        $destination = app_ProfileMusic::getMusicDir($file_hash, $file_ext);

        $title = trim($post_data['title']);
        $desc = trim($post_data['description']);
        $profile_id = $post_data['profile_id'];

        $error_ns = SK_Language::section('forms.music_upload.error_msg');
        $message_ns = SK_Language::section('forms.music_upload.msg');


        try
        {
            $music_id = app_ProfileMusic::addMusic($profile_id, $title, $desc, $post_data['privacy_status'], $file_hash, $file_ext);

            switch ( $music_id )
            {
                case -1:
                case -2:
                case -3:
                    $response->addError($error_ns->text('music_add_error'));
                    break;
                case -4:
                    $response->addError(SK_Language::section('components.music_upload')->text('music_limit_exceeded'));
                    break;
                default:
                    $tmp_file->move($destination);
                    $service = new SK_Service('upload_music', $profile_id);
                    $service->checkPermissions();
                    $service->trackServiceUse();

                    $userAction = new SK_UserAction('music_upload', $profile_id);
                    $userAction->item = $music_id;
                    $userAction->title = $title;
                    $userAction->unique = $music_id;
                    $userAction->description = $desc;

                    $userAction->status = (SK_Config::section('site')->Section('automode')->get('set_active_music_on_upload') == true) ? 'active' : 'approval';
                    app_UserActivities::trace_action($userAction);

                    if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                    {
                        $newsfeedDataParams = array(
                            'params' => array(
                                'feature' => FEATURE_MUSIC,
                                'entityType' => 'music_upload',
                                'entityId' => $music_id,
                                'userId' => $profile_id,
                                'status' => (SK_Config::section('site')->Section('automode')->get('set_active_music_on_upload') == true) ? 'active' : 'approval'
                            )
                        );
                        app_Newsfeed::newInstance()->action($newsfeedDataParams);
                    }

                    $response->addMessage($message_ns->text('music_added'));
                    $response->exec('window.location.reload();');
                    break;
            }
        }
        catch ( SK_TemporaryFileException $e )
        {
            $response->addError($e->getMessage());
        }


        return '1';
    }
}

class formMusicUpload_Embed extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('embed');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('profile_id', 'title', 'privacy_status', 'code');

        parent::setup($form);
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {
        $title = trim($post_data['title']);
        $desc = trim($post_data['description']);
        $profile_id = $post_data['profile_id'];
        $file_hash = md5(time());

        $error_ns = SK_Language::section('forms.music_upload.error_msg');
        $message_ns = SK_Language::section('forms.music_upload.msg');

        $music_id = app_ProfileMusic::addEmbeddableMusic($profile_id, $title, $desc, $post_data['privacy_status'], $file_hash, $post_data['code']);

        switch ( $music_id )
        {
            case -1:
            case -2:
            case -3:
                $response->addError($error_ns->text('music_add_error'));
                break;
            case -4:
                $response->addError(SK_Language::section('components.music_upload')->text('music_limit_exceeded'));
                break;
            default:
                $service = new SK_Service('upload_music', $profile_id);
                $service->checkPermissions();
                $service->trackServiceUse();

                $userAction = new SK_UserAction('music_upload', $profile_id);
                $userAction->item = $music_id;
                $userAction->title = $title;
                $userAction->unique = $music_id;
                $userAction->description = $desc;

                $userAction->status = (SK_Config::section('site')->Section('automode')->get('set_active_music_on_upload') == true) ? 'active' : 'approval';
                app_UserActivities::trace_action($userAction);

                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_MUSIC,
                            'entityType' => 'music_upload',
                            'entityId' => $music_id,
                            'userId' => $profile_id,
                            'status' => (SK_Config::section('site')->Section('automode')->get('set_active_music_on_upload') == true) ? 'active' : 'approval'
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }

                $response->addMessage($message_ns->text('music_added'));
                $response->exec('window.location.reload();');
        }
    }
}

