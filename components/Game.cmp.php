<?php

class component_Game extends SK_Component
{
    private $game;
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
        $this->game = app_Games::getGameById($_GET['game_id']);

        if (empty($this->game))
            SK_HttpRequest::showFalsePage();

		parent::__construct('game');
	}

	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();
		SK_Navigation::addBreadCrumbItem($this->game['name']);

        SK_Language::defineGlobal( array('gametitle' => $this->game['name']) );

        $this->getDocumentMeta()->description = $this->game['description'];

        $service = new SK_Service( 'play_games', SK_httpUser::profile_id() );
        if (SK_HttpUser::isModerator(SK_httpUser::profile_id()) || $service->checkPermissions() == SK_Service::SERVICE_FULL )
        {
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