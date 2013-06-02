<?php

class component_IMListener extends SK_Component
{
    private $enable_sound;
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct('im_listener');

		try {
			$service = new SK_ServiceUse('instant_messenger');
		}
		catch ( SK_ServiceUseException $e ) {
			//$this->annul();
		}

		if ( SK_Config::section('chuppo')->get('enable_chuppo_im') ) {
			$this->annul();
		}

        $this->enable_sound =  SK_Config::section('site')->Section('additional')->Section('im')->enable_sound;
	}

	/**
	 * Component prepare.
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 * @return boolean
	 */
	public function prepare( SK_Layout $layout, SK_Frontend $frontend )
	{
            $frontend->include_js_file(URL_STATIC.'swfobject.js');

            $this->frontend_handler = new SK_ComponentFrontendHandler('IMListener');
            $this->frontend_handler->construct(6000, $this->enable_sound);
            $this->frontend_handler->swf_player_src = URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf';

            $layout->assign('im_enable_sound', $this->enable_sound);

            return parent::prepare($layout, $frontend);
	}

	/**
	 * Ajax ping callback.
	 *
	 * @param array $params->drawn_invitations
	 * @param SK_ComponentFrontendHandler $handler
	 * @param SK_AjaxResponse $response
	 */
	public static function ping(
		SK_HttpRequestParams $params,
		SK_ComponentFrontendHandler $handler,
		SK_AjaxResponse $response
	) {
		// validating entry params
		$params->validate(array(
			'drawn_invitations' => 'array'
		), true);

		$new_invitations = app_IM::getNewInvitations((array)$params->drawn_invitations);

		if ( count($new_invitations) ) {
			$handler->drawInvitations($new_invitations);
		}
	}
}
