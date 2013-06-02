<?php
class app_Poll{
	 public static function add($expiration_timestamp){
	 	$_query = sql_placeholder("INSERT INTO `?#TBL_POLL` SET `id`=null, `expiration_timestamp`=?", $expiration_timestamp);

	 	return MySQL::insertId($_query);
	 }
	 
	 public static function get( $id ){
	 	$_query  = sql_placeholder("SELECT * FROM `?#TBL_POLL` WHERE `id` = ?", $id);
	 	return MySQL::fetchRow($_query);
	 }
	 
	 public static function addPollAnswer($pollId){
	 	$_query = sql_placeholder("INSERT INTO `?#TBL_POLL_ANSWER` SET `id`=null, `pollId`=?", $pollId );
	 	return MySQL::insertId($_query); 
	 }
	 
	 public static function deletePollAnswer($anwertId){
	 	$_query = sql_placeholder("DELETE FROM `?#TBL_POLL_ANSWER` WHERE `id`=?", $anwertId);
	 	
	 	return MySQL::affectedRows( $_query );
	 }
	 
	 public static function getList($first, $count, $active=false){
	 	$q_parts['isActive'] = ($active === true)? "`isActive` = 1" : 1;
	 	$_query = sql_placeholder("SELECT * FROM `?#TBL_POLL` WHERE {$q_parts['isActive']} LIMIT {$first}, {$count}");

	 	return MySQL::fetchArray($_query);
	 }
	 
	 public static function getCount($active=false){
	 	$q_parts['isActive'] = ($active === true)? "`isActive` = 1" : 1;
	 	$_query = sql_placeholder("SELECT COUNT(*) FROM `?#TBL_POLL` WHERE {$q_parts['isActive']}");

	 	return MySQL::fetchField($_query);
	 }

	 public static function delete($pollId){
	 	
	 	$_query = sql_placeholder( "DELETE FROM `?#TBL_POLL_ANSWER` WHERE `pollId`=?", $pollId );
	 	MySQL::affectedRows($_query);
	 	
	 	$_query = sql_placeholder( "DELETE FROM `?#TBL_POLL` WHERE `id`=?", $pollId );
	 	MySQL::affectedRows($_query);
	 }
	 
	 public static function getAnswers($pollId){
	 	$_query = sql_placeholder("SELECT * FROM `?#TBL_POLL_ANSWER` WHERE `pollId`=? ORDER BY `id`", $pollId);
	 	
	 	return MySQL::fetchArray( $_query );
	 }
	 
	 public static function doAnswer($profileId, $pollId, $answerId){
	 	$_query = sql_placeholder("INSERT INTO `?#TBL_PROFILE_POLL_ANSWER` SET `profileId`=?, `pollId`=?, `answerId`=?", $profileId, $pollId, $answerId );
	 	
	 	return MySQL::affectedRows( $_query );	
	 }
	 
	 public static function getSingleToVote($profileId){
	 	$_query = sql_placeholder("
SELECT `p`.`id` 
FROM `?#TBL_POLL` AS `p`
LEFT JOIN `?#TBL_PROFILE_POLL_ANSWER` AS `ppa`
	ON( `p`.`id` = `ppa`.`pollId` AND `ppa`.`profileId` = ? )
	
WHERE `ppa`.`profileId` IS NULL
LIMIT 1
", $profileId);
	 	
	 	return MySQL::fetchField($_query);
	 }
	 
	 public static function vote($profileId, $pollId, $answerId){
	 	$_query = sql_placeholder('INSERT INTO `?#TBL_PROFILE_POLL_ANSWER` SET `profileId`=?, pollId=?, answerId=?', $profileId, $pollId, $answerId);

	 	return MySQL::affectedRows($_query);
	 }
	 
	 public static function getVoteCount($pollId, $answerId){
		$_query = sql_placeholder( "SELECT COUNT(*) FROM `?#TBL_PROFILE_POLL_ANSWER` WHERE `pollId`=? AND `answerId`=?", $pollId, $answerId );
		
		$count = MySQL::fetchField($_query);
		
	
		return ( !is_null($count) )? $count : 0;
	 }
	 
	 public static function getAllVoteCount($pollId){
		$_query = sql_placeholder( "
SELECT COUNT( `answerId` ) 
FROM `?#TBL_PROFILE_POLL_ANSWER`
WHERE `pollId`=?
", $pollId );
		$count = MySQL::fetchField($_query);
		 
		return ( !is_null($count) )? $count : 0;
	 }	 
	 
	 public static function getProfileAnswer($pollId, $profileId){
	 	$_query = sql_placeholder("SELECT * FROM `?#TBL_PROFILE_POLL_ANSWER` WHERE `pollId`=? AND `profileId`=?", $pollId, $profileId);
	 	
	 	return MySQL::fetchRow($_query);
	 }
}
?>