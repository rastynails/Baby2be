<?php

class form_MusicEdit extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('music_edit');
	}

	public function setup()
	{
		$music_hash = new fieldType_hidden('hash');
		parent::registerField($music_hash);
		
		$title = new fieldType_text('title');
		parent::registerField($title);
		
		$description = new fieldType_textarea('description');
		$description->maxlength = 1500;
		parent::registerField($description);
		
		$privacy_status = new fieldType_select('privacy_status');
		$privacy_status->setType('select');
		parent::registerField($privacy_status);
		
		$status_vals = SK_MySQL::describe(TBL_PROFILE_MUSIC,'privacy_status');
		$statuses = array();
		$statuses = explode(",",$status_vals->size());		
	
		$privacy_status->setValues($statuses);

        $profile_id = new fieldType_hidden('profile_id');
        parent::registerField($profile_id);
		
		parent::registerAction('formMusicEdit_Save');
	}
	
}

class formMusicEdit_Save extends SK_FormAction
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
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$title = trim($post_data['title']);
		$desc = trim($post_data['description']);
		
		if ( isset($post_data['hash']) && strlen($title) && isset($post_data['privacy_status']) )
		{
			if (app_ProfileMusic::updateMusic($post_data['hash'], $title, $desc, $post_data['privacy_status']))
			{
                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $music_info = app_ProfileMusic::getMusicInfo($post_data['profile_id'], $post_data['hash']);
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_MUSIC,
                            'entityType' => 'music_upload',
                            'entityId' => $music_info['music_id'],
                            'userId' => $music_info['profile_id'],
                            'status' => $music_info['status']
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }
				$response->addMessage(SK_Language::section('forms.music_edit.msg')->text('music_updated'));
			}	
		}
		else
			$response->addError(SK_Language::section('forms.music_edit.msg')->text('music_not_updated'));
		
		return array('hash' => $post_data['hash']);

	}
}



