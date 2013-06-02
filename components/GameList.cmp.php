<?php

class component_GameList extends SK_Component
{

	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('game_list');
	}
	
	public function render( SK_Layout $Layout )
	{
        
		$games = app_Games::getGameList();
        
		if ( count($games) > 0 ) {
	
			$Layout->assign('games', $games);
		} 
		else 
			$Layout->assign('no_games', true);

        $novel_games = app_Games::getNovelGameList();

		if ( count($novel_games) > 0 ) {

			$Layout->assign('novel_games', $novel_games);
		}
			
		return parent::render( $Layout );
	}
}