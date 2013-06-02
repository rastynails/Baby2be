<?php
$file_key = 'profile_message';
$active_tab = 'keywords';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
//require_once( DIR_ADMIN_INC.'class.admin_language.php' );
//require_once( DIR_ADMIN_INC.'class.admin_profile.php' );

require_once( DIR_ADMIN_INC.'fnc.profile_message.php' );

//$language =& new AdminLanguage();
$frontend = new AdminFrontend( $language );


if( $_REQUEST['command'] )
{
	if( $_REQUEST['command'] == 'delete_keyword' && ($keyword_id = intval($_REQUEST['keyword_id'])) )
	{
		if( deleteMessageScamKeyword($keyword_id) )
			$frontend->registerMessage( 'Deleted' );
		else
			$frontend->registerMessage( 'Failed', 'error' );
	}
	elseif ( $_REQUEST['command'] == 'edit_keyword' && is_array($_REQUEST['keywords']) )
	{
		foreach ($_REQUEST['keywords'] as $keyword_id => $keyword )
			editMessageScamKeyword($keyword_id, $keyword);
		
		$frontend->registerMessage( 'Updated' );
	}
	elseif ( $_POST['command'] == 'add_keyword' && strlen($_POST['keyword']) )
	{
		if( addMessageScamKeyword($_POST['keyword']) )
			$frontend->registerMessage( 'Added' );
		else
			$frontend->registerMessage( 'Keyword already exists', 'notice' );
	}
	
	redirect( URL_ADMIN.'profile_message_keyword.php' );
}


$keywords = getSpamKeywords();

$frontend->assign_by_ref( 'keywords', $keywords );


require_once( 'inc.admin_menu.php' );

$_page['title'] = 'Spam Filter';

$frontend->display( 'profile_message_keyword.html' );
?>
