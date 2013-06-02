<?php

class component_ResetPasswordPage extends SK_Component
{
    private $ident;
    
    public function __construct( $key )
    {
        parent::__construct('reset_password_page');
        
        $key = trim($key);
        
        if ( empty($key) )
        {
            SK_HttpRequest::showFalsePage();
        }
        
        $this->ident = app_Passwords::getIdentificator($key);
        
        if ( empty($this->ident) )
        {
            SK_HttpRequest::showFalsePage();
        }
    }
    
    public function handleForm( SK_Form $form )
    {
        $form->getField('ident')->setValue($this->ident);
    }
    
}
