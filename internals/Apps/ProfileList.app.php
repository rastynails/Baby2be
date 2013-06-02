<?php

/**
 * Class for working with profile list
 *
 * @package SkaDate
 * @link http://www.skadate.com
 * @version 5.0
 */
class app_ProfileList
{

    /**
     * Return profile list view mode
     * View mode can be:<br/>
     * <li>gallery</li>
     * <li>details</li>
     *
     * @return string
     */
    public static function getViewMode()
    {
        return ( isset(SK_HttpRequest::$GET['view_mode']) && strlen(SK_HttpRequest::$GET['view_mode']) ) ? SK_HttpRequest::$GET['view_mode'] : @$_SESSION['pr_list_view_mode'];
    }

    /**
     * Set profile list view mode
     * View mode can be:<br/>
     * <li>gallery</li>
     * <li>details</li>
     *
     * @param string $value
     */
    public static function setViewMode( $value )
    {
        $_SESSION['pr_list_view_mode'] = trim($value);
    }

    public static function getPage()
    {
        // detect online list result page
        return ( isset(SK_HttpRequest::$GET['page']) && intval(SK_HttpRequest::$GET['page']) ) ? SK_HttpRequest::$GET['page'] : 1;
    }
    private static $limits = array();

    private static function getLimit( $list )
    {
        if ( isset(self::$limits[$list]) )
        {
            return self::$limits[$list];
        }

        if ( isset(self::$limits['%%all%%']) )
        {
            return self::$limits['%%all%%'];
        }
        $count = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
        return array(
            'count' => $count,
            'offset' => $count * ( self::getPage() - 1 )
        );
    }

    public static function setLimit( $count, $offset = 0, $list = '%%all%%' )
    {
        if ( !($count = intval($count)) )
        {
            return false;
        }

        self::$limits[$list] = array('offset' => $offset, 'count' => $count);
    }

    public static function unsetLimit( $list = null )
    {
        if ( !isset($list) )
        {
            self::$limits = array();
        }
        else
        {
            unset(self::$limits[$list]);
        }
    }
//-----------------------------------< / Lists >----------------------------------------------------------

    /**
     * Returns array with online profiles
     *
     * @return array
     */
    public static function OnlineList( $sex = '' )
    {
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] = sql_placeholder(
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
			USING( `profile_id` )"
        );

        $avaliable_values = SK_ProfileFields::get('sex')->values;

        $_query_parts['condition'] = in_array($sex, $avaliable_values) ? ' `profile`.`sex`=' . $sex . ' AND ' : '';
        $_query_parts['condition'] .= "`online`.`hash` IS NOT NULL AND " . app_Profile::SqlActiveString('profile');
        $_query_parts['order'] = "";

        $limit = self::getLimit('online');
        $_query_parts['limit'] = $limit['offset'] . ", " . $limit['count'];


        foreach ( explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }
//----
        $config = SK_Config::section( 'site.additional.profile_list' );
        $gender_exclusion = '';
        $sex_condition = '';

        if ( $config->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'sex' );
        }

        if ( $config->display_only_looking_for )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

            $sex_condition = !empty( $match_sex ) ? " AND `profile`.`sex` & " . $match_sex . " " : '';
        }


        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} $gender_exclusion $sex_condition " .
            ( isset($_query_parts['group']) && (strlen($_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT {$_query_parts['limit']}";

        $_result['profiles'] = MySQL::fetchArray($_query); //print_arr($_query);

        $_result['total'] = self::findOnlineCount($sex);

        return $_result;
    }

    public static function findOnlineCount( $sex = null )
    {
        $where = !empty($sex) ? SK_MySQL::placeholder(' `profile`.`sex`=? AND ', $sex) : '';
        $where .= "`online`.`hash` IS NOT NULL AND " . app_Profile::SqlActiveString('profile');

        $config = SK_Config::section( 'site.additional.profile_list' );
        $gender_exclusion = '';
        $sex_condition = '';

        if ( $config->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'sex' );
        }

        if ( $config->display_only_looking_for )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

            $sex_condition = !empty( $match_sex ) ? " AND `profile`.`sex` & " . $match_sex . " " : '';
        }

        $query = "SELECT COUNT( `profile`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `profile`
            LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
            WHERE $where $gender_exclusion $sex_condition";

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Returns array with featured profiles
     *
     * @return array
     */
    public static function FeaturedList()
    {
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] =
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
			USING( `profile_id` )";

        $config = SK_Config::section( 'site.additional.profile_list' );
        $sex_condition = "";
        if ( $config->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        $_query_parts['condition'] = "`profile`.`featured` = 'y' AND " . app_Profile::SqlActiveString('profile') . $sex_condition;
        $_query_parts['order'] = "";

        $limit = self::getLimit('featured');
        $_query_parts['limit'] = $limit['offset'] . ", " . $limit['count'];

        foreach ( explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }

        $gender_exclusion = '';

        if ( $config->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'sex' );
        }

        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} $gender_exclusion " .
            ( (strlen(@$_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT {$_query_parts['limit']}";
        $_result['profiles'] = MySQL::fetchArray($_query); //print_arr($_query);
        // get total online profiles
        $_query = "SELECT COUNT( `profile`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `profile`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			WHERE `profile`.`featured` = 'y' AND " . app_Profile::SqlActiveString('profile') . $sex_condition;

        $_result['total'] = MySQL::fetchField($_query);

        return $_result;
    }

    public static function ModeratorList()
    {
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] =
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
			USING( `profile_id` )
         INNER JOIN `" . TBL_SITE_MODERATORS . "` AS `mod`
			USING( `profile_id` )";

        $_query_parts['condition'] = app_Profile::SqlActiveString('profile');
        $_query_parts['order'] = "";

        $limit = self::getLimit('moderator');
        $_query_parts['limit'] = $limit['offset'] . ", " . $limit['count'];

        $config = SK_Config::section( 'site.additional.profile_list' );

        foreach ( explode("|", $config->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }

        $gender_exclusion = '';
        $sex_condition = '';
        
        if ( $config->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        if ( $config->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'sex' );
        }

        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} $gender_exclusion $sex_condition " .
            ( (strlen(@$_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT {$_query_parts['limit']}";
        $_result['profiles'] = MySQL::fetchArray($_query); //print_arr($_query);
        // get total online profiles
        $_query = "SELECT COUNT( `profile`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `profile`
			INNER JOIN `" . TBL_SITE_MODERATORS . "` AS `mod`	USING( `profile_id` )
			WHERE " . app_Profile::SqlActiveString('profile') . $gender_exclusion . $sex_condition;

        $_result['total'] = MySQL::fetchField($_query);

        return $_result;
    }

    /**
     * Returns array with featured profiles
     *
     * @return array
     */
    public static function AllProfileList()
    {
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] =
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
			USING( `profile_id` )";

        $sex_condition = "";
        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }
        $_query_parts['condition'] = app_Profile::SqlActiveString('profile') . $sex_condition;
        $_query_parts['order'] = "";

        $limit = self::getLimit('all');
        $_query_parts['limit'] = $limit['offset'] . ", " . $limit['count'];

        foreach ( explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }

        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} " .
            ( (strlen(@$_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT {$_query_parts['limit']}";
        $_result['profiles'] = MySQL::fetchArray($_query); //print_arr($_query);
        // get total online profiles
        $_query = "SELECT COUNT( `profile`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `profile` WHERE " . app_Profile::SqlActiveString('profile') . $sex_condition;

        $_result['total'] = MySQL::fetchField($_query);

        return $_result;
    }

    public static function ProfileMatcheList( $profile_id, $profile_count = null, $is_random = false, $_new = false )
    {
        return array(
            'profiles' => self::getMatcheList($profile_id, $profile_count, $is_random, $_new),
            'total' => self::CountProfileMatch($profile_id, $_new)
        );
    }

    public static function getMatcheList( $profile_id, $profile_count = null, $is_random = false, $_new = false )
    {
        if ( !is_numeric($profile_id) || !intval($profile_id) )
            return array();

        // detect profile limit
        if ( !empty($profile_count) )
        {
            self::setLimit($profile_count, 0, 'match');
        }

        $config = SK_Config::section( 'site.additional.profile_list' );
        
        // detect matching fields
        $_query = "SELECT `match`.`match_type`, `field`.`name` AS `field_name`, `match_field`.`name` AS `match_field_name`
			FROM `" . TBL_PROF_FIELD_MATCH_LINK . "` AS `match`
			LEFT JOIN `" . TBL_PROF_FIELD . "` AS `field` USING ( `profile_field_id` )
			LEFT JOIN `" . TBL_PROF_FIELD . "` AS `match_field` ON `match`.`match_profile_field_id`=`match_field`.`profile_field_id`";

        $_fields_arr = MySQL::fetchArray($_query);

        $_profile_info = app_Profile::getFieldValues($profile_id, array('country_id'));

        if ( !strlen($_profile_info['country_id']) )
            return array();

//-----
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] = "
			LEFT JOIN `" . TBL_PROFILE_EXTEND . "` AS `extend` USING( `profile_id` )
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			";
        $_query_parts['group'] = '';

        if ( $is_random )
        {
            $_query_parts['order'] = " RAND() ";
        }
        else
        {
            foreach ( explode("|", $config->order) as $val )
            {
                if ( in_array($val, array('', 'none')) )
                    continue;

                self::_configureOrder($_query_parts, $val);
            }
        }

        $_by_country_sql_cond = app_ProfilePreferences::get('matches', 'in_my_country_only_matches', $profile_id) ? sql_placeholder('`country_id` = ?', $_profile_info['country_id']) : '1';
        $_main_query = sql_placeholder("SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE $_by_country_sql_cond AND `profile`.`profile_id` NOT IN ( SELECT `blocked_id` FROM `" . TBL_PROFILE_BLOCK_LIST . "`
			WHERE `profile_id` =? ) ", $profile_id);

        $_query_cond = '';

        foreach ( $_fields_arr as $_key => $_match_info )
        {
            $_profile_info = app_Profile::getFieldValues($profile_id, array($_match_info['field_name'], $_match_info['match_field_name']));


            switch ( $_match_info['match_type'] )
            {
                case 'exact':
                    $_field_value = ( $_profile_info[$_match_info['field_name']] ) ? $_profile_info[$_match_info['field_name']] : MAX_SQL_BIGINT;
                    $_match_field_value = ( $_profile_info[$_match_info['match_field_name']] ) ? $_profile_info[$_match_info['match_field_name']] : MAX_SQL_BIGINT;

                    $_query_cond .= "AND ( $_field_value&IF( ISNULL(`" . $_match_info['match_field_name'] . "`) OR `" . $_match_info['match_field_name'] . "`=0, " . MAX_SQL_BIGINT . ", `" . $_match_info['match_field_name'] . "` )
						AND $_match_field_value&IF( ISNULL(`" . $_match_info['field_name'] . "`) OR `" . $_match_info['field_name'] . "`=0, " . MAX_SQL_BIGINT . ", `" . $_match_info['field_name'] . "` ) ) ";
                    break;

                case 'range':

                    if ( !$_profile_info[$_match_info['match_field_name']] || !$_profile_info[$_match_info['field_name']] )
                        continue 2;

                    $_age_info = explode('-', $_profile_info[$_match_info['match_field_name']]);

                    $_query_cond .= sql_placeholder("AND YEAR('" . date("Y-m-d H:i:s") . "')-YEAR(`{$_match_info['field_name']}`)- IF( DAYOFYEAR(`{$_match_info['field_name']}`) > DAYOFYEAR('" . date("Y-m-d H:i:s") . "'),1,0) >= ?
									AND YEAR('" . date("Y-m-d H:i:s") . "')-YEAR(`{$_match_info['field_name']}`)-IF( DAYOFYEAR(`{$_match_info['field_name']}`) > DAYOFYEAR('" . date("Y-m-d H:i:s") . "'),1,0) <=?"
                        , $_age_info[0], $_age_info[1]);


                    $_query_cond .= sql_placeholder(
                        " AND SUBSTRING_INDEX( `{$_match_info['match_field_name']}`, '-', 1 ) <= ?profile_age
					     AND SUBSTRING_INDEX( `{$_match_info['match_field_name']}`, '-', -1 ) >= ?profile_age ", array('profile_age' => app_Profile::getAge($_profile_info[$_match_info['field_name']])));

                    break;
            }
        }

        $new_matches_sql_cond = $_new ? self::getSqlCondForSendMatchesEmail($profile_id, 'profile') : '';

        $_photo_only = app_ProfilePreferences::get('matches', 'with_photo_only_matches', $profile_id) ? true : false;
        $_photo_only_sql_cond = $_photo_only ? 'AND `profile`.`has_photo` = "y"' : '';
        $sex_condition = "";
        if ( $config->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }
        $gender_exclusion = '';

        if ( $config->gender_exclusion )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
        }

        $_main_query .= sql_placeholder($_query_cond . " AND " . app_Profile::SqlActiveString('profile') . " AND `profile`.`profile_id`!= ? $new_matches_sql_cond $_photo_only_sql_cond $sex_condition $gender_exclusion", $profile_id);

        $limit = self::getLimit('match');

        $_main_query = sql_placeholder($_main_query .
            ((isset($_query_parts['order']) && strlen($_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ((isset($_query_parts['order']) && strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "") .
            " LIMIT ?limit_begin, ?count_display", array
            (
            'limit_begin' => $limit['offset'],
            'count_display' => $limit['count']
            ));


        return MySQL::fetchArray($_main_query);
    }

    public static function CountProfileMatch( $profile_id, $_new = false )
    {
        if ( !is_numeric($profile_id) || !intval($profile_id) )
            return 0;

        $_query_cond = '';
        // detect matching fields
        $_query = "SELECT `match`.`match_type`, `field`.`name` AS `field_name`, `match_field`.`name` AS `match_field_name`
			FROM `" . TBL_PROF_FIELD_MATCH_LINK . "` AS `match`
			LEFT JOIN `" . TBL_PROF_FIELD . "` AS `field` USING ( `profile_field_id` )
			LEFT JOIN `" . TBL_PROF_FIELD . "` AS `match_field` ON `match`.`match_profile_field_id`=`match_field`.`profile_field_id`";

        $_fields_arr = MySQL::fetchArray($_query);

        $_profile_info = app_Profile::getFieldValues($profile_id, array('country_id'));

        if ( !strlen($_profile_info['country_id']) )
            return 0;
        $_by_country_sql_cond = app_ProfilePreferences::get('matches', 'in_my_country_only_matches', $profile_id) ? sql_placeholder('`country_id` = ?', $_profile_info['country_id']) : '1';

        $_count_main_query = sql_placeholder("SELECT COUNT( `main`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `main`
			LEFT JOIN `" . TBL_PROFILE_EXTEND . "` AS `extend` USING( `profile_id` )
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			WHERE $_by_country_sql_cond
			AND `main`.`profile_id` NOT IN ( SELECT `blocked_id` FROM `" . TBL_PROFILE_BLOCK_LIST . "`
			WHERE `profile_id` =?  )  ", $profile_id);

        foreach ( $_fields_arr as $_key => $_match_info )
        {
            $_profile_info = app_Profile::getFieldValues($profile_id, array($_match_info['field_name'], $_match_info['match_field_name']));

            switch ( $_match_info['match_type'] )
            {
                case 'exact':

                    $_field_value = ( $_profile_info[$_match_info['field_name']] ) ? $_profile_info[$_match_info['field_name']] : MAX_SQL_BIGINT;
                    $_match_field_value = ( $_profile_info[$_match_info['match_field_name']] ) ? $_profile_info[$_match_info['match_field_name']] : MAX_SQL_BIGINT;

                    $_query_cond .= "AND ( $_field_value&IF( ISNULL(`" . $_match_info['match_field_name'] . "`) OR `" . $_match_info['match_field_name'] . "`=0, " . MAX_SQL_BIGINT . ", `" . $_match_info['match_field_name'] . "` )
						AND $_match_field_value&IF( ISNULL(`" . $_match_info['field_name'] . "`) OR `" . $_match_info['field_name'] . "`=0, " . MAX_SQL_BIGINT . ", `" . $_match_info['field_name'] . "` ) ) ";
                    break;

                case 'range':

					if ( !$_profile_info[$_match_info['match_field_name']] || !$_profile_info[$_match_info['field_name']] )
                        continue 2;

                    $_age_info = explode('-', $_profile_info[$_match_info['match_field_name']]);

                    $_query_cond .= sql_placeholder("AND YEAR('" . date("Y-m-d H:i:s") . "')-YEAR(`{$_match_info['field_name']}`)- IF( DAYOFYEAR(`{$_match_info['field_name']}`) > DAYOFYEAR('" . date("Y-m-d H:i:s") . "'),1,0) >= ?
									AND YEAR('" . date("Y-m-d H:i:s") . "')-YEAR(`{$_match_info['field_name']}`)-IF( DAYOFYEAR(`{$_match_info['field_name']}`) > DAYOFYEAR('" . date("Y-m-d H:i:s") . "'),1,0) <=?"
                        , $_age_info[0], $_age_info[1]);


                    $_query_cond .= sql_placeholder(
                        " AND SUBSTRING_INDEX( `{$_match_info['match_field_name']}`, '-', 1 ) <= ?profile_age
					     AND SUBSTRING_INDEX( `{$_match_info['match_field_name']}`, '-', -1 ) >= ?profile_age ", array('profile_age' => app_Profile::getAge($_profile_info[$_match_info['field_name']])));

                    break;
            }
        }

        $new_matches_sql_cond = $_new ? self::getSqlCondForSendMatchesEmail($profile_id, 'main') : '';

        $_photo_only = app_ProfilePreferences::get('matches', 'with_photo_only_matches', $profile_id) ? true : false;
        $_photo_only_sql_cond = $_photo_only ? 'AND `main`.`has_photo` = "y"' : '';

        $config = SK_Config::section( 'site.additional.profile_list' );

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
            $gender_exclusion = ' AND `main`.`sex` != ' . app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'sex' );
        }

        $_count_main_query .= sql_placeholder($_query_cond . " AND " . app_Profile::SqlActiveString('main') . " AND `main`.`profile_id`!= ? $new_matches_sql_cond $_photo_only_sql_cond $sex_condition $gender_exclusion", $profile_id);

        return MySQL::fetchField($_count_main_query);
    }

    public static function TopRatedProfileList()
    {

        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`, SUM(`rate`.`score`)/COUNT(`rate`.`profile_id`) AS `score`";
        $_query_parts['left_join'] = sql_placeholder(
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			LEFT JOIN `" . TBL_PROFILE_RATE . "` AS `rate` ON `profile`.`profile_id`=`rate`.`profile_id`"
        );

        $sex_condition = "";
        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        $_query_parts['condition'] = appProfile::getProfileSqlActiveString('profile') . $sex_condition;
        $_query_parts['order'] = "`score` DESC";
        $limit = self::getLimit('top_rated');
        $_query_parts['limit'] = $limit['offset'] . ", " . $limit['count'];
        $_query_parts['group'] = "`profile`.`profile_id`";

        foreach ( explode("|", getConfig('profile_list_order')) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }
//----

        $_query = sql_placeholder("SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} " .
            ( (strlen($_query_parts['group'])) ? "GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? "ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT " . $_query_parts['limit']);

        $_result['profiles_arr'] = MySQL::fetchArray($_query); //print_arr($_query);

        $_query = sql_placeholder("SELECT COUNT(*) FROM (SELECT COUNT(`profile`.`profile_id`), SUM(`rate`.`score`)/COUNT(`rate`.`profile_id`) AS `score` FROM `" . TBL_PROFILE . "` AS `profile`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			LEFT JOIN `" . TBL_PROFILE_RATE . "` AS `rate` ON `profile`.`profile_id`=`rate`.`profile_id`
			WHERE " . appProfile::getProfileSqlActiveString('profile') . $sex_condition . "
			GROUP BY `rate`.`profile_id`
			ORDER BY `score` DESC, `activity_stamp` DESC) as q");

        $_result['total'] = MySQL::fetchField($_query);

        return $_result;
    }

    public static function BirthdayList( $mode, $only_count = false )
    {

        if ( $mode == 'tomorrow' )
            $birth_add = " AND DAYOFMONTH(`profile`.`birthdate`) = DAYOFMONTH( DATE_ADD( '" . date("Y-m-d H:i:s") . "', INTERVAL 1 DAY ) ) AND MONTH(`profile`.`birthdate`) = MONTH( DATE_ADD('" . date("Y-m-d H:i:s") . "', INTERVAL 1 DAY ) ) ";
        else
            $birth_add = " AND DAYOFMONTH(`profile`.`birthdate`) = DAYOFMONTH( '" . date("Y-m-d H:i:s") . "') AND MONTH(`profile`.`birthdate`) = MONTH( '" . date("Y-m-d H:i:s") . "') ";

        $config = SK_Config::section( 'site.additional.profile_list' );

        $sex_condition = "";
        if ( $config->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        $gender_exclusion = '';
        
        if ( $config->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues( SK_HttpUser::profile_id(), 'sex' );
        }

        $query = "SELECT COUNT(`profile`.`profile_id`) FROM `" . TBL_PROFILE . "` AS `profile`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			WHERE " . app_Profile::SqlActiveString('profile') . $birth_add . $sex_condition . $gender_exclusion . "
			ORDER BY `profile`.`activity_stamp` DESC";

        $result['total'] = SK_MySQL::query($query)->fetch_cell();

        if ( $only_count )
            return $result['total'];
//-----
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] = "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )";
        $_query_parts['order'] = '';
        $_query_parts['group'] = '';

        foreach ( explode("|", $config->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }
//-----

        $result_per_page = $config->result_per_page;

        $limit = self::getLimit('match');
        $query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE " . app_Profile::SqlActiveString('profile') . $birth_add . $sex_condition . $gender_exclusion . "
			" .
            ((strlen($_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : '') .
            ((strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : '')
            . " LIMIT " . $limit['offset'] . ", " . $limit['count'];

        $query_result = SK_MySQL::query($query);

        while ( $item = $query_result->fetch_assoc() )
            $result['profiles'][] = $item;

        return $result;
    }

    public static function NewMembersList( $only_count = false )
    {
        $config_section = SK_Config::Section('site')->Section('additional')->Section('profile_list');

        $new_members_period = time() - (intval($config_section->new_members_period) * 24 * 60 * 60);

        $logged_profile_sep = SK_HttpUser::is_authenticated() ? SK_MySQL::placeholder("`profile`.`profile_id`!= ?", SK_HttpUser::profile_id()) : '1';

        $new_members_add = " AND `profile`.`join_stamp` BETWEEN  $new_members_period AND " . time() . " AND $logged_profile_sep";

        $sex_condition = "";
        if ( $config_section->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        $gender_exclusion = '';

        if ( $config_section->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues( SK_HttpUser::profile_id(), 'sex' );
        }
        
        $query = "SELECT COUNT(`profile`.`profile_id`) FROM `" . TBL_PROFILE . "` AS `profile`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )
			WHERE " . app_Profile::SqlActiveString('profile') . $new_members_add . $sex_condition . $gender_exclusion;

        $result['total'] = SK_MySQL::query($query)->fetch_cell();

        if ( $only_count )
            return $result['total'];
//-----
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] = "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` USING( `profile_id` )";
        $_query_parts['order'] = '`profile`.`join_stamp` DESC';
        $_query_parts['group'] = '';

        /* 	foreach ( explode("|",SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val)
          {
          if(in_array($val, array('','none')) )
          continue;

          self::_configureOrder($_query_parts, $val);
          } */
//-----
        $limit = self::getLimit('new_members');
        $query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE " . app_Profile::SqlActiveString('profile') . $new_members_add . $sex_condition . $gender_exclusion . "
			" .
            ((strlen($_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : '') .
            ((strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : '')
            . " LIMIT " . ($limit['offset']) . ", " . $limit['count'];

        $query_result = SK_MySQL::query($query);
        while ( $item = $query_result->fetch_assoc() )
            $result['profiles'][] = $item;

        return $result;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public static function BirthdayProfiles()
    {
        $query = sql_placeholder("SELECT `profile`.`username`, `profile`.`profile_id`, `profile`.`email`, `profile`.`language_id` FROM `" . TBL_PROFILE . "`  AS `profile`
			WHERE " . app_Profile::SqlActiveString('profile') . " AND DAYOFMONTH(`profile`.`birthdate`) = DAYOFMONTH( '" . date("Y-m-d H:i:s") . "') AND MONTH(`profile`.`birthdate`) = MONTH( '" . date("Y-m-d H:i:s") . "')");

        return MySQL::fetchArray($query);
    }

//-----------------------------------< / Lists >----------------------------------------------------------

    public static function countAllProfiles()
    {
        return (int) MySQL::fetchField(sql_placeholder("SELECT COUNT(*) AS `count` FROM `?#TBL_PROFILE`"));
    }

    public static function _configureOrder( &$query_parts, $type, $alias = 'profile' )
    {
        $query_parts['left_join'] = isset($query_parts['left_join']) ? $query_parts['left_join'] : '';
        $query_parts['projection'] = isset($query_parts['projection']) ? $query_parts['projection'] : '';
        $query_parts['group'] = isset($query_parts['group']) ? $query_parts['group'] : '';
        $query_parts['order'] = isset($query_parts['order']) ? $query_parts['order'] : '';


        switch ( $type )
        {
            case 'paid members':

                $query_parts['projection'] .= (strlen($query_parts['projection']) ? "," : "") .
                    " SUM(`fin_sale`.`amount`) AS `total_amount`";

                $query_parts['left_join'] .=
                    sql_placeholder(" LEFT JOIN `?#TBL_FIN_SALE` AS `fin_sale` ON (`$alias`.`profile_id` = `fin_sale`.`profile_id`)");

                $query_parts['group'] = "`$alias`.`profile_id`";

                $query_parts['order'] .= (strlen($query_parts['order']) ? "," : "") .
                    "`total_amount` DESC";
                break;

            case 'last activity':
                $query_parts['order'] .= (strlen($query_parts['order']) ? "," : "") .
                    " IF( `online`.`profile_id` <> NULL, 0 , 1 ), `activity_stamp` DESC";
                break;

            case 'with photo':

                $query_parts['order'] .= (strlen($query_parts['order']) ? "," : "") .
                    "`$alias`.`has_photo` DESC";
                break;

            case 'join date':

                $query_parts['order'] .= (strlen($query_parts['order']) ? "," : "") .
                    "`$alias`.`join_stamp` DESC";
                break;
        }
    }

    private static function getSqlCondForSendMatchesEmail( $profile_id, $profile_tbl_alias )
    {
        $config_section = SK_Config::section('scheduler')->Section('match_list');
        if ( !$config_section->new_matches )
            return '';

        $profile_id = intval($profile_id);
        $period = $config_section->new_match_period;
        $period_measure = $config_section->match_period_measure;

        switch ( $period_measure )
        {
            case 1:
                $period = $period * 3600 * 24;
                break;
            case 2:
                $period = $period * 3600 * 24 * 7;
                break;
            case 3:
                $period = $period * 3600 * 24 * 7 * 31;
                break;
        }

        $period_cond = intval($period) ? SK_MySQL::placeholder("AND `$profile_tbl_alias`.`join_stamp` BETWEEN ? AND ? AND `$profile_tbl_alias`.`join_stamp`", time() - intval($period), time()) : '';
        $sent_matches_cond = SK_MySQL::placeholder(" AND `$profile_tbl_alias`.`profile_id` NOT IN( SELECT `match_profile_id` FROM `" . TBL_PROFILE_SENT_MATCHES . "` WHERE `profile_id` = ? )", $profile_id);
        return $period_cond . $sent_matches_cond;
    }

    /**
     * Returns URL to specified profile list
     *
     * @param string $base_url
     * @param string $list_command
     * @param integer $page
     * @return string
     */
    function getListURL( $base_url, $list_command, $page = 1 )
    {
        $_return_url = GetURL($base_url, 'command', 'page') . 'page=' . $page . '&command=' . $list_command;

        return $_return_url;
    }

    /**
     * Returns array with featured profiles
     *
     * @return array
     */
    public static function IndexFeaturedList( $count = 8 )
    {
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] =
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
			USING( `profile_id` )";

        $sex_condition = "";
        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        $_query_parts['condition'] = "`profile`.`featured` = 'y' AND " . app_Profile::SqlActiveString('profile') . $sex_condition;
        $_query_parts['order'] = "";
        $result_per_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
        $_query_parts['limit'] = $count;

        foreach ( explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }

        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} " .
            ( (strlen(@$_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT {$_query_parts['limit']}";
        $_result = MySQL::fetchArray($_query); //print_arr($_query);

        return $_result;
    }

    public static function findLatestList( $start, $count, $sex = null )
    {
        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] =
            "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
            USING( `profile_id` )";

        $_query_parts['condition'] = app_Profile::SqlActiveString('profile');
        if ( !empty($sex) )
        {
            $sex = intval($sex);
            $_query_parts['condition'] .= " AND `profile`.`sex` = $sex";
        }
        else if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $_query_parts['condition'] .= " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }

        $_query_parts['order'] = "";
        $_query_parts['limit'] = $start . ', ' . $count;


        $order = explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order);
        array_unshift($order, 'join date');

        foreach ( $order as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }

        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
            {$_query_parts['left_join']}
            WHERE {$_query_parts['condition']} " .
            ( (strlen(@$_query_parts['group'])) ? " GROUP BY {$_query_parts['group']}" : "" ) .
            ( (strlen($_query_parts['order'])) ? " ORDER BY {$_query_parts['order']}" : "" ) .
            " LIMIT {$_query_parts['limit']}";

        $_result['profiles'] = MySQL::fetchArray($_query); //print_arr($_query);

        $query = "SELECT COUNT(`profile_id`) FROM `" . TBL_PROFILE . "` AS `profile`
            WHERE {$_query_parts['condition']}";

        $_result['total'] = SK_MySQL::query($query)->fetch_cell();

        return $_result;
    }

    public static function getProfileListByUserIdList( array $userIdList )
    {
        if ( empty($userIdList) )
        {
            return $_result = array( 'profiles' => array(),
                                     'total' => 0 );
        }

        $_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`";
        $_query_parts['left_join'] =
            " LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online`
			USING( `profile_id` ) ";
        $_query_parts['condition'] = SK_MySQL::placeholder(" `profile`.`profile_id` IN ( '?@' ) ", $userIdList );

        $sex_condition = "";
        /* 
        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_only_looking_for && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `profile`.`sex` & " . $match_sex . " ";
            }
        }
        $_query_parts['condition'] = app_Profile::SqlActiveString('profile') . $sex_condition;
        $_query_parts['order'] = ""; */

/*        $limit = self::getLimit('all');
        $_query_parts['limit'] = $limit['offset'] . ", " . $limit['count'];*/
        $_query_parts['limit'] = '';

        foreach ( explode("|", SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            self::_configureOrder($_query_parts, $val);
        }

        $_query = "SELECT {$_query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `profile`
			{$_query_parts['left_join']}
			WHERE {$_query_parts['condition']} ";
            
        $_result['profiles'] = MySQL::fetchArray($_query); //print_arr($_query);
        // get total online profiles
        $_query = "SELECT COUNT( `profile`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `profile` WHERE " . app_Profile::SqlActiveString('profile') . $sex_condition;

        $_result['total'] = count($userIdList);

        return $_result;
    }

}

