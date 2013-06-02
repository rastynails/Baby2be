<?php

class app_ProfileSearch
{
	/**
	 * Returns array with profiles ID, that are found
	 * by search. Function saves fields values in search preference, delete old ( if exists )
	 * and create new temporary profile list.
	 *
	 * @param string $search_type
	 * @param array $values_arr
	 *
	 * @return boolean
	 */
	public static function generateResultList( $search_type, $values_arr, $only_count = false )
	{
		if ( !strlen( trim( $search_type ) ) )
			return false;

		$_fields_arr = self::SearchTypeFields( $search_type );

		if ( !is_array( $_fields_arr ) )
			return false;

		self::clearSearchPreference();

         $_query_join = '';
         
                $config = SK_Config::section('site.additional.profile_list');

		if (!empty($values_arr['match_sex']) && $config->intellectual_search) {
			$query_ins = array();
		} else {
			$query_ins = '';
		}

		foreach ( $_fields_arr as $_field_name )
		{

			$_field_value = @$values_arr[$_field_name];

			$pr_field = SK_ProfileFields::get($_field_name);

			if (is_array($query_ins)) {
				if ($_field_name == 'match_sex') {
					$_query_ins = & $query_ins['sex'];
				} else {
					$_query_ins = & $query_ins[$pr_field->getPermission()];
				}
			} else {
				$_query_ins = &$query_ins;
			}

			$_query_ins = isset($_query_ins) ? $_query_ins : '';

			switch ( $pr_field->presentation )
			{
				case 'location':

					$location_fields = array('country_id','state_id','city_id','zip_third','radius_first', 'radius_third', 'custom_location','mcheck_country');
					foreach ($location_fields as $field){

						// Convert city name to city code
						if ( ($field == 'city_id') && isset($_field_value[$field]) && strlen( $_field_value[$field] ) ){
							$value = app_Location::CityCodeByName( @$_field_value['state_id'], @$_field_value['city_id'] );
						}
						else {
							$value = isset($_field_value[$field]) ? $_field_value[$field] : null ;
						}

                        $result = self::getLocationSearchString( $field, $value, $_field_value );
                        $_query_ins .= $result['where'];
                        $_query_join .= $result['join'];
					}
					break;

				case 'date':
					if ($_field_value && @$_field_value['day'] && @$_field_value['month'] && @$_field_value['year'])
					{
						$value = "{$_field_value['day']}/{$_field_value['month']}/{$_field_value['year']}";

						$_query_ins .= ( SK_ProfileFields::get($_field_name)->base_field )
									? SK_MySQL::placeholder("`main`.`$_field_name` = '?' AND ", $_field_value )
									: SK_MySQL::placeholder( "`extend`.`$_field_name` = '?' AND ", $_field_value );

						self::savePreference( $_field_name, $_field_value );
					}

					break;

				case 'text':
				case 'textarea':
				case 'callto':

					$_field_value = ( $_field_name == 'username' ) ? trim($_field_value) : $_field_value;

					if ( trim( $_field_value ) )
					{
						$_query_ins .= ( SK_ProfileFields::get($_field_name)->base_field )
									? sql_placeholder( "`main`.`$_field_name` LIKE ? AND ", "$_field_value%" )
									: sql_placeholder( "`extend`.`$_field_name` LIKE ? AND ", "$_field_value%" );

						self::savePreference( $_field_name, $_field_value );
					}


					break;

				case 'url':
					// Full hardcode
					if(!trim($_field_value))
						break;
					$_query_ins .= ( SK_ProfileFields::get($_field_name)->base_field )
								? sql_placeholder( "`main`.`$_field_name` LIKE ? AND ", "%$_field_value%" )
								: sql_placeholder( "`extend`.`$_field_name` LIKE ? AND ", "%$_field_value%" );

					self::savePreference( $_field_name, $_field_value );
					break;
				case 'fselect':
				case 'fradio':

					if ( !is_array( $_field_value ) )
						break;

					$_cookie_value = ( $_field_value  ) ? implode( ':', $_field_value ) : '';

					self::savePreference( $_field_name, $_cookie_value );

					if ( $_field_value )
						$_query_ins .= ( SK_ProfileFields::get($_field_name)->base_field )
								? sql_placeholder( "`main`.`$_field_name` IN ( '".implode( "','", $_field_value )."' ) AND ")
								: sql_placeholder( "`extend`.`$_field_name` IN ( '".implode( "','", $_field_value )."' ) AND " );

					break;
				case 'multicheckbox':
				case 'multiselect':
				case 'select':
				case 'radio':

					$_cookie_value = ( is_array( $_field_value ) ) ? array_sum( $_field_value ) : $_field_value;
                    $bValue = is_array( $_field_value ) ? array_sum( $_field_value ) : $_field_value;

					self::savePreference( $_field_name, $_cookie_value );

					switch ( $_field_name )
					{
						case 'sex':
							$_query_ins .= !empty( $_field_value ) ? sql_placeholder( "? &`main`.`match_sex` AND ", $bValue ) : '';
							break;
						case 'match_sex':

							$_query_ins .= !empty( $_field_value ) ? sql_placeholder( "`main`.`sex` & ? AND ", $bValue ) : '';
							break;
						default:
                            if ( !empty($bValue) )
                            {
                                $_query_ins .= ( SK_ProfileFields::get($_field_name)->base_field )
                                    ? sql_placeholder( "`main`.`$_field_name`& ? AND ", $bValue )
                                    : sql_placeholder( "`extend`.`$_field_name`& ? AND ", $bValue );
                            }

							break;
					}
					break;

				case 'birthdate':
				//case 'date':

					$_cookie_value = $_field_value[0].'-'.$_field_value[1];
					self::savePreference( $_field_name, $_cookie_value );

					if ( !$_field_value )
						break;

					$_query_ins .= (SK_ProfileFields::get($_field_name)->base_field)
								? sql_placeholder( " YEAR(NOW())-YEAR(`main`.`$_field_name`)- IF( DAYOFYEAR(`main`.`$_field_name`) > DAYOFYEAR(NOW()),1,0) >= ?
									AND YEAR(NOW())-YEAR(`main`.`$_field_name`)-IF( DAYOFYEAR(`main`.`$_field_name`) > DAYOFYEAR(NOW()),1,0) <=? AND "
								, $_field_value[0], $_field_value[1] )
								: sql_placeholder( " YEAR(NOW())-YEAR(`extend`.`$_field_name`)- IF( DAYOFYEAR(`extend`.`$_field_name`) > DAYOFYEAR(NOW()),1,0) >= ?
									AND YEAR(NOW())-YEAR(`extend`.`$_field_name`)-IF( DAYOFYEAR(`extend`.`$_field_name`) > DAYOFYEAR(NOW()),1,0) <=? AND "
								, $_field_value[0], $_field_value[1] ) ;

					break;

				case 'checkbox':
					self::savePreference( $_field_name, $_field_value );
					if ( $_field_value )
					{
						$_query_ins .= ( SK_ProfileFields::get($_field_name)->base_field )
									? sql_placeholder( "`main`.`$_field_name`=? AND ", $_field_value )
									: sql_placeholder( "`extend`.`$_field_name`=? AND ", $_field_value );
					}
					break;

				case 'radius':

					if ( !isset($values_arr['city_id']) && !isset($values_arr['zip']) )
						break;

					break;

				case 'array':

					if ( $_field_name == 'mcheck_country' )
						$_query_ins .= self::getLocationSearchString( $_field_name, $_field_value, $values_arr );

					self::savePreference( $_field_name, $_field_value );
					break;

				case 'keyword':

					$_field_value = trim( $_field_value );

					if ( strlen( $_field_value ) )
					{
						$_keyword_fields = app_ProfileField::TextualFields();

						if ( $_keyword_fields )
						{
							$_query_ins .= "(";

							$keyword_values = explode( ' ', $_field_value );

							foreach ( $_keyword_fields as $_keyword_field_name )
							{
								$skip_fields = array('email', 'password');

								if ( in_array($_keyword_field_name, $skip_fields) ) {
									continue;
								}

								foreach( $keyword_values as $_keyword_value ){
									$_query_ins .= ( SK_ProfileFields::get($_keyword_field_name)->base_field )
										? sql_placeholder( "`main`.`$_keyword_field_name` LIKE ? OR ", "$_keyword_value%" )
										: sql_placeholder( "`extend`.`$_keyword_field_name` LIKE ? OR ", "$_keyword_value%" );
								}
							}
							$_query_ins = substr( $_query_ins, 0, -3 );
							$_query_ins .= ") AND";
						}
						self::savePreference( $_field_name, $_field_value );
					}
					break;
				case 'system_checkbox':

					$_query_ins .= self::getCustomFieldSearchString( $_field_name, $_field_value );
					self::savePreference( $_field_name, $_field_value );

					break;
			}
		}

		if (is_array($query_ins)) {
			$sex_criterion = !empty($query_ins['sex']) ? $query_ins['sex'] : '';
			unset($query_ins['sex']);

			$query_include = $sex_criterion . $query_ins[SK_ProfileFields::fullPermissions()];
			unset($query_ins[SK_ProfileFields::fullPermissions()]);
			$query_include .= '( ( ';

                        $query_ins = array_filter($query_ins, 'trim');

			foreach ($query_ins as $key => $item) {
				$_sex_criterion = '';
				if ($item = trim($item)) {
					$query_ins[$key] = $_sex_criterion . substr( $item, 0, -4 );
					$_sex_criterion = sql_placeholder( "NOT `main`.`sex`&? OR ",  $key);
				} else {
					$_sex_criterion = sql_placeholder( "`main`.`sex`&? ",  $key);
				}
				if ($sex_criterion) {
					$query_ins[$key] = $_sex_criterion . $query_ins[$key];
				}

				if (!trim($query_ins[$key])) {
					unset($query_ins[$key]);
				}
			}

			if (count($query_ins)) {
				$query_include .= implode(' ) OR ( ', $query_ins);
			} else {
				$query_include .= $sex_criterion ? substr($sex_criterion, 0, -4 ) : '1';
			}
			$query_include .= ' ) )';
		} else {
			$query_include = substr( $query_ins, 0, -4 );
		}



		// do not inlude current online profile in search result
		$_this_profile_cond = ( SK_HttpUser::is_authenticated() ) ? sql_placeholder( " AND `main`.`profile_id`!=? ", SK_HttpUser::profile_id() ) : '' ;

		$sqlActiveString = app_Profile::SqlActiveString( 'main' );
		$sqlActiveString =( strlen( trim( $sqlActiveString ) ) ) ? ' AND ' . $sqlActiveString : '' ;
		// main query
		$_where_query_cond = ( strlen( trim( $query_include ) ) )
							? "$query_include ".$sqlActiveString." $_this_profile_cond"
							: " 1 ".$sqlActiveString." $_this_profile_cond" ;
//----
		$_query_parts['projection']	= '`main`.`profile_id`';
		$_query_parts['left_join']	= sql_placeholder("
			LEFT JOIN `?#TBL_PROFILE_EXTEND` AS `extend` USING( `profile_id` )
			LEFT JOIN `?#TBL_PROFILE_ONLINE` AS `online` USING( `profile_id` )
		" . $_query_join );
		$_query_parts['condition']	= $_where_query_cond ;
		$_query_parts['group']	= '';
		$_query_parts['order']	= '';


		foreach ( explode("|",SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val)
		{
			if(in_array($val, array('','none')) )
				continue;

			app_ProfileList::_configureOrder($_query_parts, $val, 'main');
		}
//----
                $sex_condition = "";
                
                if ( $config->display_only_looking_for && SK_HttpUser::is_authenticated() )
                {
                    $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
                    
                    if ( !empty($match_sex) )
                    {
                        $sex_condition = " AND `main`.`sex` & " . $match_sex . " ";
                    }
                }
                
                $gender_exclusion = '';

                if ( $config->gender_exclusion && SK_HttpUser::is_authenticated() )
                {
                    $gender_exclusion = ' AND `main`.`sex` != ' . app_Profile::getFieldValues( SK_HttpUser::profile_id(), 'sex' );
                }

                if ( !$only_count )
                {
                    $_query = "SELECT DISTINCT {$_query_parts['projection']} FROM `".TBL_PROFILE."` AS `main`
                            {$_query_parts['left_join']}
                            WHERE {$_query_parts['condition']} $sex_condition $gender_exclusion ".
                            ((strlen($_query_parts['group']))?"\n GROUP BY {$_query_parts['group']}":'')." ".
                            ((strlen($_query_parts['order']))?"\n ORDER BY {$_query_parts['order']}":'')."
                            \nLIMIT ".SEARCH_RESULT_MAX;

                    $_found_profiles = MySQL::fetchArray( $_query, 0 );

                    app_TempProfileList::Delete( app_TempProfileList::getListSessionInfo( 'search', array( 'var_name' => 'list_id' ) ) );
                    app_TempProfileList::deleteListInfoFromSession( 'search' );

                    if ( $_found_profiles )
                    {
                            $_list_info = app_TempProfileList::Create( $search_type, $_found_profiles );

                            app_TempProfileList::setInfoInSession( 'search', array
                                                                                                                            (
                                                                                                                                    'list_id'	=> $_list_info['profile_list_id'],
                                                                                                                                    'pr_total'	=> $_list_info['profile_list_total'],
                                                                                                                            ) );
                    }

                    return true;
                }
                else
                {
                    $result = SK_MySQL::query('
                        SELECT COUNT(`main`.`profile_id`)
                        FROM `' . TBL_PROFILE . '` AS `main` ' .
                            $_query_parts['left_join'] .'
                        WHERE ' . $_query_parts['condition'] . $sex_condition . $gender_exclusion);
                    
                    return $result->fetch_cell();
                }
	}

	/**
	 * Get search result.
	 * Get search result info from existsing profile list or generate new search
	 * calling function appSearchResult::generateResultList().
	 * Returns array with following items:<br />
	 * <li>profile_arr - array with profiles</li>
	 * <li>total - total number of profiles in search result</li>
	 * <li>page - return current page of search result</li>
	 *
	 * @return array
	 */
	public static function Profiles()
	{
		// detect search result page
		$page = app_ProfileList::getPage();
                $search_type = self::getSearchType();


		if ( $_GET['new_search'] )
		{

			// unset flag new search in query string
			unset( $_GET['new_search'] );

			// get search preference
			$values_arr = ( $_GET ) ? $_GET : $_COOKIE['search_preference'];

			if ( !strlen( trim( $search_type ) ) || !is_array( $values_arr ) )
				return array();

			self::generateResultList( $search_type, $values_arr );

			$page = 1;

			self::saveSearchType($search_type);
		}
		elseif (isset($_GET['search_list']))
		{
			$criterion_id = intval($_GET['search_list']);
			$criterion = app_SearchCriterion::getCriterionById($criterion_id);

			self::generateResultList( $criterion['search_type'], $criterion );

			self::saveSearchType($criterion['search_type']);

                        $search_type = $criterion['search_type'];
		}

		$list_id = app_TempProfileList::getListSessionInfo( 'search', 'list_id' );
		if (!$list_id) {
			return array();
		}

		$config = SK_Config::section('site')->Section('additional')->Section('profile_list');

		$_query = sql_placeholder( "SELECT `prof_extend`.*, `prof`.*, `tmp_list_link`.`result_number`, `prof_online`.`hash` AS `online` FROM `".TBL_LINK_PR_LIST_PR."` AS `tmp_list_link`
			LEFT JOIN `".TBL_PROFILE."` AS `prof` USING( `profile_id` )
			LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `prof_extend` USING( `profile_id` )
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `prof_online` USING( `profile_id` )
			WHERE `tmp_list_link`.`profile_list_id`=?
			ORDER BY `tmp_list_link`.`result_number`
			LIMIT ".($config->result_per_page * ( $page-1 )).", ". $config->result_per_page,
			$list_id);

		$_result['profiles'] = MySQL::FetchArray( $_query );

		$_result['total']	= app_TempProfileList::getListSessionInfo( 'search', 'pr_total' );
		$_result['page']	= $page;
                $_result['search_type'] = $search_type;

		return $_result;
	}

	/**
	 * Return array with fields for display on search.
	 * Fields are depended from search type
	 *
	 * @param string $search_type
	 *
	 * @return array
	 */
	public static function SearchTypeFields( $search_type )
	{
		switch ( $search_type )
		{
			case 'refine_keyword':
			case 'keyword':
				$_fields_arr = array( 'keyword_search' );
				break;
			case 'refine_main':
			case 'main':
				//$_fields_arr = app_ProfileField::getPageFields( 'search', 1);
				$_fields_arr  = app_FieldForm::formFields(app_FieldForm::FORM_SEARCH );
				break;
			case 'refine_username':
			case 'username':
				$_fields_arr = array( SK_Config::section('profile_fields')->Section('advanced')->default_username_field_display );
				break;

			case 'quick_search':
				$fields_arr = array
					(
						'sex',
						'match_sex',
						'birthdate',
						'location',
						'search_online_only',
						'search_with_photo_only',
					);
				$_fields_arr = array();
				foreach ($fields_arr as $n) {
					if (SK_ProfileFields::get($n)->searchable) {
						$_fields_arr[] = $n;
					}
				}
				break;
			default:
				$_fields_arr = array();
		}

		return $_fields_arr;
	}

	/**
	 * Save search preference in session and cookie
	 *
	 * @param string $name
	 * @param integer $value
	 * @return boolean
	 */
	public static function savePreference( $name, $value )
	{
		if ( !strlen( trim( $name ) ) )
			return false;

		if ( !is_array( $value ) )
			SK_HttpUser::set_cookie('search_preference['.$name.']', $value);

		$_SESSION['search_preference'][$name] = $value;

		return true;
	}

	public static function clearSearchPreference()
	{
		SK_HttpUser::unset_cookie('search_preference');
		unset( $_SESSION['search_preference'] );
	}

	/**
	 * Save search type in session and cookie
	 *
	 * @param string $value
	 */
	public static function saveSearchType( $value )
	{
		SK_HttpUser::set_cookie('search_type', trim($value));
		$_SESSION['search_type'] = trim($value);
	}

	/**
	 * Return all search preference
	 * or any field value if $name is specified
	 *
	 * @param string $name
	 *
	 * @return array|string
	 */
	public static function getPreference( $name = null )
	{
		return ( strlen( trim( $name ) ) ) ? @$_SESSION['search_preference'][$name] : @$_SESSION['search_preference'];
	}

	/**
	 * Returns search type from session ( if exists ) or cookie ( else )
	 *
	 * @return string
	 */
	public static function getSearchType()
	{
		if ( strlen( trim( $_GET['search_type'] ) ) )
			return $_GET['search_type'];
		else
			return ( strlen( trim( $_SESSION['search_type'] ) ) ) ? $_SESSION['search_type'] : $_COOKIE['search_type'];
	}

	public static function getLocationSearchString( $field_name, $field_value, &$values_arr )
	{

		$_return_where_str = '';
        $_return_join_str = '';

		if ( $field_name == 'state_id' && (isset($values_arr['city_id']) && strlen( trim( $values_arr['city_id'] ) )) && strlen( trim( $field_value ) ) )
		{
			self::savePreference( $field_name, $field_value );
			return '';
		}

		$config = SK_Config::section('site')->Section('additional')->Section('profile_list');

		switch ( $field_name )
		{
			case 'country_id':

				if ( strlen( trim( $field_value ) ) )
				{
					$_return_where_str = SK_MySQL::placeholder( "`main`.`country_id`='?' AND ", $field_value );
					self::savePreference( $field_name, $field_value );
				}
				break;

			case 'state_id':

				if ( strlen( trim( $field_value ) ) && app_Location::isCountryStateExists( @$values_arr['country_id'] ) )
				{
					$_return_where_str = SK_MySQL::placeholder( "`main`.`state_id`='?' AND ", $field_value );
					self::savePreference( $field_name, $field_value );
				}
				elseif ( isset($values_arr['custom_location']) && strlen( $values_arr['custom_location'] ) &&  !app_Location::isCountryStateExists( @$values_arr['country_id'] ) )
				{
					$_return_where_str = SK_MySQL::placeholder( "UPPER(`main`.`custom_location`) LIKE UPPER( '?' ) AND ", "%".@$values_arr['custom_location']."%" );
					self::savePreference( 'custom_location', @$values_arr['custom_location'] );
				}

				break;

			case 'city_id':

				if ( strlen( trim( $field_value ) ) && is_numeric( @$values_arr['radius_first'] ) && intval( @$values_arr['radius_first'] ) )
				{
					$_search_distance_unit = ( $config->search_distance_unit == 'mile' ) ? '3963.0' : '6378.8';

					$_location_query = SK_MySQL::placeholder( "SELECT `Feature_dec_lat` AS `lat`, `Feature_dec_lon` AS `lon`
						FROM `".TBL_LOCATION_CITY."` WHERE `Feature_int_id`='?'", $field_value );

					$_city_info = MySQL::fetchRow( $_location_query );

					$_return_join_str = SK_MySQL::placeholder( " INNER JOIN ( SELECT `Feature_int_id` AS `ID` FROM `".TBL_LOCATION_CITY."` WHERE ( $_search_distance_unit*acos( sin( ".( $_city_info['lat']/57.29577951 ).") * sin(`Feature_dec_lat`/57.29577951) + cos(".( $_city_info['lat']/57.29577951 ).") * cos(`Feature_dec_lat`/57.29577951) * cos(`Feature_dec_lon`/57.29577951 -".$_city_info['lon']/57.29577951.") ) ) <= '?'  ) AS `radius_city` ON ( `radius_city`.`ID` = `main`.`city_id` ) ", $values_arr['radius_first'] );

					self::savePreference( $field_name, $field_value );
					self::savePreference( 'radius_first', @$values_arr['radius_first'] );
				}
				else
				{
					if ( strlen( trim( $field_value ) ) )
					{
						$_return_where_str = SK_MySQL::placeholder( "`main`.`city_id`='?' AND ", $field_value );
						self::savePreference( $field_name, $field_value );
					}
				}
				break;

			case 'zip_first':

				if ( strlen( trim( @$values_arr['zip_first'] ) ) )
				{
					$_search_distance_unit = ( $config->search_distance_unit == 'mile' ) ? '3963.0' : '6378.8';

					if (  is_numeric( @$values_arr['radius_first'] ) && intval( @$values_arr['radius_first'] ) )
					{
						$_location_query = SK_MySQL::placeholder( "SELECT `latitude` AS `lat`, `longitude` AS `lon`
									FROM `".TBL_LOCATION_ZIP."` WHERE `zip`='?' AND `country_id`='?'", @$values_arr['zip_first'], @$values_arr['country_id'] );

						$_zip_info = MySQL::fetchRow( $_location_query );

						$_return_join_str = SK_MySQL::placeholder( " INNER JOIN ( SELECT `zip` FROM `".TBL_LOCATION_ZIP."` WHERE ( $_search_distance_unit*acos( sin( ".( $_zip_info['lat']/57.29577951 ).") * sin(`latitude`/57.29577951) + cos(".( $_zip_info['lat']/57.29577951 ).") * cos(`latitude`/57.29577951) * cos(`longitude`/57.29577951 -".$_zip_info['lon']/57.29577951.") ) ) <= '?'  ) AS `zip_first` ON ( `zip_first`.`zip` = `main`.`zip` ) ", @$values_arr['radius_first'] );

						self::savePreference( $field_name, @$values_arr['zip_first'] );
						self::savePreference( 'radius', @$values_arr['radius_first'] );
					}
					else
					{
						$_return_where_str = SK_MySQL::placeholder( "`main`.`zip`='?' AND", @$values_arr['zip_first'] );

						self::savePreference( $field_name, @$values_arr['zip_first'] );
					}

					if ( strlen( $values_arr['custom_location'] ) &&  !app_Location::isCountryStateExists( @$values_arr['country_id'] ) )
					{
						$_return_where_str = SK_MySQL::placeholder( "UPPER( `main`.`custom_location` ) LIKE UPPER('?') AND ", "%".@$values_arr['custom_location']."%" );
						self::savePreference( 'custom_location', @$values_arr['custom_location'] );
					}
				}

                break;

			case 'zip_third':

				if ( isset($values_arr['zip_third']) && strlen( trim( $values_arr['zip_third'] ) ) )
				{
					$_search_distance_unit = ( $config->search_distance_unit == 'mile' ) ? '3963.0' : '6378.8';

					if (  isset($values_arr['radius_third']) && intval( $values_arr['radius_third'] ) )
					{
						$_location_query = SK_MySQL::placeholder( "SELECT `latitude` AS `lat`, `longitude` AS `lon`
									FROM `".TBL_LOCATION_ZIP."` WHERE `zip`='?'", $values_arr['zip_third'] );

						$_zip_info = MySQL::fetchRow( $_location_query );

						$_return_join_str = SK_MySQL::placeholder( " INNER JOIN ( SELECT `zip` FROM `".TBL_LOCATION_ZIP."` WHERE ( $_search_distance_unit*acos( sin( ".( $_zip_info['lat']/57.29577951 ).") * sin(`latitude`/57.29577951) + cos(".( $_zip_info['lat']/57.29577951 ).") * cos(`latitude`/57.29577951) * cos(`longitude`/57.29577951 -".$_zip_info['lon']/57.29577951.") ) ) <= '?'  ) AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` ) ", @$values_arr['radius_third'] );

						self::savePreference( $field_name, @$values_arr['zip_third'] );
						self::savePreference( 'radius_third', @$values_arr['radius_third'] );
					}
					else
					{
						$_return_where_str = SK_MySQL::placeholder( "`main`.`zip`='?' AND", @$values_arr['zip_third'] );

						self::savePreference( $field_name, @$values_arr['zip_third'] );
					}
				}

				break;

			case 'mcheck_country':

					if ( !is_array( $field_value ) )
						break;

					$_return_where_str = '( ';

					foreach ( $field_value as $_key => $_country_id )
					{
						$_return_where_str .= SK_MySQL::placeholder( "`main`.`country_id`='?' OR ", $_country_id );
					}

					$_return_where_str = substr( $_return_where_str, 0, -3 ).') AND ';
					self::savePreference( $field_name, $field_value );
		}

		return array( 'where' => $_return_where_str, 'join' => $_return_join_str );
	}

	public static function getCustomFieldSearchString( $field_name, $field_value )
	{
		$query_part = '';
		switch ( $field_name )
		{
			case 'search_online_only':

				$query_part .= ( $field_value ) ? "( `online`.`hash` IS NOT NULL ) AND " : '';

				break;
			case 'search_with_photo_only':

				$query_part .= ( $field_value ) ? "( `has_photo`='y' ) AND " : '';
				break;
		}

		return $query_part;
	}

}