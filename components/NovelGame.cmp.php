<?php

class component_NovelGame extends SK_Component
{
    private $game;
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
        $this->game = app_Games::getNovelGameById($_GET['game_id']);

        if (empty($this->game) || !$this->game['is_enabled'])
            SK_HttpRequest::showFalsePage();


		parent::__construct('game');
	}
	
	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();
		SK_Navigation::addBreadCrumbItem($this->game['name']);

        SK_Language::defineGlobal( array('gametitle' => $this->game['name']) );

        $service = new SK_Service( 'play_games', SK_httpUser::profile_id() );
        if (SK_HttpUser::isModerator(SK_httpUser::profile_id()) || $service->checkPermissions() == SK_Service::SERVICE_FULL )
        {
                $playerID = SK_HttpUser::profile_id();
                $playerName = preg_replace('/(\s)+/', '+', app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'username'));

                $novelgames_gameURL = 'inc/'.$this->game['filepath'];
                $novelgames_rightClickEnabled = (bool)$this->game['rightClickEnabled'] ? 'true' : 'false';
                
$this->game['code'] =
<<<EOT
<script language="JavaScript" type="text/JavaScript">
	novelgames_gameURL = '{$novelgames_gameURL}';
    playerID = {$playerID};
    playerName = '{$playerName}';
    
	// DO NOT enable right click for games that do not need it, because:
	// 1) the game will run slower
	// 2) if you press the arrow keys in IE, the page will scroll
	novelgames_rightClickEnabled = {$novelgames_rightClickEnabled};
</script>
<script language="JavaScript" type="text/JavaScript" src="inc/game.js"></script>
EOT;
                $service->trackServiceUse();
                $Layout->assign('game', $this->game);

        }
        else
        {
                $Layout->assign( 'err_message', $service->permission_message['message'] );
        }
			
		return parent::render( $Layout );
	}
}