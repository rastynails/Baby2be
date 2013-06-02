<?php

class component_EmailVerify extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('email_verify');
	}

	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend) {

		if( app_EmailVerification::processCheckVerificationCode() )
		{
                    $redirect_settings = SK_Config::section('navigation')->Section('settings');

                    $signin_redirect = $redirect_settings->signin_document_redirect;

                    if ( !empty($signin_redirect) )
                    {
                        SK_HttpRequest::redirect(SK_Navigation::href($signin_redirect));
                    }
                    else
                    {
                        $redirectDocument = $redirect_settings->join_document_redirect;
                        SK_HttpRequest::redirect(SK_Navigation::href($redirectDocument) );
                    }
		}

		return parent::prepare($Layout, $Frontend);
	}

	public function handleForm(SK_Form $Form) {
		$email = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'email');
		if ($email) {
			$Form->fields['email']->setValue($email);
		}
	}
}
