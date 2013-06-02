<?php

class SK_UserAgent 
{
	/**
	 * This array contains four arrays of user agent data. 
	 * It is used to identify browser, platform, robot, and mobile devices. 
	 * The array keys are used to identify the device
	 * and the array values are used to set the actual name of the item.
	 * 
	 */
	public static $config = array();

	/**
	 * The current user agent
	 * 
	 */
	public static $user_agent;
	
	/**
	 * Set user agent state
	 *
	 */
	public static function setup()
	{
		/**
		 * Platforms
		 */
		self::$config['platform'] = array
		(
			'windows nt 6.0' => 'Windows Vista',
			'windows nt 5.2' => 'Windows 2003',
			'windows nt 5.0' => 'Windows 2000',
		    'windows phone'  => 'Windows Phone',
			'windows nt 5.1' => 'Windows XP',
			'windows nt 4.0' => 'Windows NT',
			'winnt4.0'       => 'Windows NT',
			'winnt 4.0'      => 'Windows NT',
			'winnt'          => 'Windows NT',
			'windows 98'     => 'Windows 98',
			'win98'          => 'Windows 98',
			'windows 95'     => 'Windows 95',
			'win95'          => 'Windows 95',
            'windows ce'     => 'Windows CE',
            'wm5'            => 'Windows Mobile v5',
			'windows'        => 'Unknown Windows OS',
			'os x'           => 'Mac OS X',
			'intel mac'      => 'Intel Mac',
			'ppc mac'        => 'PowerPC Mac',
			'powerpc'        => 'PowerPC',
			'ppc'            => 'PowerPC',
			'cygwin'         => 'Cygwin',
			'linux'          => 'Linux',
			'debian'         => 'Debian',
			'openvms'        => 'OpenVMS',
			'sunos'          => 'Sun Solaris',
			'amiga'          => 'Amiga',
			'beos'           => 'BeOS',
			'apachebench'    => 'ApacheBench',
			'freebsd'        => 'FreeBSD',
			'netbsd'         => 'NetBSD',
			'bsdi'           => 'BSDi',
			'openbsd'        => 'OpenBSD',
			'os/2'           => 'OS/2',
			'warp'           => 'OS/2',
			'aix'            => 'AIX',
			'irix'           => 'Irix',
			'osf'            => 'DEC OSF',
			'hp-ux'          => 'HP-UX',
			'hurd'           => 'GNU/Hurd',
			'unix'           => 'Unknown Unix OS',
		);
		
		
		/**
		 * Browser
		 */
		self::$config['browser'] = array
		(
			'Opera'             => 'Opera',
			'MSIE'              => 'Internet Explorer',
			'Internet Explorer' => 'Internet Explorer',
			'Shiira'            => 'Shiira',
			'Firefox'           => 'Firefox',
			'Chimera'           => 'Chimera',
			'Phoenix'           => 'Phoenix',
			'Firebird'          => 'Firebird',
			'Camino'            => 'Camino',
			'Netscape'          => 'Netscape',
			'OmniWeb'           => 'OmniWeb',
			'Chrome'            => 'Chrome',
            'Mobile Safari'     => 'Mobile Safari',
            'Safari'            => 'Safari',
			'Konqueror'         => 'Konqueror',
			'Epiphany'          => 'Epiphany',
			'Galeon'            => 'Galeon',
			'Mozilla'           => 'Mozilla',
			'icab'              => 'iCab',
			'lynx'              => 'Lynx',
			'links'             => 'Links',
			'hotjava'           => 'HotJava',
			'amaya'             => 'Amaya',
			'IBrowse'           => 'IBrowse',
		);
		
		/**
		 * Mobile browsers
		 */
		self::$config['mobile'] = array
		(
			'mobileexplorer' => 'Mobile Explorer',
			'openwave'       => 'Open Wave',
			'opera mini'     => 'Opera Mini',
			'operamini'      => 'Opera Mini',
            'opera mobi'     => 'Opera Mobi',
            'maemo browser'  => 'Maemo Browser',
			'elaine'         => 'Palm',
			'palmsource'     => 'Palm',
			'digital paths'  => 'Palm',
			'avantgo'        => 'Avantgo',
			'xiino'          => 'Xiino',
			'palmscape'      => 'Palmscape',
			'nokia'          => 'Nokia',
			'ericsson'       => 'Ericsson',
            'samsung'        => 'Samsung',
            'sonyericsson'   => 'SonyEricsson',
			'blackBerry'     => 'BlackBerry',
			'motorola'       => 'Motorola',
            'mot'            => 'Motorola',
			'iphone'         => 'iPhone',
            'ipod'           => 'iPod',
		    'nexus one'      => 'Nexus One',
            'htc_hd2'        => 'HTC HD2',
		    'htc desire'     => 'HTC Desire',
		    'htc_touch_diamond' => 'HTC Touch Diamond',
            'htc_dream'      => 'HTC Dream',
            'htc6875'        => 'HTC Touch PRO 2',
            'XV6850'         => 'HTC Touch PRO',
            'htc'            => 'HTC unknown',
            'hp ipaq'        => 'HP iPAQ',
			'android'        => 'Android',
            'sprint'         => 'Sprint',
            'docomo'         => 'DoCoMo',
            'vodafone'       => 'Vodafone',
            'minimo'         => 'Minimo',
            'plink'          => 'PocketLink',
            'netfront'       => 'NetFront',
            'pie'            => 'Pocket Internet Explorer',
            'lge'            => 'LGE',
            'nintendo wii'   => 'Nintendo Wii',
            'playstation'    => 'Sony PlayStation'
		);
		
		self::$config['tablet'] = array(
            'ipad'           => 'iPad',
            'tablet browser' => 'Tablet browser'
		);
		
		/**
		 * The most common bots
		 */
		self::$config['robot'] = array
		(
			'googlebot'   => 'Googlebot',
			'msnbot'      => 'MSNBot',
			'slurp'       => 'Inktomi Slurp',
			'yahoo'       => 'Yahoo',
			'askjeeves'   => 'AskJeeves',
			'fastcrawler' => 'FastCrawler',
			'infoseek'    => 'InfoSeek Robot 1.0',
			'lycos'       => 'Lycos',
		);
		
		// Set the user agent
		self::$user_agent = trim($_SERVER['HTTP_USER_AGENT']);
	}
	
	/**
	 * Retrieves current user agent information:
	 * keys:  browser, version, platform, mobile, robot, referrer, languages, charsets
	 * tests: is_browser, is_mobile, is_robot
	 *
	 * @param string $key
	 * @return boolean | string
	 */
	public static function get($key = 'agent')
	{
		static $info;

		// Return the raw string
		if ($key === 'agent')
			return self::$user_agent;
	
		$info = array();
		
		// Parse the user agent and extract information
		foreach ( self::$config as $type => $data)
		{
			foreach ( $data as $agent => $name )
			{
				if ( stripos(self::$user_agent, $agent ) !== FALSE )
				{
					if ( $type === 'browser' AND preg_match('|'.preg_quote($agent).'[^0-9.]*+([0-9.][0-9.a-z]*)|i', self::$user_agent, $match) )
					{
						// Set the browser version
						$info['version'] = $match[1];
					}

					// Set the agent name
					$info[$type] = $name;
					break;
				}
			}
		}

		if ( empty($info[$key]) )
		{
			switch ( $key )
			{
				case 'is_robot':
				case 'is_browser':
				case 'is_mobile':
					$return = !empty($info[substr($key, 3)]);
				break;
				
				case 'languages':
					$return = array();
					if ( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
					{
						if ( preg_match_all('/[-a-z]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])), $matches) )
						{
							$return = $matches[0];
						}
					}
					break;
				
				case 'charsets':
					$return = array();
					if ( !empty($_SERVER['HTTP_ACCEPT_CHARSET']) )
					{
						if ( preg_match_all('/[-a-z0-9]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET'])), $matches) )
						{
							$return = $matches[0];
						}
					}
					break;
				
				case 'referrer':
					if ( !empty($_SERVER['HTTP_REFERER']) )
					{
						$return = trim($_SERVER['HTTP_REFERER']);
					}
					break;
			}

			isset($return) and $info[$key] = $return;
		}
        
		// Return the key, if set
		return isset($info[$key]) ? $info[$key] : NULL;
	}
}