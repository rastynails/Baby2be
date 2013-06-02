<?php

/**
 * Class for profile notes
 * (notes are visible only for author)
 *
 */
class app_ProfileNotes
{
    const NOTE_MAX_LENGTH = 200;

	public static function getNoteAboutProfile( $profile_id, $viewer_id )
	{
		if (!isset($profile_id) || !isset($viewer_id))
			return array();

		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_PROFILE_NOTE."` WHERE `on_profile`=? and `by_profile`=?",
			$profile_id, $viewer_id);

		return SK_MySQL::query($query)->fetch_assoc();
	}


	public static function updateNote( $by_profile, $on_profile, $note )
	{
		if (!isset($by_profile) || !isset($on_profile) || !strlen($note))
			return false;

        $note = substr($note, 0, self::NOTE_MAX_LENGTH).'...';

		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE_NOTE."` SET `note_text`='?'
			WHERE `by_profile`=? AND `on_profile`=?", $note, $by_profile, $on_profile);

		SK_MySQL::query($query);

		return SK_MySQL::affected_rows();
	}

	public static function addNote( $by_profile, $on_profile, $note )
	{
		if (!isset($by_profile) || !isset($on_profile) || !strlen($note))
			return false;

        $note = substr($note, 0, self::NOTE_MAX_LENGTH).'...';

		$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_NOTE."` (`by_profile`, `on_profile`, `note_text`)
			VALUES (?,?,'?')", $by_profile, $on_profile, $note);

		SK_MySQL::query($query);

		return SK_MySQL::insert_id();
	}

	public static function deleteNote( $by_profile, $on_profile)
	{
		if (!isset($by_profile) || !isset($on_profile))
			return false;

		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_NOTE."`
			WHERE `by_profile`=? AND `on_profile`=?", $by_profile, $on_profile);

		SK_MySQL::query($query);

		return SK_MySQL::affected_rows();
	}


	/**
	 * Returns array of noted profiles
	 *
	 * @return array
	 */
	public static function getNoteProfiles($by_profile)
	{
		if (!isset($by_profile))
			return false;

		// detect online list result page
		$_page = app_ProfileList::getPage();

		$_query_parts['projection'] = "`profile`.*, `online`.`hash` AS `online`, `note`.*";
		$_query_parts['left_join'] =
		"LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online`
			USING( `profile_id` )";

		$_query_parts['condition'] = app_Profile::SqlActiveString( 'profile' );
		$_query_parts['order'] = "";
		$result_per_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
		$_query_parts['limit'] = $result_per_page  *( $_page-1 ).", ".$result_per_page ;

		foreach ( explode("|",SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val)
		{
			if(in_array($val, array('','none')) )
				continue;

			app_ProfileList::_configureOrder($_query_parts, $val);
		}

		$_query = "SELECT {$_query_parts['projection']} FROM `".TBL_PROFILE."` AS `profile`
			{$_query_parts['left_join']} LEFT JOIN `".TBL_PROFILE_NOTE."` AS `note` ON (`profile`.`profile_id`=`note`.`on_profile`)
			WHERE {$_query_parts['condition']} AND `note`.`by_profile`=".$by_profile." ".
			( (strlen(@$_query_parts['group']))?" GROUP BY {$_query_parts['group']}":"" ).
			( (strlen($_query_parts['order']))?" ORDER BY {$_query_parts['order']}":"" ).
			" LIMIT {$_query_parts['limit']}";
		$_result['profiles'] = MySQL::fetchArray( $_query );

		// get total online profiles
		$_query = "SELECT COUNT( `profile`.`profile_id` ) FROM `".TBL_PROFILE."` AS `profile`
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` USING( `profile_id` )
			LEFT JOIN `".TBL_PROFILE_NOTE."` AS `note` ON( `profile`.`profile_id` = `note`.`on_profile` )
			WHERE `profile`.`featured` = 'y' AND ".app_Profile::SqlActiveString( 'profile' )."
			AND `note`.`by_profile` = $by_profile
			ORDER BY `activity_stamp` DESC";

		$_result['total'] = MySQL::fetchField( $_query );

		return $_result;
	}
}
