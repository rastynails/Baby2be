<?php

/**
 * Class implementing Games feature
 *
 */
class app_Games {

	/**
	 * Returns game list
	 *
	 * @return array
	 */
	public static function getGameList()
	{
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_GAME."` ORDER BY `create_timestamp` DESC ");

		return MySQL::fetchArray($query);
	}

    public static function getGameById( $game_id )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_GAME."` WHERE `game_id`=? ", $game_id);

		return MySQL::fetchRow($query);

    }

    public static function getNovelGameList()
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_NOVEL_GAME."` WHERE `is_enabled`=1 ORDER BY `name` ASC ");

		return MySQL::fetchArray($query);
    }

    public static function getNovelGameById( $game_id )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_NOVEL_GAME."` WHERE `id`=? ", $game_id);

		return MySQL::fetchRow($query);

    }
}
