<?php
require_once DIR_SITE_ROOT . 'facebook_connect' . DIRECTORY_SEPARATOR . 'init.php';

class component_FBCButton extends SK_Component
{
    private static $inited = false;

    private $type;
    private $label;
	private $size = 'medium';

    public function __construct($params = array())
    {
        parent::__construct('fbc_button');
        $this->type = empty($params['type']) ? 'login' : $params['type'];

        if (empty($params['label']))
        {
            $this->label = $this->type == 'login' ? SK_Language::text('components.fbc_button.login') : SK_Language::text('components.fbc_button.synchronize');
        }
        else
        {
            $this->label = $params['label'];
        }

		if (!empty($params['size']))
        {
            $this->size = $params['size'];
        }
    }

    public function prepare(SK_Layout $layout, SK_Frontend $frontend)
    {
        if (!SK_Config::section('facebook_connect')->enabled)
        {
            return false;
        }

        if ( $this->type == 'login' && SK_HttpUser::is_authenticated() )
        {
            return false;
        }

        if ( $this->type == 'synchronize' && !SK_HttpUser::is_authenticated() )
        {
            return false;
        }

        $service = FBC_Service::getInstance();
        $frontend->include_js_file($service->getFaceBookAccessDetails()->jsLibUrl);

        $requestUri = urlencode(sk_request_uri());
        $handler = new SK_ComponentFrontendHandler('FBCButton');
        $url = $this->type == 'login' ? SITE_URL . 'facebook_connect/login.php' : SITE_URL . 'facebook_connect/sinchronize.php';

        $layout->assign('label', $this->label);
	$layout->assign('size', $this->size);

        $options = array(
            'appId' => $service->getFaceBookAccessDetails()->appId,
            'status' => true, // check login status
            'cookie' => true, // enable cookies to allow the server to access the session
            'xfbml'  => true, // parse XFBML
            'channelURL' => $service->getFaceBookAccessDetails()->xdReceiverUrl, // channel.html file
            'oauth' => true // enable OAuth 2.0
        );

        $code = app_Invitation::getRegistrationCode();

        $handler->construct($options, sk_make_url($url, array(
            'backUri' => $requestUri,
            'registration_code' => $code
        )));

        $this->frontend_handler = $handler;

        return parent::prepare($layout, $frontend);
    }
}
