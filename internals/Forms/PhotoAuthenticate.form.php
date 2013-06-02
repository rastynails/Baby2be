<?php


class form_PhotoAuthenticate extends SK_Form
{
    public function __construct()
    {
        parent::__construct('photo_authenticate');
    }
    
    public function setup()
    {
        $uploadField = new photo_field('photo');
        $this->registerField($uploadField);
        
        $this->registerAction('form_PhotoAuthenticate_Process');
    }
}


class form_PhotoAuthenticate_Process extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('send');
    }
    
   public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
   {
       $file = new SK_TemporaryFile($post_data['photo']);
       $profileId = SK_HttpUser::profile_id();
       app_PhotoAuthenticate::addResponce($profileId, $file);
       
       $response->reload();
   }
}

class photo_field extends fieldType_file
{
    public function setup( SK_Form $form )
    {
        parent::setup($form);
        $this->js_presentation['uploadComplete'] = 'function(){
            $(".submit_c").show();
        }';
        
        $this->js_presentation['onConstruct'] = 'function(input, form){
            $(".submit_c").hide();
        }';
        
        $this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
        $this->max_file_size = SK_Config::section("photo")->Section("general")->max_filesize * 1024 * 1024;
        
        $this->multifile = false;
    }
    
    
    public function preview( SK_TemporaryFile $tmp_file )
    {
        return '<div><img src="' . $tmp_file->getURL() . '" /><br /><a class="delete_file_btn" href="javascript://">' . SK_Language::text('forms._fields.file.delete_btn') . '</a></div>';
    }
    
    public function validateUserFile(SK_TemporaryFile $tmp_file) {
        parent::validateUserFile($tmp_file);

        $field_name = $this->getName();
        
        list($width, $height, $type) = getimagesize($tmp_file->getPath());
        $config = SK_Config::section("photo")->Section("general");
        
        if ($width > $config->max_width || $height > $config->max_height) 
        {
            $error_key = 'max_resolution_exceeded';
            try {
                $preferred_section = 'forms.'.$this->name.'.fields.'.$field_name.'.errors';
                $message = SK_Language::section($preferred_section)->text($error_key);
            }
            catch ( SK_LanguageException $e ) {
                $default_section = 'forms._fields.'.$field_name.'.errors';
                try {
                    $message = SK_Language::section($default_section)->text($error_key);
                } catch (SK_LanguageException $e) {
                    $default_section = 'forms._errors';
                    $message = SK_Language::section($default_section)->text($error_key);
                }
            }
            
            throw new SK_UserFileValidationException(
                $message,
                SK_UserFileValidationException::MAX_RESOLUTION_EXCEEDED
            );
        }
    }
    
}

