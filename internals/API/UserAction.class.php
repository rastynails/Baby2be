<?php

class SK_UserAction
{
	/**
	 * Actor `profile_id`.
	 *
	 * @var integer
	 */
	private $actor_id;
	
	/**
	 * Type of an action.
	 *
	 * @var string
	 */
	private $type;
	
	/**
	 * Action timestamp.
	 *
	 * @var integer
	 */
	private $timestamp;
	
	/**
	 * Additional properties list.
	 *
	 * @var array
	 */
	private $properties = array();
	
	/**
	 * Action item to group similar ations.
	 *
	 * @var mixed
	 */
	public $item;
	
	public $status = 'approval';
	
	/**
	 * Constructor.
	 *
	 * @param string $type action type
	 * @param integer $actor_id actor profile_id
	 */
	public function __construct( $type, $actor_id = null )
	{
		$this->type = $type;
		
		if ( !isset($actor_id) ) {
			if ( !SK_HttpUser::is_authenticated() ) {
				throw new Exception('failed to get actor profile_id, user not authenticated', 0);
			}
			
			$this->actor_id = SK_HttpUser::profile_id();
		}
		else {
			$this->actor_id = $actor_id;
		}
		
		$this->timestamp = time();
	}
	
	/**
	 * Additional properties setter.
	 */
	public function __set( $name, $value ) {
		$this->properties[$name] = $value;
	}
	
	/**
	 * Get SQL presenation of an object.
	 *
	 * @return string
	 */
	public function toSQL()
	{
		if ( count($this->properties) ) {
			ksort($this->properties, SORT_STRING);
			$json_properties = json_encode($this->properties);
		}
		else {
			$json_properties = '{}';
		}

		return SK_MySQL::placeholder(
			"`actor_id`=?, `type`='?', `timestamp`=?,
				`item`='?', `properties`='?', `status`='?'",
			$this->actor_id, $this->type, $this->timestamp,
				json_encode($this->item), $json_properties, $this->status
		);
	}
	
}
