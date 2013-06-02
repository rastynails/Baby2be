<?php

class component_ContactImporterButtons extends SK_Component
{

    public function __construct()
    {
        parent::__construct('contact_importer_buttons');
    }

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $this->frontend_handler = new SK_ComponentFrontendHandler('ContactImporterButtons');
        $this->frontend_handler->construct();

        $buttons = array(
            'google' => false,
            'facebook' => false
        );

        // Google
        if ( SK_Config::section('google')->enable )
        {
            $callbackUrl = SITE_URL . 'google/oauth.php';
            $clientId = SK_Config::section('google')->client_id;

            $authUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query(array(
                    'response_type' => 'code',
                    'client_id' => $clientId,
                    'redirect_uri' => $callbackUrl,
                    'state' => 'contacts',
                    'scope' => 'https://www.google.com/m8/feeds/'
            ));

            $this->frontend_handler->google(array(
                'popupUrl' => $authUrl
            ));

            $buttons['google'] = true;
        }

        if ( SK_Config::section('facebook_connect')->enable_invite )
        {

            $service = FBC_Service::getInstance();
            $Frontend->include_js_file($service->getFaceBookAccessDetails()->jsLibUrl);

            $options = array(
                'appId' => $service->getFaceBookAccessDetails()->appId,
                'status' => true, // check login status
                'cookie' => true, // enable cookies to allow the server to access the session
                'xfbml'  => true, // parse XFBML
                'oauth' => true // enable OAuth 2.0
            );

            $method = array(
                "method" => "apprequests",
                "message" => SK_Language::text('txt.fb_invite_txt'),
                                    "data" => array( "userId" => SK_HttpUser::profile_id() )
            );

            $Frontend->onload_js('FB.init(' . json_encode($options) . '); Fb_Invite = function() { FB.ui(' . json_encode($method) . '); };');

            $buttons['facebook'] = true;
        }

        $Layout->assign('buttons', $buttons);

        return parent::prepare($Layout, $Frontend);
    }

    public function render( SK_Layout $Layout )
    {
        return parent::render($Layout);
    }
}