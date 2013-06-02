<?php

class form_GroupEdit extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('group_edit');
	}
	
	public function setup()
	{
		$group_id = new fieldType_hidden('group_id');
		parent::registerField($group_id);
		
		$title = new fieldType_text('title');
		parent::registerField($title);
		
		$description = new fieldType_textarea('description');
		parent::registerField($description);
		$description->maxlength = 3000;
		
		$browse_type = new fieldType_select('browse_type');
		parent::registerField($browse_type);
		
		$browse_vals = SK_MySQL::describe(TBL_GROUP, 'browse_type');
		$statuses = array();
		$statuses = explode(",", $browse_vals->size());		
		$browse_type->setValues($statuses);
						
		//$join_type = new fieldType_select('join_type');
		//parent::registerField($join_type);
		$join_type = new field_group_type('join_type');
		parent::registerField($join_type);
				
		$join_vals = SK_MySQL::describe(TBL_GROUP, 'join_type');
		$statuses = explode(",", $join_vals->size());		
		$join_type->setValues($statuses);
		
		parent::registerField( new GroupUploadField( 'photo' ) );
		
		parent::registerAction('form_GroupEdit_Process');
	}
}

class GroupUploadField extends fieldType_file
{
	public function __construct( $name )
	{
		parent::__construct( $name );
	}
	/**
	 * @see fieldType_file::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup ( $form )
	{
		$this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
		$this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png','image/pjpeg');
		$this->max_file_size = 2*1024*1024;
		
		parent::setup( $form );
	}

	/**
	 * @see fieldType_file::preview()
	 *
	 * @param SK_TemporaryFile $tmp_file
	 */
	public function preview ( SK_TemporaryFile $tmp_file )
	{
		$file_url = $tmp_file->getURL();
		return '<div><img src="'.$file_url.'" width="100" />&nbsp;&nbsp;&nbsp;<a class="delete_file_btn" style="cursor:pointer;">'.SK_Language::text('%forms._fields.file.delete_btn').'</a></div>';
	}
}

class form_GroupEdit_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('process');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('group_id', 'title', 'browse_type', 'join_type');
		
		parent::setup($form);
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$group_id = intval(trim($post_data['group_id']));
		
		if ( $group_id && app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $group_id) )
		{
			$title = trim($post_data['title']);
			$description = trim($post_data['description']);
			$browse_type = trim($post_data['browse_type']);
			$join_type = $post_data['join_type'];
			
		    if ( $post_data['photo'] )
            {
                $tmp_file = new SK_TemporaryFile($post_data['photo']);
                $config = SK_Config::section("photo")->Section("general");
                
                $properties = GetImageSize($tmp_file->getPath());

                if ( $properties[0] > $config->max_width || $properties[1] > $config->max_height )
                {
                    $response->addError(SK_Language::text('forms.upload_photo.errors.max_resolution'));
                    return;
                }
            }
            
			$updated = app_Groups::updateGroup($group_id, $title, $description, $browse_type, $join_type, $post_data['photo'] );
			
			if ( $updated === true )
			{
                $response->addMessage(SK_Language::text('forms.group_edit.group_updated'));
                
                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_GROUP,
                            'entityType' => 'group_add',
                            'entityId' => $group_id,
                            'userId' => SK_HttpUser::profile_id()
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }

                $response->redirect(SK_Navigation::href('group', array('group_id' => $group_id)));
			}
			else if ( $updated == -1 )
			{
                $response->addError(SK_Language::text('%forms.group_add.group_image_error'));
			}
		}
	}
}
