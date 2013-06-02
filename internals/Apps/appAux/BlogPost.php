<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: DTO Class for blog_post Table entries
 */

final class BlogPost extends SK_Entity
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $text;
	
	/**
	 * @var string
	 */
	private $mored_text;
	
	/**
	 * @var string
	 */
	private $preview_text;
	
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
	private $view_count;
	
	/**
	 * @var integer
	 */
	private $profile_status;
	
	/**
	 * @var integer
	 */
	private $admin_status;
	
	/**
	 * @var integer
	 */
	private $is_news;

	
	/**
	 * Class constructor
	 *
	 * @param integer $profile_id
	 * @param string $title
	 * @param string $text
	 * @param string $mored_text
	 * @param string $preview_text
	 * @param integer $create_time_stamp
	 * @param integer $update_time_stamp
	 * @param integer $view_count
	 * @param integer $profile_status
	 * @param integer $admin_status
	 * @param integer $is_news
	 */
	public function __construct( $profile_id = null, $title = null, $text = null, $mored_text = null, $preview_text = null, 
		$create_time_stamp = null, $update_time_stamp = null, $view_count = 0, $profile_status = 0, $admin_status = 0, $is_news = 0 )
	{
		$this->profile_id = (int)$profile_id;
		$this->title = $title;
		$this->text = $text;
		$this->mored_text = $mored_text;
		$this->preview_text = $preview_text;
		$this->create_time_stamp = (int)$create_time_stamp;
		$this->update_time_stamp = (int)$update_time_stamp;
		$this->view_count = (int)$view_count;
		$this->profile_status = ( (int)$profile_status === 1 ? 1 : 0 );
		$this->admin_status = ( (int)$admin_status === 1 ? 1 : 0 );	
		$this->is_news = ( (int)$is_news === 1 ? 1 : 0 );
	}
	
	/**
	 * @return integer
	 */
	public function getAdmin_status() {
		return (int)$this->admin_status;
	}
	
	/**
	 * @param integer $admin_status
	 */
	public function setAdmin_status($admin_status) {
		$this->admin_status = ( (int)$admin_status === 1 ? 1 : 0 );
	}
	
	/**
	 * @return integer
	 */
	public function getCreate_time_stamp() {
		return (int)$this->create_time_stamp;
	}
	
	/**
	 * @param integer $create_time_stamp
	 */
	public function setCreate_time_stamp($create_time_stamp) {
		$this->create_time_stamp = (int)$create_time_stamp;
	}
	
	/**
	 * @return integer
	 */
	public function getIs_news() {
		return (int)$this->is_news;
	}
	
	/**
	 * @param integer $is_main
	 */
	public function setIs_news($is_news) {
		$this->is_news = ( (int)$is_news === 1 ? 1 : 0 );
	}
	
	/**
	 * @return string
	 */
	public function getMored_text() {
		return $this->mored_text;
	}
	
	/**
	 * @param string $mored_text
	 */
	public function setMored_text($mored_text) {
		$this->mored_text = $mored_text;
	}
	
	/**
	 * @return string
	 */
	public function getPreview_text() {
		return $this->preview_text;
	}
	
	/**
	 * @param string $preview_text
	 */
	public function setPreview_text($preview_text) {
		$this->preview_text = $preview_text;
	}
	
	/**
	 * @return integer
	 */
	public function getProfile_id() {
		return (int)$this->profile_id;
	}
	
	/**
	 * @param integer $profile_id
	 */
	public function setProfile_id($profile_id) {
		$this->profile_id = (int)$profile_id;
	}
	
	/**
	 * @return integer
	 */
	public function getProfile_status() {
		return (int)$this->profile_status;
	}
	
	/**
	 * @param integer $profile_status
	 */
	public function setProfile_status($profile_status) {
		$this->profile_status = ( (int)$profile_status === 1 ? 1 : 0 );
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
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * @return integer
	 */
	public function getUpdate_time_stamp() {
		return (int)$this->update_time_stamp;
	}
	
	/**
	 * @param integer $update_time_stamp
	 */
	public function setUpdate_time_stamp($update_time_stamp) {
		$this->update_time_stamp = (int)$update_time_stamp;
	}
	
	/**
	 * @return integer
	 */
	public function getView_count() {
		return (int)$this->view_count;
	}
	
	/**
	 * @param integer $view_count
	 */
	public function setView_count($view_count) {
		$this->view_count = (int)$view_count;
	}
	
	public static function __set_state( array $params )
	{
		$obj = new self();
		
		foreach ( $params as $prop => $val )
			$obj->$prop = $val;

		return $obj;
	}
}