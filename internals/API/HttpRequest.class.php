<?php

class SK_HttpRequest
{

	public static $query_string;


	/**
	 * HTTP request headers.
	 *
	 * @var array
	 */
	private static $request_headers = array();

	/**
	 * $_GET params.
	 *
	 * @var array
	 */
	public static $GET;

	/**
	 * Current SK_NavigationDocument Object
	 *
	 * @var SK_NavigationDocument
	 */
	private static $document;


	private static $requare_document_key;


	private static $modules = array();

	private static $checked_url;

	/**
	 * Prepares HttpRequest class.
	 *
	 * @param string $url
	 */
	public static function prepare( $url )
	{
		if ( !$url || self::isXMLHttpRequest() ) {
			return;
		}

		$url_info = @parse_url(sk_request_uri($url));

		if ( !$url_info )
		{
            return null;
		}

		$path = substr($url_info['path'],1);

		$path .= (substr($path, -1,1) == '/') || !strlen($path) ? 'index.php' : '';

		if(isset($url_info['query'])){

			$query = SK_MySQL::placeholder(
						"SELECT `document_key` AS `key`, `url` FROM `".TBL_DOCUMENT."` WHERE
						SUBSTRING(`url`,1,?)='?'"
						, strlen($path), $path );
		}
		else
		{
			$query = SK_MySQL::placeholder(
						"SELECT `document_key` AS `key`, `url` FROM `".TBL_DOCUMENT."` WHERE `url`='?'"
						, $path );
		}

		$result = SK_MySQL::query($query);

		if ( !((bool)$result->num_rows()) ){
			return ;
		}

		$document_key = null;

		if( $result->num_rows() <=1 ){
			$document_key = $result->fetch_object()->key;
		}
		else
		{

			while ($item = $result->fetch_object()){

				$_url_info = parse_url($item->url);

				if ( !isset($document_key) && !isset($_url_info['query'])){
					$document_key = $item->key;
					continue;
				}

				parse_str($_url_info['query'], $query_arr1);
				parse_str($url_info['query'], $query_arr2);

				if ( !(bool)array_diff_assoc( $query_arr1, $query_arr2 ) ){
					$document_key = $item->key;
				}
			}
		}

		if (isset($document_key)){
			self::$document = new SK_NavigationDocument($document_key);
		}

		if (isset($url_info['query'])){
			parse_str($url_info['query'], self::$GET);
			self::$query_string = $url_info['query'];
		}

		self::request_navigate();

		return true;
	}

	public static function setDocument($doc_key) {
		self::$document = new SK_NavigationDocument($doc_key);
	}


	/**
	 * Returns an object of SK_NavigationDocument.
	 *
	 * @return SK_NavigationDocument
	 */
	public static function getDocument()
	{
		return self::$document;
	}

	/**
	 * returns path to Document which mast requare instead curent document
	 *
	 * @return string|null
	 */
	public static function getRequarePath()
	{
		if(isset(self::$requare_document_key)){
			self::setDocument(self::$requare_document_key);
			return SK_Navigation::getDocument(self::$requare_document_key)->path;
		}
		return null;
	}

	private static function request_navigate()
	{
                if (!isset(self::$document)) {
			return ;
		}

		if ( !self::$document->status ) {
			header("Status: 404 Not Found",null,404);

			exit();
		}

		if ( !app_Site::active() ) {
			if (self::$document->document_key != "site_suspended") {
				self::redirect(SK_Navigation::href('site_suspended'));
			}
		} else {
			if ( self::$document->document_key == "site_suspended" ) {
				self::redirect(SK_Navigation::href('index'));
			}
		}

                if ( isset($_GET['request_ids']) )
                {
                    self::redirect(sk_make_url(SITE_URL . 'facebook/index.php', array(
                        'redirect' => 1,
                        'request_ids' => $_GET['request_ids']
                    )));
                }

		if (self::$document->document_key == "profile") {
			if (!isset(self::$GET["profile_id"]) || self::$GET["profile_id"] == SK_httpUser::profile_id()) {
				self::setDocument('profile_view');
			}
		}

		// control registration profile form
		if ( self::$document->document_key == 'join_profile' ) {
			app_Invitation::controlRegistrationAccess();
		}

		if(SK_HttpUser::is_authenticated())
		{

			//profile not reviewed check
			$avalible_documents = array('sign_out');

			if (!in_array(self::$document->document_key, $avalible_documents))
			{
                            if (self::$document->document_key != 'email_verify' && !app_Profile::email_verified() ) {
                                self::$requare_document_key = 'email_verify';
                            }
			}

			//profile not reviewed check
			$avalible_documents = array('sign_out', "email_verify", "profile_suspended");

			if (!in_array(self::$document->document_key, $avalible_documents))
			{
				if (self::$document->document_key != 'not_reviewed') {
					if (!app_Profile::reviewed() && !$config = SK_Config::section('site')->Section('additional')->Section('profile')->not_reviewed_profile_access) {
						self::$requare_document_key = 'not_reviewed';
					}
				} else {
					if (app_Profile::reviewed()) {
						self::redirect(SK_Navigation::href('home'));
					}
				}
			}

			//profile suspended check
			$avalible_documents = array('profile_edit', 'profile_unregister', 'sign_out', 'email_verify', 'contact');

			if (!in_array(self::$document->document_key, $avalible_documents))
			{
				if (self::$document->document_key != 'profile_suspended') {
					if (app_Profile::suspended()) {
						self::$requare_document_key = 'profile_suspended';
					}
				} else {
					if (!app_Profile::suspended()) {
						self::redirect(SK_Navigation::href('home'));
					}
				}
			}

		    //profile suspended check
            $avalible_documents = array('profile_edit', 'profile_unregister', 'sign_out', 'contact', 'email_verify');

            if (!in_array(self::$document->document_key, $avalible_documents))
            {
               if ( SK_Config::section('facebook_connect')->fields_required && app_Profile::isFacebookUser() && !app_Profile::isProfileFieldsCompleted() )
               {
                   self::redirect(SK_Navigation::href('profile_edit', array(
                       'complete' => 1
                   )));
               }
            }

			if ( !self::$document->access_member ){
				self::redirect(SK_Navigation::href('home'));
			}
		}
		else
		{
			if ( !self::$document->access_guest ) {
				self::$requare_document_key = 'sign_in';
			}
		}
	}


	/**
	 * Redirect an http request.
	 * If pass FALSE as the second argument script will be continue running just sending a header.
	 *
	 * @param string $url
	 * @param bool $die
	 */
	public static function redirect( $url, $die = true )
	{

		header('Location: ' . $url);

		if ($die)
			exit();
	}


	public static function collect_request_headers()
	{
		if ( function_exists('apache_request_headers') ) {
			// this works only when PHP compiled as an apache module...
			$t = apache_request_headers();
			foreach( $t as $k => $v)
			{
				self::$request_headers[$k] = $v;
			}
		}
		else {
			// ...and this once when PHP is CGI extension
			foreach ( array_keys($_SERVER) as $skey ) {
				if ( substr($skey, 0, 5) == 'HTTP_' ) {
					$headername = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($skey, 5)))));
					self::$request_headers[$headername] = $_SERVER[$skey];
				}
			}
		}

		if ( isset(self::$request_headers['x-requested-with']) ) { // IE case
			self::$request_headers['X-Requested-With'] = self::$request_headers['x-requested-with'];
			unset(self::$request_headers['x-requested-with']);
		}
	}

	/**
	 * Get an http request header value.
	 *
	 * @param string $name header name
	 * @return string header value or NULL if not exists
	 */
	public static function getRequestHeader( $name )
	{
		if ( !isset(self::$request_headers[$name]) ) {
			return null;
		}

		return self::$request_headers[$name];
	}

	/**
	 * Returns TRUE if current script was requested by XMLHttpRequest (Ajax).
	 *
	 * @return boolean
	 */
	public static function isXMLHttpRequest()
	{
		return (isset(self::$request_headers['X-Requested-With'])
			&& self::$request_headers['X-Requested-With'] == 'XMLHttpRequest');
	}

	public static function isIpBlocked( $ip = null )
	{
		$ip = 	( $ip ) ? $ip : self::getRequestIP();

		if ( !$ip )
			return false;
                
                if ( !SK_Config::section( 'security' )->enable_block_ip )
                {
                    return false;
                }

		$query = SK_MySQL::placeholder("SELECT `block_ip` FROM `".TBL_BLOCK_IP."`
			WHERE `block_ip`=INET_ATON('?')", $ip );

		return ( SK_MySQL::query( $query )->fetch_cell() ) ? true : false;
	}

	public static function showFalsePage()
	{
		self::redirect(SK_Navigation::href('false_page'), true);
	}

	public static function getRequestIP()
	{
		return isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'];
	}
}


SK_HttpRequest::collect_request_headers();
