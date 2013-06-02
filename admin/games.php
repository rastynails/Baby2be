<?php
$file_key = 'games';
$active_tab = 'games';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'class.admin_games.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( @$_POST['create'] )
{
	switch ( adminGames::createGame( @$_POST['games_name'], @$_POST['games_code'], @$_POST['games_description'] ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Game name must be entered', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Game code must be entered', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Game with this name already exists', 'error' );
			break;
		default:
			$frontend->RegisterMessage( 'Game has been added' );
			break;
	}		
}

if ( @$_POST['delete'] )
{
	if ( adminGames::deleteGame( @$_POST['game_id'] ) )
		$frontend->RegisterMessage( 'Game has been deleted' );
	else 
		$frontend->RegisterMessage( 'Game has not been deleted', 'notice' );
}

if ( @$_POST['save'] )
{
    
	switch ( adminGames::saveGame( @$_POST['game_id'], @$_POST['games_name'], @$_POST['games_code'], @$_POST['games_description'] ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Undefined game', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Game name must be entered', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Game code must be entered', 'error' );
			break;
		case -4:
			$frontend->RegisterMessage( 'Game with this name already exists', 'notice' );
			break;
		case 0:
			$frontend->RegisterMessage( 'Game has not been changed', 'notice' );
			break;
		default:
			$frontend->RegisterMessage( 'Game has been changed' );
			break;
	}
}

if( @$_POST['set_novel_games'] )
{
    if(adminGames::setNovelGameStatus($_POST['novel_game']))
        $frontend->RegisterMessage( 'Novel games status has been changed' );
}

if ( @$_POST )
	redirect( $_SERVER['REQUEST_URI'] );

$all_games = adminGames::getAllGames();
$frontend->assign_by_ref( 'all_games', $all_games);

$all_novel_games = adminGames::getAllNovelGames();
$frontend->assign_by_ref( 'all_novel_games', $all_novel_games);

$template = 'games.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'games.js' );

$_page['title'] = "Games";

// display template
$frontend->display( $template );
?>
