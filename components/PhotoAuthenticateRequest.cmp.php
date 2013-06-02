<?php

class component_PhotoAuthenticateRequest extends SK_Component
{
    private $profileId;
    
    public function __construct( $profileId )
    {
        parent::__construct('photo_authenticate_request');

        $this->profileId = (int) $profileId;
    }
}


