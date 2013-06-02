<?php

class BlogPostImage extends SK_Entity
{
	private $post_id;
	
	private $label;
	
	private $filename;
	
	private $profile_id;
	
	public function __construct( $post_id = null, $label = null, $filename = null, $profile_id = null )
	{
		$this->post_id = $post_id;
		$this->label = $label;
		$this->filename = $filename;
		$this->profile_id = $profile_id;
	}
	
	/**
	 * @return unknown
	 */
	public function getFilename() 
	{
		return $this->filename;
	}
	
	/**
	 * @param unknown_type $filename
	 */
	public function setFilename($filename) 
	{
		$this->filename = $filename;
	}
	
	/**
	 * @return unknown
	 */
	public function getLabel() 
	{
		return $this->label;
	}
	
	/**
	 * @param unknown_type $label
	 */
	public function setLabel($label) 
	{
		$this->label = $label;
	}
	
	/**
	 * @return unknown
	 */
	public function getPost_id() 
	{
		return $this->post_id;
	}
	
	/**
	 * @param unknown_type $post_id
	 */
	public function setPost_id($post_id) 
	{
		$this->post_id = $post_id;
	}
	
	/**
	 * @return unknown
	 */
	public function getProfile_id()
	{
		return $this->profile_id;
	}
	
	/**
	 * @param unknown_type $profile_id
	 */
	public function setProfile_id($profile_id)
	{
		$this->profile_id = $profile_id;
	}

}