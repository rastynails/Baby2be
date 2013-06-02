<?php

class form_SignIn extends SK_Form
{

	public function __construct()
	{
		parent::__construct('sign_in');
	}


	public function setup()
	{
		$login = new fieldType_text('login');
		parent::registerField($login);

		$password = new fieldType_password('password');
		parent::registerField($password);

		$remember_me = new fieldType_checkbox('remember_me');
		parent::registerField($remember_me);

		parent::registerAction('form_SignIn_Process');
	}



}

class form_SignIn_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('process');
	}

	public function setup( SK_Form $form )
	{
		$this->required_fields = array('login', 'password');

		parent::setup($form);
	}


	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
            if ( app_Security::getInstance()->check($post_data['login']) )
            {
                $response->redirect( SITE_URL . 'security.php' );
                return;
            }
		try {
			$authenticated = SK_HttpUser::authenticate($post_data['login'], $post_data['password']);
		}
		catch (SK_HttpUserException $e){
			$field = null;
			switch ($e->getCode()){
				case 1:
					$field = 'login';
					break;
				case 2:
					$field = 'password';
					break;
			}
			$response->addError($e->getMessage());
			$authenticated = false;
		}

		if ( $authenticated ) {
			if($post_data['remember_me']){
				app_Profile::setLoginCookies(SK_HttpUser::username());
			}
			$response->addMessage(SK_Language::section("forms.sign_in.msg")->text("login_complete"));

                        $redirect_document = SK_Config::section('navigation')->Section('settings')->signin_document_redirect;
                        if ( !empty($redirect_document) )
                        {
                            $response->redirect(SK_Navigation::href($redirect_document));
                        }
                        else
                        {
                            $response->exec('window.location.reload();');
                        }
		}
	}
}
