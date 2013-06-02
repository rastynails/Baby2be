<?php

class form_VideoUpload extends SK_Form
{

    public function __construct()
    {
        parent::__construct('video_upload');
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

        $status_vals = SK_MySQL::describe(TBL_PROFILE_VIDEO, 'privacy_status');
        $statuses = array();
        $statuses = explode(",", $status_vals->size());

        $privacy_status->setValues($statuses);

		$password = new fieldType_text('password');
		$password->maxlength = 32;
		parent::registerField($password);
		
        $video_file = new field_profile_video('profile_video');
        parent::registerField($video_file);

        $tag = new fieldType_text('tag');
        parent::registerField($tag);

        if ( SK_Config::section('video')->Section('other_settings')->get('enable_categories') )
        {
            $categories = new fieldType_select('category');
            $categories->setType('select');
            $categories->label_prefix = 'cat';
            $catArray = app_VideoList::getVideoCategories(true);
            $categories->setValues($catArray);
            parent::registerField($categories);
        }

        parent::registerAction('formVideoUpload_Upload');
        parent::registerAction('formVideoUpload_Embed');
    }

}

class formVideoUpload_Upload extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('upload');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('profile_id', 'title', 'privacy_status', 'profile_video');

        parent::setup($form);
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {
        if ( $post_data['profile_id'] != SK_HttpUser::profile_id() )
            return false;

        $tmp_file = new SK_TemporaryFile($post_data['profile_video']);

        $file_hash = md5(time());
        $file_ext = $tmp_file->getExtension();

        $destination = app_ProfileVideo::getVideoDir($file_hash, $file_ext);

        $title = trim($post_data['title']);
        $desc = trim($post_data['description']);
        $tag = trim($post_data['tag']);
        $profile_id = $post_data['profile_id'];
        $category_id = (int) $post_data['category'];
        $password = !empty($post_data['password']) ? htmlspecialchars($post_data['password']) : null;

        $error_ns = SK_Language::section('forms.video_upload.error_msg');
        $message_ns = SK_Language::section('forms.video_upload.msg');

        $video_mode = SK_Config::section('video')->get('media_mode');

        switch ( $video_mode )
        {
            case 'windows_media':
                try
                {
                    $video_id = app_ProfileVideo::addVideo($profile_id, $title, $desc, $post_data['privacy_status'], $file_hash, $file_ext, true, $category_id, $password);

                    switch ( $video_id )
                    {
                        case -1:
                        case -2:
                        case -3:
                            $response->addError($error_ns->text('video_add_error'));
                            break;
                        case -4:
                            $response->addError(SK_Language::section('components.video_upload')->text('video_limit_exceeded'));
                            break;
                        default:
                            $tmp_file->move($destination);
                            $service = new SK_Service('upload_media', $profile_id);
                            $service->checkPermissions();
                            $service->trackServiceUse();

                            $userAction = new SK_UserAction('media_upload', $profile_id);
                            $userAction->item = (int) $video_id;
                            $userAction->unique = $video_id;

                            $userAction->status = (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload') == true) ? 'active' : 'approval';
                            app_UserActivities::trace_action($userAction);

                            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                            {
                                $newsfeedDataParams = array(
                                    'params' => array(
                                        'feature' => FEATURE_VIDEO,
                                        'entityType' => 'media_upload',
                                        'entityId' => $video_id,
                                        'userId' => $profile_id,
                                        'status' => (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload') == true) ? 'active' : 'approval'
                                    )
                                );
                                app_Newsfeed::newInstance()->action($newsfeedDataParams);
                            }

                            if ( isset($tag) )
                                app_TagService::stAddEntityTags($video_id, $tag, 'video');

                            $response->addMessage($message_ns->text('video_added'));
                            $response->exec('window.location.reload();');
                            break;
                    }
                }
                catch ( SK_TemporaryFileException $e )
                {
                    $response->addError($e->getMessage());
                }
                break;

            case 'flash_video':
                $video_id = app_ProfileVideo::addVideo($profile_id, $title, $desc, $post_data['privacy_status'], $file_hash, $file_ext, false, $category_id. $password);

                switch ( $video_id )
                {
                    case -1:
                    case -2:
                    case -3:
                        $response->addError($error_ns->text('video_add_error'));
                        break;
                    case -4:
                        $response->addError(SK_Language::section('components.video_upload')->text('video_limit_exceeded'));
                        break;
                    default:
                        app_ProfileVideo::sheduleVideoForConvert($video_id, $tmp_file, $file_hash);

                        $service = new SK_Service('upload_media', $profile_id);
                        $service->checkPermissions();
                        $service->trackServiceUse();

                        $userAction = new SK_UserAction('media_upload', $profile_id);
                        $userAction->item = (int) $video_id;
                        $userAction->unique = $video_id;

                        $userAction->status = (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload') == true) ? 'active' : 'approval';

                        app_UserActivities::trace_action($userAction);

                        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                        {
                            $newsfeedDataParams = array(
                                'params' => array(
                                    'feature' => FEATURE_VIDEO,
                                    'entityType' => 'media_upload',
                                    'entityId' => $video_id,
                                    'userId' => $profile_id,
                                    'status' => (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload') == true) ? 'active' : 'approval'
                                )
                            );
                            app_Newsfeed::newInstance()->action($newsfeedDataParams);
                        }

                        if ( isset($tag) )
                            app_TagService::stAddEntityTags($video_id, $tag, 'video');

                        $response->addMessage($message_ns->text('video_added_for_convert'));
                        $response->exec('window.location.reload();');
                        break;
                }
                break;
        }
        return array('video_mode' => $video_mode);
    }
}

class formVideoUpload_Embed extends SK_FormAction
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
        $tag = trim($post_data['tag']);
        $file_hash = md5(time());
        $category_id = (int) $post_data['category'];
        $password = !empty($post_data['password']) ? htmlspecialchars($post_data['password']) : null;
        
        $error_ns = SK_Language::section('forms.video_upload.error_msg');
        $message_ns = SK_Language::section('forms.video_upload.msg');

        $video_id = app_ProfileVideo::addEmbeddableVideo($profile_id, $title, $desc, $post_data['privacy_status'], $file_hash, $post_data['code'], $category_id, $password);

        switch ( $video_id )
        {
            case -1:
            case -2:
            case -3:
                $response->addError($error_ns->text('video_add_error'));
                break;
            case -4:
                $response->addError(SK_Language::section('components.video_upload')->text('video_limit_exceeded'));
                break;
            default:
                $service = new SK_Service('upload_media', $profile_id);
                $service->checkPermissions();
                $service->trackServiceUse();

                if ( isset($tag) )
                    app_TagService::stAddEntityTags($video_id, $tag, 'video');

                $userAction = new SK_UserAction('media_upload', $profile_id);
                $userAction->item = $video_id;
                $userAction->title = $title;
                $userAction->description = $desc;

                $userAction->status = (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload') == true) ? 'active' : 'approval';
                app_UserActivities::trace_action($userAction);

                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_VIDEO,
                            'entityType' => 'media_upload',
                            'entityId' => $video_id,
                            'userId' => $profile_id,
                            'status' => (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload') == true) ? 'active' : 'approval'
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }

                $response->addMessage($message_ns->text('video_added'));
                $response->exec('window.location.reload();');
        }
    }
}

