<?php
require_once FBC_DIR_BOL . 'base_dao.php';
require_once FBC_DIR_BOL . 'auth.php';

class FBC_AuthDao extends FBC_BaseDao
{
    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'FBC_Auth';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return DB_TBL_PREFIX . 'fbconnect_auth';
    }
    
    /**
     * 
     * @param $remoteId
     * @return FBC_Auth
     */
    public function findByRemoteId( $remoteId )
    {
        $query = SK_MySQL::placeholder('SELECT * FROM `' . $this->getTableName() . '` WHERE `remoteId`="?"', $remoteId);
        
        return $this->fetchObject($query); 
    }
    
    /**
     * 
     * @param $remoteId
     * @return FBC_Auth
     */
    public function findByUserId( $userId )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `" . $this->getTableName() . "` WHERE `userId`=?", $userId);
        
        return $this->fetchObject($query); 
    }
    
    public function deleteByUserId( $userId )
    {
        $query = SK_MySQL::placeholder("DELETE FROM `" . $this->getTableName() . "` WHERE `userId`=?", $userId);
        
        SK_MySQL::query($query);
    }
    
    public function saveOrUpdate( FBC_Auth $entity )
    {
        $query = SK_MySQL::placeholder(
        	'SELECT * FROM `' . $this->getTableName() . '` WHERE `userId`=? AND `remoteId`="?"'
            , $entity->userId, $entity->remoteId);
            
        $entityDto = $this->fetchObject($query);
            
        if ( $entityDto !== null )
        {
            $entity->id = $entityDto->id;
        }
        
        return $this->save($entity);
    }
}