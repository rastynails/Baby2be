<?php
require_once DIR_CONTACT_GRABBER.'openinviter.php';

class app_ContactGrabber
{
	private static $instance;

	/**
	 * Returns Instance of OpenInviter class
	 *
	 * @return OpenInviter
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new OpenInviter();
		}
		return self::$instance;
	}

	public static function emailProviders()// $separate = false )
	{
		$plagins = @self::getInstance()->getPlugins();
		return $plagins['email'];
		//return $separate ? $plagins : array_merge($plagins['email'], $plagins['social']);
	}

	public static function contacts($user, $password, $provider)
	{
		$providers = self::emailProviders();

		self::getInstance()->startPlugin($provider);
		$error = self::getInstance()->getInternalError();

		if ($error) {
			throw new Exception($error,1);
		}
		$result = self::getInstance()->login($user, $password);
		if (!$result) {
			throw new Exception('login_faild',2);
		}

		$result = self::getInstance()->getMyContacts();

		if (!isset($result) || $result===false) {
			throw new Exception('get_contacts_faild',3);
		}
		self::getInstance()->logout();
		return $result;
	}

}

