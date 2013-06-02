<?php

/**
 * Index page template builder
 *
 */
class PageBuilder
{
	/**
	 * Returns URL to theme preview image 
	 *
	 * @param string $theme
	 */
	private static function getThemeThumbUrl( $theme )
	{
		return URL_LAYOUT.'themes/'.$theme.'/theme.jpg';
	}
	
	/**
	 * Returns info about activated theme
	 *
	 */
	public static function getActiveTheme()
	{
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_THEME."` WHERE `is_active`='yes' LIMIT 1");
		
		$theme_info = SK_MySQL::query($query)->fetch_assoc();
		if ($theme_info)
			$theme_info['thumb'] = self::getThemeThumbUrl($theme_info['theme_name']);
		
		return $theme_info;
	}
	
	/**
	 * Updates theme info and theme's page code 
	 *
	 * @param string $name
	 * @param string $tpl_field
	 * @param string $code
	 * @param string $title
	 * @param string $author
	 */
	public static function updateCode( $name, $tpl_field, $code, $title = '', $author = '', $direction = 'ltr' )
	{
		$code = trim($code);
		
		if ( strlen($title) && strlen($author) )
		{
			$query = SK_MySQL::placeholder("UPDATE `".TBL_THEME."` 
                SET `title`='?', `author`='?', `".$tpl_field."`='?', `tpl_timestamp`=?, `direction`='?'
                WHERE `theme_name`='?'", $title, $author, $code, time(), $direction, $name);
		}
		else
		{
			$query = SK_MySQL::placeholder("UPDATE `".TBL_THEME."` 
                SET `".$tpl_field."`='?', `tpl_timestamp`=?
				  WHERE `theme_name`='?'", $code, time(), $name);
		}
		SK_MySQL::query($query);
		
		switch ( $tpl_field )
		{
		    case 'index_page_code':
		        httpdoc_Index::clearCompile();
		        break;
		        
		    case 'home_page_code':
		        component_MemberHome::clearCompile();
		        break;
		}
		
		return SK_MySQL::affected_rows();
	}
	
	/**
	 * Returns path to theme directory 
	 *
	 * @param string $theme
	 * @return string
	 */
	private static function getThemeDir( $theme = '' )
	{
		if (strlen($theme))
			return DIR_LAYOUT.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR;
		else	
			return DIR_LAYOUT.'themes'.DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Resets theme's page code to default value.
	 *
	 * @param string $theme
	 * @param string $tpl_field
	 * @param string $update_info
	 */
	public static function resetCode( $theme, $tpl_field, $update_info = true )
	{
		$theme_dir = self::getThemeDir($theme); 
		
	    switch ( $tpl_field )
        {
            case 'index_page_code':
                $file_name = $theme_dir . 'index_page.html';
                break;
                
            case 'home_page_code':
                $file_name = $theme_dir . 'home_page.html';
                break;
        }
		
		$r_handle = fopen($file_name, 'r');
		$tpl = fread($r_handle, filesize($file_name));
		
		if ( $update_info )
		{
    		preg_match("~\{\*\s*Theme\s*\:\s*(.+\S+)\s*\*\}~i", $tpl, $match_t);
    		$title = $match_t[1];
    		preg_match("~\{\*\s*Author\s*\:\s*(.+\S+)\s*\*\}~i", $tpl, $match_a);
    		$author = $match_a[1];
            preg_match("~\{\*\s*Direction\s*\:\s*(.+\S+)\s*\*\}~i", $tpl, $match_d);
            $direction = !empty($match_d[1]) && in_array($match_d[1], array('ltr', 'rtl')) ? $match_d[1] : 'ltr';
    				
    		self::updateCode($theme, $tpl_field, $tpl, $title, $author, $direction);
		}
		else 
		{
            self::updateCode($theme, $tpl_field, $tpl);
		}
		
		fclose($r_handle);
	}
	
	/**
	 * Activates specified theme
	 *
	 * @param string $name
	 */
	public static function setActiveTheme( $name )
	{
		$query = SK_MySQL::placeholder("UPDATE `".TBL_THEME."` SET `is_active`='yes', `tpl_timestamp`=? 
			WHERE `theme_name`='?'", time(), $name);
		
		SK_MySQL::query($query);
		
		$is_set = SK_MySQL::affected_rows(); 
		if ($is_set)
		{
			$query = SK_MySQL::placeholder("UPDATE `".TBL_THEME."` SET `is_active`='no' WHERE `theme_name`!='?'", $name);
			SK_MySQL::query($query);
		}
		SK_Config::section('layout')->set('theme', $name);
		
		httpdoc_Index::clearCompile();

		return $is_set;
	}
	
	/**
	 * Returns theme info
	 *
	 * @param string $name
	 */
	public static function getThemeInfo( $name )
	{
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_THEME."` WHERE `theme_name` ='?'", $name);
		
		$theme_info = SK_MySQL::query($query)->fetch_assoc();
		if ($theme_info)
			$theme_info['thumb'] = self::getThemeThumbUrl($name);
		
		return $theme_info;
	}
	
	/**
	 * Scans themes directory and adds new found themes
	 *
	 */
	public static function readThemes()
	{
		$themes_arr = array();
		$themes_dir = self::getThemeDir();
		
		// get names of all themes  
		if ( $handle = opendir($themes_dir) )
		    while (false !== ($filename = readdir($handle)))
        		if ( $filename != '.' && $filename != '..' && preg_match('/^\w+$/i', $filename))
        			$themes_arr[] = $filename;
		    
		closedir($handle);

		$themes = array();
		$pattern = 'index_page.html';
		
		// get themes containing file 'index_page.html'
		foreach ($themes_arr as $theme) 
		{
			if ( $handle = opendir($themes_dir.$theme) )
			    while (false !== ($theme_file = readdir($handle)))
	        		if ( $theme_file != '.' && $theme_file != '..' && preg_match("/$pattern/i", $theme_file))
	        			$themes[] = $theme;
	        			
			closedir($handle);
		}
		
		$available_themes = array();
		
		foreach ($themes as $t)
		{
			$theme_info = self::getThemeInfo($t);
			if ($theme_info)
				$available_themes[] = $theme_info;
			else
			{
				$new_theme = self::addTheme($t);
				if ($new_theme)
					$available_themes[] = $new_theme; 
			} 
		}
		
        //usort($available_themes, 'self::sortThemes');
    	return $available_themes;
	}
    
    public static function sortThemes($theme1, $theme2)
    {
        if ($theme1['title'] > $theme2['title'])
            return 1;
    }
	
	/**
	 * Adds new theme
	 *
	 * @param string $name
	 * @return unknown
	 */
	private static function addTheme( $name )
	{
		$theme_dir = self::getThemeDir($name); 
		$index_page = $theme_dir . 'index_page.html';

		if ( !file_exists($index_page) )
		{
            return false;
		}
		
		$r_handle = fopen($index_page, 'r');
	    $index_tpl = fread($r_handle, filesize($index_page));
		fclose($r_handle);
	
		preg_match("~\{\*\s*Theme\s*\:\s*(.+\S+)\s*\*\}~i", $index_tpl, $match_t);
		$title = $match_t[1];
		preg_match("~\{\*\s*Author\s*\:\s*(.+\S+)\s*\*\}~i", $index_tpl, $match_a);
		$author = $match_a[1];

        // direction
        preg_match("~\{\*\s*Direction\s*\:\s*(.+\S+)\s*\*\}~i", $index_tpl, $match_d);
        $direction = !empty($match_d[1]) && in_array($match_d[1], array('ltr', 'rtl')) ? $match_d[1] : 'ltr';

		$home_page = $theme_dir . 'home_page.html';
		if ( file_exists($home_page) )
		{
            $r_handle = fopen($home_page, 'r');
            $home_tpl = fread($r_handle, filesize($home_page));
            fclose($r_handle);
		}
		
		$return_arr = array (
			'theme_name' => $name,
			'title' => $title,
			'author' => $author,
            'direction' => $direction,
			'index_page_code' => $index_tpl,
            'home_page_code' => $home_tpl,		
			'is_active' => 'no',
			'thumb' => self::getThemeThumbUrl($name)
		);
		
		$query = SK_MySQL::placeholder("INSERT INTO `".TBL_THEME."` 
			(`theme_name`, `title`, `author`, `index_page_code`, `home_page_code`, `is_active`, `tpl_timestamp`, `direction`)
			VALUES ('?', '?', '?', '?', '?', 'no', ?, '?')", $name, $title, $author, $index_tpl, $home_tpl, time(), $direction);
		
		SK_MySQL::query($query);
		
		return $return_arr;
	}
	
	private function updateCodeTimeStamp($theme)
	{
		if (strlen($theme))
		{
			$query = SK_MySQL::placeholder("UPDATE `".TBL_THEME."` SET `tpl_timestamp`=? 
				WHERE `theme_name`='?'", time(), $theme);
			SK_MySQL::query($query);
			
			if (SK_MySQL::affected_rows())
				return true;
		}
		return false;
	}
}
