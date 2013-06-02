<?php

class form_GroupAdd extends SK_Form
{

    public function __construct()
    {
        parent::__construct('group_add');
    }

    public function setup()
    {
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

        $join_type = new field_group_type('join_type');
        parent::registerField($join_type);

        $join_vals = SK_MySQL::describe(TBL_GROUP, 'join_type');
        $statuses = explode(",", $join_vals->size());
        $join_type->setValues($statuses);

        parent::registerField(new GroupUploadField('photo'));

        parent::registerAction('form_GroupAdd_Process');
    }
}

class GroupUploadField extends fieldType_file
{
    public function __construct( $name )
    {
        parent::__construct($name);
    }
    /**
     * @see fieldType_file::setup()
     *
     * @param SK_Form $form
     */
    public function setup( $form )
    {
        $this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
        $this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png', 'image/pjpeg');
        $this->max_file_size = 2 * 1024 * 1024;

        parent::setup($form);
    }

    /**
     * @see fieldType_file::preview()
     *
     * @param SK_TemporaryFile $tmp_file
     */
    public function preview( SK_TemporaryFile $tmp_file )
    {
        $file_url = $tmp_file->getURL();
        return '<div><img src="' . $file_url . '" width="100" />&nbsp;&nbsp;&nbsp;<a class="delete_file_btn" style="cursor:pointer;">Delete</a></div>';
    }
}

class form_GroupAdd_Process extends SK_FormAction
{

    public function __construct()
    {
        parent::__construct('process');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('title');

        parent::setup($form);
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
    {
        $service = new SK_Service('create_group', SK_httpUser::profile_id());

        if ( $service->checkPermissions() !== SK_Service::SERVICE_FULL )        {
            $response->addError($service->permission_message['message']);
        }        else        {
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
            
            $group_id = app_Groups::AddGroup(SK_HttpUser::profile_id(), $title, $description, $browse_type, $join_type, $post_data['photo']);

            if ( $group_id == -1 )
            {
                $response->addError(SK_Language::text('%forms.group_add.group_image_error'));
            }
            else if ( $group_id > 0 )
            {
                $action = new SK_UserAction('add_group', SK_HttpUser::profile_id());

                $active = SK_Config::section('site')->Section('automode')->set_active_group_on_creation;

                $action->status = ($active) ? 'active' : 'approval';
                $action->unique = $group_id;

                $action->item = $group_id;

                app_UserActivities::trace_action($action);

                $service->trackServiceUse();

                $response->addMessage(SK_Language::text('forms.group_add.group_added'));
                $response->redirect(SK_Navigation::href('group', array('group_id' => $group_id)));
            }
        }
    }
}
