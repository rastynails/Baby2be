<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: DTO Class for blog_comment Table entries
 */

final class Comment extends SK_Entity
{
	/**
	 * @var integer
	 */
	private $author_id;
	
	/**
	 * @var string
	 */
	private $text;
	
	/**
	 * @var integer 
	 */
	private $entity_id;
	/**
	 * @var integer 
	 */
	private $entityType;    
	
	/**
	 * @var integer
	 */
	private $create_time_stamp;
	
	/**
	 * @var integer
	 */
	private $update_time_stamp;
	
	/**
	 * @var integer
	 */
	private $update_author_id;
	
	
	/**
	 * Class constructor
	 *
	 * @param integer $authorId
	 * @param string $text
	 * @param integer $entityId
	 * @param integer $createTimeStamp
	 * @param integer $updateTimeStamp
	 */
	public function __construct( $author_id = null, $text = null, $entity_id = null, $create_time_stamp = null, $update_time_stamp = null, $update_author_id = null, $entityType = null )
	{
		$this->author_id = (int)$author_id;
		$this->text = $text;
		$this->entity_id = (int)$entity_id;
		$this->create_time_stamp = (int)$create_time_stamp;
		$this->update_time_stamp = (int)$update_time_stamp;
		$this->update_author_id = (int)$update_author_id;
        $this->entityType = $entityType;
	}
	
	/**
	 * @return integer
	 */
	public function getAuthor_id() {
		return $this->author_id;
	}
	
	/**
	 * @param integer $author_id
	 */
	public function setAuthor_id($author_id) {
		$this->author_id = $author_id;
	}
	
	/**
	 * @return integer
	 */
	public function getCreate_time_stamp() {
		return $this->create_time_stamp;
	}
	
	/**
	 * @param integer $create_time_stamp
	 */
	public function setCreate_time_stamp($create_time_stamp) {
		$this->create_time_stamp = $create_time_stamp;
	}
	
	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * @param string $text
	 */
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
	 * @return integer
	 */
	public function getUpdate_time_stamp() {
		return $this->update_time_stamp;
	}
	
	/**
	 * @param integer $update_time_stamp
	 */
	public function setUpdate_time_stamp($update_time_stamp) {
		$this->update_time_stamp = $update_time_stamp;
	}
	
	/**
	 * @return integer
	 */
	public function getEntity_id ()
	{
		return (int)$this->entity_id;
	}
	
	/**
	 * @param integer $entity_id
	 */
	public function setEntity_id ( $entity_id )
	{
		$this->entity_id = (int)$entity_id;
	}
    
    /**
	 * @return string
	 */
	public function getEntityType ()
	{
		return $this->entityType;
	}
	
	/**
	 * @param string $entityType
	 */
	public function setEntityType ( $entityType )
	{
		$this->entityType = $entityType;
	}    
	
	/**
	 * @return integer
	 */
	public function getUpdate_author_id() {
		return $this->update_author_id;
	}
	
	/**
	 * @param integer $update_author_id
	 */
	public function setUpdate_author_id($update_author_id) {
		$this->update_author_id = (int)$update_author_id;
	}
}