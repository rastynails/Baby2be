<?php

class form_EmailVerify extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('email_verify');
	}
	
	
	public function setup()
	{
		parent::registerField( new field_email() );
				
		parent::registerAction('form_EmailVerify_Process');
	}
	
	
	
}

class form_EmailVerify_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('email');
			
		parent::setup($form);
	}
	
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
	    $profileEmail = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'email');
	    
	    if ( $post_data['email'] != $profileEmail )
	    {
    	    $result = SK_MySQL::query(SK_MySQL::placeholder("
                SELECT COUNT(*) FROM " . TBL_PROFILE . " WHERE `email`='?'
            ", $post_data['email']))->fetch_cell();
            
            if ($result) {
                $response->addError(SK_Language::text("%components.email_verify.email_already_exists"));
                return false;
            }    
	    }
		
	    try 
	    {
    		if (app_EmailVerification::addRequestEmailVerification(SK_HttpUser::profile_id(), $post_data['email']) > 0) {
    			$response->addMessage(SK_Language::text('%forms.email_verify.messages.success'));
    		}
	    }
	    catch ( SK_EmailException $e )
	    {
	        return false;
	    }
	}
}
