<?php
require_once DIR_APPS.'appAux/ClassifiedsFileDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 13, 2009
 * 
 * Desc: Classifieds File Service Class
 */

final class app_ClassifiedsFileService
{
    private $filesCache = array();
	/**
	 * @var ClassifiedsFileDao
	 */
	private $classifiedsFileDao;
		
	/**
	 * @var array
	 */
	private static $classInstance;
	
	/**
	 * Class constructor
	 */
	private function __construct()
	{
		$this->classifiedsFileDao = new ClassifiedsFileDao();
	}
	
	/**
	 * Returns the only instance of the class
	 *
	 * @return app_ClassifiedsFileService
	 */
	public static function newInstance ()
	{
		if ( self::$classInstance === null )
			self::$classInstance = new self();
		return self::$classInstance;
	}
	
	/**
	 * Returns file path
	 *
	 * @param string $filename
	 * @return string
	 */
	public function getFilePath ( $filename )
	{
		return DIR_USERFILES.$filename;	
	}
	
	/**
	 * Returns file url
	 *
	 * @param string $filename
	 * @return string
	 */	
	public function getFileUrl ( $filename )
	{
		return URL_USERFILES.$filename;
	}
	
	/**
	 * Saves file
	 *
	 * @param ClassifiedsFile $file
	 */
	public function saveFile( ClassifiedsFile $file )
	{
		$this->classifiedsFileDao->saveOrUpdate( $file );	
	}	
		
	/**
	 * Saves file
	 *
	 * @param ClassifiedsFile $file
	 */
	public function saveItemFile( $file_code, $entity_id )
	{
		$tmp_file = new SK_TemporaryFile( $file_code );
		
		$new_file = new ClassifiedsFile( $entity_id, $tmp_file->getExtension() );
		
		$this->saveFile( $new_file );
		
		$new_file_name = 'item_file_'.$new_file->getId().'.'.$tmp_file->getExtension();
		
		$tmp_file->move( $this->getFilePath( $new_file_name ) );		
	}	

	/**
	 * Returns Item files
	 *
	 * @param int $entity_id
	 * @return array
	 */	
	public function getItemFiles ( $item_id )
	{
        if ( isset($this->filesCache[$item_id]) )
        {
            return $this->filesCache[$item_id];
        }

		$files = $this->classifiedsFileDao->findByEntityId( $item_id );
		
		$file_arr = array();
		
		foreach ($files as $file) 
		{
			$file_info['file_id'] = $file->getId();
			$file_info['entity_id'] = $file->getEntity_id();
			$file_info['extension'] = $file->getExtension();
			$file_info['file_url'] = $this->getFileUrl( 'item_file_'.$file_info['file_id'].'.'.$file_info['extension'] ); 
			$file_info['file_path'] = $this->getFilePath( 'item_file_'.$file_info['file_id'].'.'.$file_info['extension'] ); 
			
			$file_arr[] = $file_info;
		}
		
		return $file_arr;
	}


    public function getItemsFileList ( $item_id_list )
	{
		$fentityList = $this->classifiedsFileDao->findByEntityIdList($item_id_list);

        if ( !$fentityList )
        {
            return array();
        }

		$file_arr = array();
        
		foreach ($fentityList as $entityId => $files )
		{
            foreach ($files as $file)
            {
                $file_info = array();
                if ( !empty($file) )
                {
                    $file_info['file_id'] = $file->getId();
                    $file_info['entity_id'] = $file->getEntity_id();
                    $file_info['extension'] = $file->getExtension();
                    $file_info['file_url'] = $this->getFileUrl( 'item_file_'.$file_info['file_id'].'.'.$file_info['extension'] );
                    $file_info['file_path'] = $this->getFilePath( 'item_file_'.$file_info['file_id'].'.'.$file_info['extension'] );

                    $file_arr[$entityId][] = $file_info;

                    $this->filesCache[$file->getId()] = $file_info;
                }

                $this->filesCache[$file->getId()] = array();
            }
		}

		return $file_arr;
	}
	
	/**
	 * Returns Item's file
	 *
	 * @param int $file_id
	 * @return array
	 */	
	public function getItemFile ( $file_id )
	{
		$file = $this->classifiedsFileDao->findById( $file_id );
		$file_info = array();
		
		if ( $file )
		{
			$file_info['file_id'] = $file->getId();
			$file_info['extension'] = $file->getExtension();
			$file_info['file_path'] = $this->getFilePath( 'item_file_'.$file_info['file_id'].'.'.$file_info['extension'] ); 
		}
		
		return $file_info;
	}	
	
	/**
	 * Deletes Item's files
	 *
	 * @param int $item_id
	 */	
	public function deleteItemFiles ( $item_id )
	{
		$files = $this->getItemFiles( $item_id );
		
		foreach ($files as $file) 
		{
			@unlink( $file['file_path'] );
			$this->classifiedsFileDao->deleteById( $file['file_id'] );
		}
		
	}
	
	/**
	 * Deletes Item's file
	 *
	 * @param int $file_id
	 */	
	public function deleteItemFile ( $file_id )
	{
		if ( !$file_id ) {
			return false;
		}
		$file = $this->getItemFile( $file_id );
		if ( !$file ) {
			return false;
		}

		@unlink( $file['file_path'] );
		$this->classifiedsFileDao->deleteById( $file['file_id'] );

		return true;
	}	
	
	/** ---------------------------- static interface -------------------------------------- **/
	
	/**
	 * Returns Item files
	 *
	 * @param int $entity_id
	 * @return array
	 */
	public static function stGetItemFiles( $item_id )
	{
		$service = self::newInstance();
		
		return $service->getItemFiles( $item_id );		
	}
	
	/**
	 * Deletes Item's files
	 *
	 * @param int $item_id
	 */
	public static function stDeleteItemFiles ( $item_id )
	{
		$service = self::newInstance();
		
		$service->deleteItemFiles( $item_id );
	}
	
	/**
	 * Deletes Item's file
	 *
	 * @param int $file_id
	 */
	public static function stDeleteItemFile ( $file_id )
	{
		$service = self::newInstance();
		
		return $service->deleteItemFile( $file_id );
	}	
	
}