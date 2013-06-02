<?php

class SK_ServiceUse extends SK_Service
{
	/**
	 * Keeps a result of $this->checkPerissions() for saving resources;
	 *
	 * @var integer
	 */
	private $check_result;
	
	/**
	 * Constructor.
	 *
	 * @param string $service_key
	 * @param integer $profile_id
	 */
	public function __construct( $service_key, $profile_id = null )
	{
		parent::__construct($service_key, $profile_id);
		
		$this->check_result = $this->checkPermissions();
		
		if ( in_array($this->check_result, array(
			self::SERVICE_NO, self::SERVICE_GUEST, self::SERVICE_DENIED
		)) ) {
			throw new SK_ServiceUseException($this);
		}
	}
	
	/**
	 * The getter for $this->check_result;
	 */
	public function check_result() {
		return $this->check_result;
	}
	
	/**
	 * Track a profile service use.
	 */
	public function trackServiceUse()
	{
		if ( in_array($this->check_result, array(
			self::SERVICE_NO_CREDITS, self::SERVICE_LIMITED
		)) ) {
			throw new SK_ServiceUseException($this);
		}
		
		parent::trackServiceUse();
	}
	
	/**
	 * Track a profile service use.
	 */
	public function track() {
		$this->trackServiceUse();
	}
}


class SK_ServiceUseException extends Exception
{
	/**
	 * Service is completely disabled.
	 */
	const SERVICE_DISABLED = 1;
	
	/**
	 * Service requires authentication.
	 */
	const AUTH_REQUIRED = 2;
	
	/**
	 * Profile hasn't enought permissions.
	 */
	const SERVICE_DENIED = 3;
	
	/**
	 * Profile hasn't enought credits.
	 */
	const NO_CREDITS = 4;
	
	/**
	 * Profile has exhausted a use limit.
	 */
	const LIMIT_EXCEEDED = 5;
	
	/**
	 * Service dey message.
	 *
	 * @var array
	 */
	private $service_deny_message;
	
	/**
	 * Constructor.
	 *
	 * @param SK_ServiceUse $service
	 * @param integer $service_check_result
	 */
	public function __construct( SK_ServiceUse $service )
	{
		$service_check_result = $service->check_result();
		
		$this->service_deny_message = $service->permission_message;
		
		$code_index = array(
			SK_Service::SERVICE_NO		=> self::SERVICE_DISABLED,
			SK_Service::SERVICE_GUEST 	=> self::AUTH_REQUIRED,
			SK_Service::SERVICE_DENIED 	=> self::SERVICE_DENIED,
			SK_Service::SERVICE_NO_CREDITS	=> self::NO_CREDITS,
			SK_Service::SERVICE_LIMITED	=> self::LIMIT_EXCEEDED
		);
		
		parent::__construct(
			$this->service_deny_message['alert'],
			$code_index[$service_check_result]
		);
	}
	
	/**
	 * Get html format message.
	 *
	 * @return string
	 */
	public function getHtmlMessage() {
		return $this->service_deny_message['message'];
	}
}
