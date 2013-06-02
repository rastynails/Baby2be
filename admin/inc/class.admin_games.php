<?php
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );

/**
 * Project:    SkaDate reloaded
 * File:       class.admin_ads.php
 *
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */


/**
 * Class for config games on site
 *
 * @package SkaDate
 * @since 5.0
 * @author Denis J
 * @link http://www.skalinks.com/ca/forum/ Technical Support forum
 */
class adminGames
{
	public static function getAllGames()
	{
		$query = "SELECT * FROM `".TBL_GAME."` ORDER BY `game_id` DESC";
		return MySQL::fetchArray( $query, 'game_id' );
	}
	
	
	public static function createGame( &$name, &$code, &$description )
	{
		if ( !strlen( trim( $name ) ) )
			return -1;
			
		if ( !strlen( trim( $code ) ) )
			return -2;
			
		// detect if the same name exists
		$query = sql_placeholder( "SELECT `name` FROM `".TBL_GAME."` WHERE `name`=?", $name );
		if ( MySQL::fetchField( $query ) )
			return -3;
				
		$query = sql_placeholder( "INSERT INTO `".TBL_GAME."`( `name`, `code`, `description` )
		VALUES( ?@ )", array( $name, $code, $description ) );
			
		return MySQL::insertId( $query );
	}
	
	public static function deleteGame( &$game_id )
	{
		if ( !( int )$game_id )
			return false;
			
		$query = "DELETE FROM `".TBL_GAME."` WHERE `game_id`='$game_id'";
		MySQL::fetchResource( $query );
		
		return true;
	}
	
	
	
	public static function saveGame( $game_id, $name, $code, $description )
	{
		if ( !( int )$game_id )
			return -1;
			
		if ( !strlen( trim( $name ) ) )
			return -2;
			
		if ( !strlen( trim( $code ) ) )
			return -3;
			
		// check if the same name exists
		$query = sql_placeholder( "SELECT `name` FROM `".TBL_GAME."` WHERE `name`=? AND `game_id`!=?", $name, $game_id );
		if ( MySQL::fetchField( $query ) )
			return -4;
			
		// update row
		$query = sql_placeholder( "UPDATE `".TBL_GAME."` SET `name`=?, `code`=?, `description`=? WHERE `game_id`='$game_id'", $name, $code, $description );
		
		return MySQL::affectedRows( $query );
	}

    public static function getAllNovelGames()
	{
		$query = "SELECT * FROM `".TBL_NOVEL_GAME."` ORDER BY `name` ASC";
		return MySQL::fetchArray( $query );
	}

    public static function setNovelGameStatus( $games )
	{
        $query = "UPDATE `".TBL_NOVEL_GAME."` SET `is_enabled`=0";
        MySQL::affectedRows($query);
        
        foreach ($games as $id=>$status)
        {
            $query = MySQL::placeholder( "UPDATE `".TBL_NOVEL_GAME."` SET `is_enabled`=? WHERE `id`=?;", $status, $id );
            MySQL::affectedRows($query);
        }
        
        return true;
	}
	
}
?>
