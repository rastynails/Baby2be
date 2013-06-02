<?php

class form_VideoEdit extends SK_Form
{

    public function __construct()
    {
        parent::__construct('video_edit');
    }

    public function setup()
    {
        $video_hash = new fieldType_hidden('hash');
        parent::registerField($video_hash);

        $title = new fieldType_text('title');
        parent::registerField($title);

        $description = new fieldType_textarea('description');
        $description->maxlength = 1500;
        parent::registerField($description);

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

        $categories = new fieldType_select('category');
        $categories->setType('select');
        $categories->label_prefix = 'cat';
        $catArray = app_VideoList::getVideoCategories(true);
        $categories->setValues($catArray);
        parent::registerField($categories);

        $profile_id = new fieldType_hidden('profile_id');
        parent::registerField($profile_id);

        parent::registerAction('formVideoEdit_Save');
    }

}

class formVideoEdit_Save extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('save');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('hash', 'title', 'privacy_status');

        parent::setup($form);
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {
        $title = trim($post_data['title']);
        $desc = trim($post_data['description']);
        $category = isset($post_data['category']) ? (int) $post_data['category'] : null;
$password = !empty($post_data['password']) ? htmlspecialchars($post_data['password']) : null;

        if ( isset($post_data['hash']) && strlen($title) && isset($post_data['privacy_status']) )
        {
            if ( app_ProfileVideo::updateVideo($post_data['hash'], $title, $desc, $post_data['privacy_status'], $category, $password) )
            {
                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $video_info = app_ProfileVideo::getVideoInfo($post_data['profile_id'], $post_data['hash']);

                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_VIDEO,
                            'entityType' => 'media_upload',
                            'entityId' => $video_info['video_id'],
                            'userId' => $video_info['profile_id'],
                            'status' => $video_info['status']
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }

                $response->addMessage(SK_Language::section('forms.video_edit.msg')->text('video_updated'));
            }
        }
        else
            $response->addError(SK_Language::section('forms.video_edit.msg')->text('video_not_updated'));

        return array('hash' => $post_data['hash']);

    }
}



