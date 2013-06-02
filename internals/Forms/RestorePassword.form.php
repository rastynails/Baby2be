<?php

class form_RestorePassword extends SK_Form
{
    
    public function __construct()
    {
        parent::__construct('restore_password');
    }
    
    
    public function setup()
    {
        $new_password = new fieldType_password('new_password');
        $new_password_confirm = new fieldType_password('new_password_confirm');
        
        parent::registerField($new_password);
        parent::registerField($new_password_confirm);
        
        $field = new fieldType_hidden('ident');
        parent::registerField($field);
        
        $new_password->setRegExPatterns(SK_ProfileFields::get('password')->regexp);
        
        parent::registerAction('form_RestorePassword_Process');
    }
    
}

class form_RestorePassword_Process extends SK_FormAction
{
    public function __construct()
    {
        parent::__construct('change');
    }
    
    public function setup( SK_Form $form )
    {
        $this->required_fields = array('new_password', 'new_password_confirm');
            
        parent::setup($form);
    }
    
    public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
    {
        $lang_errors = SK_Language::section('forms.change_password.messages.error');
        
        if ( $data['new_password']!=$data['new_password_confirm'] ) {
            $response->addError($lang_errors->text('passwords_not_match'), 'new_password_confirm');
        }
    }

    public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
    {
        $lang_msg = SK_Language::section('forms.change_password.messages.success');
        
        if (app_Profile::changePassword($post_data['ident'], $post_data['new_password'])){
            $response->addMessage($lang_msg->text('success'));
            
            $username = app_Profile::getFieldValues($post_data['ident'], 'username');
            SK_HttpUser::authenticate($username, $post_data['new_password']);
            
            app_Passwords::deleteByIdentificator($post_data['ident']);
            
            $response->redirect(SK_Navigation::href('home'));
        }
    }
}
