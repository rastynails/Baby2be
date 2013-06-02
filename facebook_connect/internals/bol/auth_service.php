<?php

class FBC_AuthService
{
    /**
     * 
     * @var BOL_AuthDao
     */
    private $authDao;
    
    private function __construct()
    {
        $this->authDao = new FBC_AuthDao();
    }

    /**
     * Class instance
     *
     * @var FBC_AuthService
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return FBC_AuthService
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    /**
     * 
     * @param $remoteId
     * @return FBC_Auth
     */
    public function findByRemoteId( $remoteId )
    {
        return $this->authDao->findByRemoteId($remoteId);
    }
    
    /**
     * 
     * @param $userId
     * @return FBC_Auth
     */
    public function findByUserId( $userId  )
    {
        return $this->authDao->findByUserId($userId);
    }
    
    public function saveOrUpdate( FBC_Auth $entity )
    {
        return $this->authDao->saveOrUpdate($entity);
    }
    
    public function deleteByUserId( $userId )
    {
        return $this->authDao->deleteByUserId($userId);
    }
}