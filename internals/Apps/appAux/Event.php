<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 06, 2008
 * 
 * Desc: DTO Class for event table entries
 */

final class Event extends SK_Entity
{
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $description;
	
	/**
	 * @var integer
	 */
	private $start_date;
	
	/**
	 * @var integer
	 */
	private $end_date;
	
	/**
	 * @var integer
	 */
	private $create_date;
	
	/**
	 * @var string
	 */
	private $custom_location;
	
	/**
	 * @var string
	 */
	private $country_id;
	
	/**
	 * @var string
	 */
	private $state_id;
	
	/**
	 * @var string
	 */
	private $city_id;
	
	/**
	 * @var string
	 */
	private $zip;
	
	/**
	 * @var string
	 */
	private $address;
	
	/**
	 * @var string
	 */
	private $image;
	
	/**
	 * @var integer
	 */
	private $i_am_attand;
	
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var integer
	 */
	private $admin_status;

	/**
	 * @var boolean
	 */
	private $is_speed_dating;

    /**
     * @var boolean
     */
    private $notified;

    /**
     * @var boolean
     */
    private $search_by_location;

	/**
	 * Class constructor
	 *
	 * @param string $title
	 * @param string $description
	 * @param integer $start_date
	 * @param integer $end_date
	 * @param integer $create_date
	 * @param string $custom_location
	 * @param string $country_id
	 * @param string $state_id
	 * @param string $city_id
	 * @param string $zip
	 * @param string $address
	 * @param integer $image_id
	 * @param integer $i_am_attand
	 * @param integer $profile_id
	 * @param integer $admin_status
     * @param boolean $is_speed_dating
     * @param boolean $notified
     * @param boolean $search_by_location
	 */
	public function __construct( $title = null, $description = null, $start_date = null, $end_date = null, $create_date = null, $custom_location = null,
		 $country_id = null, $state_id = null, $city_id = null, $zip = null, $address = null, $image = null, $i_am_attand = 0, $profile_id = null, $admin_status = 0, $is_speed_dating = 0, $notified = 0, $search_by_location = 0 )
	{
		$this->title = $title;
		$this->description = $description;
		$this->start_date = (int)$start_date;
		$this->end_date = (int)$end_date;
		$this->create_date = (int)$create_date;
		$this->custom_location = $custom_location;
		$this->country_id = $country_id;
		$this->state_id = trim($state_id);
		$this->city_id = $city_id;
		$this->zip = $zip;
		$this->address = $address;
		$this->image = $image;
		$this->i_am_attand = $i_am_attand;
		$this->profile_id = (int)$profile_id;
		$this->admin_status = $admin_status;
		$this->is_speed_dating = $is_speed_dating;
        $this->notified = $notified;
        $this->search_by_location = $search_by_location;
	}
	
	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}
	
	/**
	 * @param string $address
	 */
	public function setAddress($address) {
		$this->address = $address;
	}
	
	/**
	 * @return string
	 */
	public function getCity_id() {
		return $this->city_id;
	}
	
	/**
	 * @param string $city_id
	 */
	public function setCity_id($city_id) {
		$this->city_id = $city_id;
	}
	
	/**
	 * @return string
	 */
	public function getCountry_id() {
		return $this->country_id;
	}
	
	/**
	 * @param string $country_id
	 */
	public function setCountry_id($country_id) {
		$this->country_id = $country_id;
	}
	
	/**
	 * @return integer
	 */
	public function getCreate_date() {
		return $this->create_date;
	}
	
	/**
	 * @param integer $create_date
	 */
	public function setCreate_date($create_date) {
		$this->create_date = (int)$create_date;
	}
	
	/**
	 * @return string
	 */
	public function getCustom_location() {
		return $this->custom_location;
	}
	
	/**
	 * @param string $custom_location
	 */
	public function setCustom_location($custom_location) {
		$this->custom_location = $custom_location;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return boolean
	 */
	public function getNotified() {
		return $this->notified;
	}

	/**
	 * @param boolean
	 */
	public function setNotified( $notified ) {
	    $this->notified = $notified;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * @return integer
	 */
	public function getEnd_date() {
		return $this->end_date;
	}
	
	/**
	 * @param integer $end_date
	 */
	public function setEnd_date($end_date) {
		$this->end_date = (int)$end_date;
	}
	
	/**
	 * @return integer
	 */
	public function getI_am_attand() {
		return $this->i_am_attand;
	}
	
	/**
	 * @param integer $i_am_attand
	 */
	public function setI_am_attand($i_am_attand) {
		$this->i_am_attand = (int)$i_am_attand;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}
	
	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}
	
	/**
	 * @return integer
	 */
	public function getProfile_id() {
		return $this->profile_id;
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
	public function getStart_date() {
		return $this->start_date;
	}
	
	/**
	 * @param integer $start_date
	 */
	public function setStart_date($start_date) {
		$this->start_date = (int)$start_date;
	}
	
	/**
	 * @return integer
	 */
	public function getState_id() {
		return $this->state_id;
	}
	
	/**
	 * @param integer $state_id
	 */
	public function setState_id($state_id) {
		$this->state_id = trim($state_id);
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
	 * @return string
	 */
	public function getZip() {
		return $this->zip;
	}
	
	/**
	 * @param string $zip
	 */
	public function setZip($zip) {
		$this->zip = $zip;
	}
	
	/**
	 * @return integer
	 */
	public function getAdmin_status ()
	{
		return $this->admin_status;
	}
	/**
	 * @param integer $admin_status
	 */
	public function setAdmin_status ( $admin_status )
	{
		$this->admin_status = (int)$admin_status;
	}

	/**
	 * @return integer
	 */
	public function getIs_speed_dating ()
	{
		return $this->is_speed_dating;
	}
	/**
	 * @param integer $is_speed_dating
	 */
	public function setIs_speed_dating ( $is_speed_dating )
	{
		$this->is_speed_dating = (int)$is_speed_dating;
	}


	/**
	 * @return integer
	 */
	public function getSearch_by_location ()
	{
		return $this->search_by_location;
	}
	/**
	 * @param integer $search_by_location
	 */
	public function setSearch_by_location( $search_by_location )
	{
		$this->search_by_location = (int)$search_by_location;
	}
}
