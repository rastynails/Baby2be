<?php

class app_ProfilePreferences
{
	private static $preferences = array();
	
	/**
	 * Returns preference typed var.
	 *
	 * @param string $var
	 * @param string $presentation
	 * @return mixed
	 */
	private static function _defineType( $var, $presentation )
	{
		switch ( $presentation )
		{
			case 'checkbox':
				return (bool)(int)$var;
			
			default:
				return $var;
		}
	}

    private static $configs_ref = array();
    private static $configs = array();

    private static function initSections()
    {
        if ( empty( self::$configs_ref ) )
		{
			$result = SK_MySQL::query( 'SELECT * FROM `'.TBL_PROFILE_PREFERENCE.'` WHERE 1' );

			while ( $row = $result->fetch_assoc() )
				self::$configs_ref[$row['section'].'%'.$row['key']] = array (
					'profile_preference_id'	=>	$row['profile_preference_id'],
					'presentation'			=>	$row['presentation'],
					'default_value'			=>	self::_defineType( $row['default_value'], $row['presentation'] )
				);
		}
    }

    /**
	 * Returns typed profile config var.
	 *
	 * @param string $section
	 * @param string $key
	 * @param integer $profile_id if null trying to get online user id.
	 * @return mixed
	 */
	public static function get( $section, $key, $profile_id = null )
	{  
		$profile_id = ( $profile_id = (int)$profile_id ) ? $profile_id : SK_HttpUser::profile_id();
		
		if ( !$profile_id )
			throw new Exception( __CLASS__.'::'.__FUNCTION__."() \r\nError: Undefined profile id" );

        self::initSections();

		if ( !$_preference_id = &self::$configs_ref[$section.'%'.$key]['profile_preference_id'] )
			throw new Exception( __CLASS__.'::'.__FUNCTION__."() \r\nError: Undefined preference `".$section.'%'.$key.'`' );

        if( !array_key_exists($profile_id, self::$configs) )
        {
            $result = SK_MySQL::queryForList(SK_MySQL::placeholder( 'SELECT * FROM `'.TBL_PROFILE_PREFERENCE_DATA.'` WHERE `profile_id`=?', $profile_id));

            self::$configs[$profile_id] = array();

            foreach ( $result as $item )
            {
                self::$configs[$item['profile_id']][$item['profile_preference_id']] = $item['value'];
            }
        }

        if( array_key_exists($_preference_id, self::$configs[$profile_id]) )
        {
            return self::_defineType( self::$configs[$profile_id][$_preference_id], self::$configs_ref[$section.'%'.$key]['presentation'] );
        }

        return self::$configs_ref[$section.'%'.$key]['default_value'];
	}

    public static function initForList( array $profileIdList )
    {
        self::initSections();

        $query = SK_MySQL::placeholder( 'SELECT * FROM `'.TBL_PROFILE_PREFERENCE_DATA.'` WHERE `profile_id` IN (?@)', $profileIdList );

        $result = SK_MySQL::queryForList($query);

        foreach ( $profileIdList as $id )
        {
            self::$configs[$id] = array();
        }

        foreach ( $result as $item )
        {
            self::$configs[$item['profile_id']][$item['profile_preference_id']] = $item['value'];
        }
    }


	/**
	 * Adds/Updates user custom config.
	 * Returns true if value has been changed.
	 * 
	 * @param string $section
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public static function set( $section, $key, $value, $profileId = null )
	{
		static $configs_ref = array();
		static $user_defined = null;
		
		if ( empty( $configs_ref ) )
		{
			$result = SK_MySQL::query( 'SELECT * FROM `'.TBL_PROFILE_PREFERENCE.'` WHERE 1' );
			
			while ( $row = $result->fetch_assoc() )
			{
				$configs_ref[$row['profile_preference_id']] = $row;
				
				$configs_ref[$row['section'].'%'.$row['key']] = &$configs_ref[$row['profile_preference_id']];
			}
		}
		
		if ( !isset( $configs_ref[$section.'%'.$key] ) )
			throw new SK_ProfilePreferencesException( __CLASS__.'::'.__FUNCTION__."() \r\nError: Undefined preference `".$section.'%'.$key.'`' , 2);
		
		
		$user_id = empty($profileId) ? SK_HttpUser::profile_id() : $profileId;
		
		if ( !$user_id )
			throw new SK_ProfilePreferencesException( 'Error: Undefined session user id' , 1);
		
		
		if ( $user_defined === null )
		{
			$result = SK_MySQL::query( SK_MySQL::placeholder( 'SELECT `profile_preference_id`
								FROM `'.TBL_PROFILE_PREFERENCE_DATA.'` WHERE `profile_id`=?', $user_id ) );
			while ( $row = $result->fetch_assoc() )
			{
				$_config_ref = &$configs_ref[$row['profile_preference_id']];
				$user_defined[$_config_ref['section'].'%'.$_config_ref['key']] = true;
			}
		}
		
		
		$prefence_id = &$configs_ref[$section.'%'.$key]['profile_preference_id'];
		
		if ( $user_defined[$section.'%'.$key] )
		{
			$query = SK_MySQL::placeholder('UPDATE `'.TBL_PROFILE_PREFERENCE_DATA.'` SET `value`="?"
					WHERE `profile_id`=? AND `profile_preference_id`=?', strval( $value ), $user_id, $prefence_id );
			SK_MySQL::query($query);
			return (bool)SK_MySQL::affected_rows();
		}
		else 
		{
			$query = SK_MySQL::placeholder( 'INSERT INTO `'.TBL_PROFILE_PREFERENCE_DATA.'`
					SET `profile_id`=?, `profile_preference_id`=?, `value`="?"', $user_id, $prefence_id, strval( $value ) );
			
			SK_MySQL::query( $query );
			
			return true;
		}
	}
	
	/**
	 * Deletes user custom configs.
	 *
	 * @return boolean
	 */
	public static function resetConfigs()
	{
		$user_id = SK_HttpUser::profile_id();
		
		if ( !$user_id )
			throw new SK_ProfilePreferencesException( 'Error: Undefined session user id', 1);
		
		$query = SK_MySQL::placeholder( 'DELETE FROM `' . TBL_PROFILE_PREFERENCE_DATA . '` WHERE `profile_id`=?
		AND `profile_preference_id` NOT IN(SELECT `profile_preference_id` FROM `' . TBL_PROFILE_PREFERENCE . '` WHERE `section` = "hidden")', $user_id );
		SK_MySQL::query($query);
		return (bool)SK_MySQL::affected_rows();
	}
	
	
	/**
	 * Returns all user configs array grouped by sections.
	 * 
	 * @return array
	 */
	public static function getList()
	{
		if (count(self::$preferences)) {
			return self::$preferences;
		}
		
		$user_id = SK_HttpUser::profile_id();
		
		if ( !$user_id )
			throw new SK_ProfilePreferencesException('Error: Undefined session user id' , 1);
		
		
		$query = SK_MySQL::placeholder( 'SELECT `profile_preference_id` AS `id`,`value`
							FROM `'.TBL_PROFILE_PREFERENCE_DATA.'` WHERE `profile_id`=?', $user_id );
		
		$result = SK_MySQL::query($query);
		
		while ($preference = $result->fetch_object())
			$user_defined[$preference->id] = $preference->value;
			
		$preferences = array();
		
		$result = SK_MySQL::query( 'SELECT * FROM `'.TBL_PROFILE_PREFERENCE.'` WHERE `section`!="hidden"' );
		while ( $row = $result->fetch_assoc() )
		{
			if ( !isset( $preferences[$row['section']] ) )
				$preferences[$row['section']] = array();
			
			$_pref_id = &$row['profile_preference_id'];
			
			$preferences[$row['section']][$row['key']] = array (
				'presentation'	=>	$row['presentation'],
				'value'		=>	isset( $user_defined[$_pref_id] ) ? $user_defined[$_pref_id] : $row['default_value']
			);
		}
		
		self::$preferences = $preferences;
		return $preferences;
	}
	
	
	public static function hasCustoms()
	{
		$user_id = SK_HttpUser::profile_id();
		
		if ( !$user_id )
			throw new SK_ProfilePreferencesException( 'Error: Undefined session user id' ,1);
		
		
		$query = SK_MySQL::placeholder( 'SELECT `d`.`profile_preference_id`
					FROM `'.TBL_PROFILE_PREFERENCE_DATA.'` AS `d`
					LEFT JOIN `'.TBL_PROFILE_PREFERENCE.'` AS `p` ON `p`.`profile_preference_id` = `d`.`profile_preference_id` 
					WHERE `profile_id`=? AND `section` !="hidden" LIMIT 1', $user_id );
		
		return (bool) SK_MySQL::query($query)->fetch_cell();
	}
	
}

class SK_ProfilePreferencesException extends Exception {}

?>