<?php

require_once FBC_DIR_BOL . 'auth_service.php';

class FBC_AuthAdapter
{
    private $remoteId;
    
    /**
     * 
     * @var FBC_AuthService
     */
    private $authService;
    
    public function __construct($remoteId)
    {
        $this->remoteId = $remoteId;
        
        $this->authService = FBC_AuthService::getInstance();
    }
    
    public function getRemoteId()
    {
        return $this->remoteUserId;
    }
    
    public function isRegistered()
    {
        return (bool) $this->authService->findByRemoteId($this->remoteId);
    }
    
    public function register( $userId, $custom = null )
    {
        $entity = new FBC_Auth();
        $entity->userId = (int) $userId;
        $entity->remoteId = $this->remoteId;
        $entity->timeStamp = time();
        $entity->custom = $custom;

        return $this->authService->saveOrUpdate($entity);
    }
    
    /**
     *
     * @return OW_AuthResult
     */
    public function authenticate()
    {
        $entity = $this->authService->findByRemoteId($this->remoteId);
        if ( $entity === null )
        {
            return false; 
        }
        else
        {
            $userId = (int) $entity->userId;
        }
          
        $info = app_Profile::getFieldValues($userId, array('username', 'password'));
        try 
        {
            SK_HttpUser::authenticate($info['username'], $info['password'], false);
        }
        catch (SK_HttpUserException $e)
        {
            return false;
        }
        
        // Update profile activity info
		app_Profile::updateProfileActivity( $userId );
        
        return $userId;
    }
}