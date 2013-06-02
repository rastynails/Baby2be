<?php
class app_SearchCriterion
{
	
	public static function getCriterionList($profile_id)
	{
		 $result = SK_MySQL::query('SELECT `criterion_id`,`criterion_name` FROM `'.TBL_SEARCH_CRITERION."` WHERE `profile_id` = $profile_id;");
		 $out = array();
		 while ($item = $result->fetch_object()) {
		 	$out[] = $item;
		 }
		 return $out;
	}

	public static function saveCriterion($profile_id, $name, $criterion)
	{
		$profile_id = intval($profile_id);
		if(!$profile_id)
			throw new SK_SearchCriterionException("incorrect_profile_id",1);
		//limit cheking
		
		/*if(count($criteria_set_name_arr) == getConfig('user_saved_search_num_on_page'))
			return -1;*/
		//checking the name for existence
		$query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `".TBL_SEARCH_CRITERION."` WHERE `criterion_name`='?' AND `profile_id`=? ", $name, $profile_id);	
		
		if( SK_MySQL::query($query)->fetch_cell() )
			throw new SK_SearchCriterionException('criterion_exists',2);
		//checking the name entered
		if(!$name)
			throw new SK_SearchCriterionException('incorrect_name',3);
			
		$query = SK_MySQL::placeholder("INSERT INTO `".TBL_SEARCH_CRITERION."` VALUES(null, '?', ?, '?')", $name, $profile_id, json_encode($criterion));
		SK_MySQL::query($query);
		$criteria_set_id = SK_MySQL::insert_id();
				
		return $criteria_set_id;	
	}
	
	public static function updateCriterion($criterion_id, $criterion)
	{
		if (is_array($criterion)) {
			$criterion = json_encode($criterion);
		}
		$query = SK_MySQL::placeholder("UPDATE `".TBL_SEARCH_CRITERION."` SET `criterion`='?' WHERE `criterion_id`=?",$criterion, $criterion_id);
		SK_MySQL::query($query);
		return (bool)SK_MySQL::affected_rows();
	}
	
	public static function getCriterion($name)
	{
		$query = SK_MySQL::placeholder("SELECT `criterion` FROM `".TBL_SEARCH_CRITERION."` 
									WHERE `profile_id`=? AND `criterion_name`='?'", SK_HttpUser::profile_id(), $name);
		$criterion = SK_MySQL::query($query)->fetch_cell();
		
		return json_decode($criterion, true);
	}
	
	public static function getCriterionById($criterion_id)
	{
		$query = SK_MySQL::placeholder("SELECT `criterion` FROM `".TBL_SEARCH_CRITERION."` 
									WHERE `criterion_id`=?", $criterion_id);
		$criterion = SK_MySQL::query($query)->fetch_cell();
		
		return json_decode($criterion, true);
	}
	
		
	function deleteCriterion($profile_id, $name)
	{
		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_SEARCH_CRITERION."` 
								WHERE `profile_id`=? AND `criterion_name`='?'", $profile_id, $name);
		SK_MySQL::query($query);
		return (bool)SK_MySQL::affected_rows();
	}	
	
}


class SK_SearchCriterionException extends Exception 
{
	private $error_key;
	
	/**
	 * Constructor.
	 *
	 * @param string $error_key
	 */
	public function __construct( $error_key , $code = null)
	{
		$this->error_key = $error_key;
		
		parent::__construct('', $code);
	}
	
	/**
	 * Get Image error key.
	 *
	 * @return string
	 */
	public function getErrorKey()
	{
		return $this->error_key;
	}
}