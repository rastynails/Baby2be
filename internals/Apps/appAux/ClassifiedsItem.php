<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 08, 2009
 * 
 * Desc: DTO Class for classifieds_item Table entries
 */

final class ClassifiedsItem extends SK_Entity 
{
	/**
	 * @var int
	 */
	private $profile_id;

	/**
	 * @var string
	 */
	private $entity;
	
	/**
	 * @var int
	 */
	private $group_id;

	/**
	 * @var string
	 */
	private $title;	
	
	/**
	 * @var string
	 */
	private $description;		

	/**
	 * @var string
	 */
	private $budget_min;	
	
	/**
	 * @var string
	 */
	private $budget_max;	

	/**
	 * @var string
	 */
	private $price;	
	
	/**
	 * @var string
	 */
	private $payment_dtls;
	
	/**
	 * @var string
	 */
	private $currency;
		
	/**
	 * @var int
	 */
	private $create_stamp;	
		
	/**
	 * @var int
	 */
	private $start_stamp;	
	
	/**
	 * @var int
	 */
	private $end_stamp;	
		
	/**
	 * @var int
	 */
	private $edit_stamp;		
	
	/**
	 * @var int
	 */
	private $edited_by_profile_id;
	
	/**
	 * @var int
	 */
	private $allow_comments;	

	/**
	 * @var int
	 */
	private $allow_bids;

	/**
	 * @var int
	 */
	private $limited_bids;
		
	/**
	 * @var int
	 */
	private $order;	

	/**
	 * @var int
	 */
	private $is_approved;
	/**
	 * Class consructor
	 *
	 * @param int $profile_id
	 * @param string $entity
	 * @param int $group_id
	 * @param string $title
	 * @param string $description
	 * @param int $create_stamp
	 * @param int $order
	 * @param int $budget_min
	 * @param int $budget_max
	 * @param int $price
	 * @param int $currency	  
	 * @param int $start_stamp
	 * @param int $end_stamp
	 * @param int $allow_bids
	 * @param int $allow_comments
	 * @param int $limited_bids
	 * @param int $edit_stamp
	 * @param int $edited_by_profile_id
	 */
	public function __construct(
									 $profile_id = null, $entity = null, $group_id = null, $title = null, $description = null, 
									 $create_stamp = null, $order = null, $budget_min = null, $budget_max = null, $price = null, 
									 $currency = null, $start_stamp = null, $end_stamp = null, $allow_bids = null, 
									 $allow_comments = null, $limited_bids = null, $edit_stamp = null, $edited_by_profile_id = null,
									 $is_approved = 0, $payment_dtls = null	 
								)
	{
		$this->profile_id = $profile_id;
		$this->entity = $entity;
		$this->group_id = $group_id;
		$this->title = $title;
		$this->description = $description;
		$this->budget_min = $budget_min;
		$this->budget_max = $budget_max;
		$this->price = $price;
		$this->currency = $currency;
		$this->create_stamp = $create_stamp;
		$this->start_stamp = $start_stamp;
		$this->end_stamp = $end_stamp;
		$this->edit_stamp = $edit_stamp;
		$this->edited_by_profile_id = $edited_by_profile_id;
		$this->order = $order;
		$this->allow_bids = $allow_bids;
		$this->allow_comments = $allow_comments;
		$this->limited_bids = $limited_bids;
		$this->is_approved = $is_approved;
		$this->payment_dtls = $payment_dtls;
	}
	
	/**
	 * @return string
	 */
	public function getBudget_max()
	{
		return $this->budget_max;
	}
	
	/**
	 * @return string
	 */
	public function getBudget_min()
	{
		return $this->budget_min;
	}
	
	/**
	 * @return int
	 */
	public function getCreate_stamp()
	{
		return $this->create_stamp;
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @return int
	 */
	public function getEdit_stamp()
	{
		return $this->edit_stamp;
	}
	
	/**
	 * @return int
	 */
	public function getEdited_by_profile_id()
	{
		return $this->edited_by_profile_id;
	}
	
	/**
	 * @return int
	 */
	public function getEnd_stamp()
	{
		return $this->end_stamp;
	}
	
	/**
	 * @return string
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * @return int
	 */
	public function getGroup_id()
	{
		return $this->group_id;
	}
	
	/**
	 * @return int
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return string
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
    /**
     * @return string
     */
    public function getPayment_dtls()
    {
        return $this->payment_dtls;
    }
	
	/**
	 * @return int
	 */
	public function getProfile_id()
	{
		return $this->profile_id;
	}
	
	/**
	 * @return int
	 */
	public function getStart_stamp()
	{
		return $this->start_stamp;
	}
	
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @return int
	 */
	public function getIs_approved()
	{
		return $this->is_approved;
	}	
	
	/**
	 * @param string $budget_max
	 */
	public function setBudget_max($budget_max)
	{
		$this->budget_max = $budget_max;
	}
	
	/**
	 * @param string $budget_min
	 */
	public function setBudget_min($budget_min)
	{
		$this->budget_min = $budget_min;
	}
	
	/**
	 * @param int $create_stamp
	 */
	public function setCreate_stamp($create_stamp)
	{
		$this->create_stamp = $create_stamp;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @param int $edit_stamp
	 */
	public function setEdit_stamp($edit_stamp)
	{
		$this->edit_stamp = $edit_stamp;
	}
	
	/**
	 * @param int $edited_by_profile_id
	 */
	public function setEdited_by_profile_id($edited_by_profile_id)
	{
		$this->edited_by_profile_id = $edited_by_profile_id;
	}
	
	/**
	 * @param int $end_stamp
	 */
	public function setEnd_stamp($end_stamp)
	{
		$this->end_stamp = $end_stamp;
	}
	
	/**
	 * @param string $entity
	 */
	public function setEntity($entity)
	{
		$this->entity = $entity;
	}
	
	/**
	 * @param int $group_id
	 */
	public function setGroup_id($group_id)
	{
		$this->group_id = $group_id;
	}
	
	/**
	 * @param int $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param string $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}
	
    /**
     * @param string $price
     */
    public function setPayment_dtls($payment_dtls)
    {
        $this->payment_dtls = $payment_dtls;
    }
	
	/**
	 * @param int $profile_id
	 */
	public function setProfile_id($profile_id)
	{
		$this->profile_id = $profile_id;
	}
	
	/**
	 * @param int $profile_id
	 */
	public function setIs_approved($is_approved)
	{
		$this->is_approved = $is_approved;
	}	
	
	/**
	 * @param int $start_stamp
	 */
	public function setStart_stamp($start_stamp)
	{
		$this->start_stamp = $start_stamp;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return int
	 */
	public function getAllow_bids()
	{
		return $this->allow_bids;
	}
	
	/**
	 * @return int
	 */
	public function getAllow_comments()
	{
		return $this->allow_comments;
	}
	
	/**
	 * @param int $allow_bids
	 */
	public function setAllow_bids($allow_bids)
	{
		$this->allow_bids = $allow_bids;
	}
	
	/**
	 * @param int $allow_comments
	 */
	public function setAllow_comments($allow_comments)
	{
		$this->allow_comments = $allow_comments;
	}
	
	/**
	 * @return int
	 */
	public function getLimited_bids()
	{
		return $this->limited_bids;
	}
	
	/**
	 * @param int $limited_bids
	 */
	public function setLimited_bids($limited_bids)
	{
		$this->limited_bids = $limited_bids;
	}
	
	/**
	 * @return string
	 */
	public function getCurrency()
	{
		return $this->currency;
	}
	
	/**
	 * @param string $currency
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}


}