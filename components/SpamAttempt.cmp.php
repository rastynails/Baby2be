<?php

class component_SpamAttempt extends SK_Component
{
    public function __construct()
    {
        parent::__construct( 'spam_attempt' );
    }
    
    public function prepare(SK_Layout $Layout, SK_Frontend $Frontend)
    {
        if ( empty($_SESSION[app_Security::SESSION_NAME]['attempt']) )
        {
            SK_HttpRequest::showFalsePage();
        }

        unset( $_SESSION[app_Security::SESSION_NAME] );
        
        parent::prepare($Layout, $Frontend);
    }
}
