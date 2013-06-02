<?php

require_once DIR_SITE_ROOT . 'facebook_connect' . DIRECTORY_SEPARATOR . 'init.php';

class component_InviteFriends extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('invite_friends');
	}

	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
            if (!app_Features::isAvailable( 33 )) {
                    return false;
            }

            if (SK_Config::section('profile_registration')->invite_access == 'admin') {
                    return false;
            }

            //$handler = new SK_ComponentFrontendHandler('InviteFriends');
            //$handler->construct();
            //$this->frontend_handler = $handler;

            parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{
		//$providers = app_ContactGrabber::emailProviders();
		//$Layout->assign('providers', $providers);
                $Layout->assign('contactGrabber', false);
		return parent::render($Layout);
	}

	public static function ajax_loadContacts(SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler)
	{
		try {
			$params->validate(
				array(
					'email' => 'string',
					'password'=>'string',
					'provider'=>'string'
					)
				, true);
		} catch (SK_HttpRequestException $e) {}

		//$preg = "/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9_]{2,}(\.\w{2})?$/i";
		$msg_section = SK_Language::section('components.invite_friends.import.messages');
		$error_section = SK_Language::section('components.invite_friends.import.errors');

		if( !trim($params->email)){
			$handler->error($error_section->text('incorrect_email'));
			return ;
		}

		if (!strlen($params->password)) {
			$handler->error($error_section->text('empty_password'));
			return ;
		}

		try {
			$contacts = app_ContactGrabber::contacts($params->email, $params->password, $params->provider);
		}catch (Exception $e){
			try {
				$handler->error($error_section->text($e->getMessage()));
				return;
			}
			catch (SK_LanguageException $lang_e){
				$handler->error($e->getMessage());
				return;
			}
		}

		$result = array();

		foreach ($contacts as $address=>$name){
			$result[] = array('name'=>$name, 'address'=>$address);
		}

		$handler->drawList($result);
	}

}
