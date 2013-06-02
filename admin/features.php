<?php

$file_key = 'features';

$dating = isset($_GET['dating']);

$active_tab = $dating ? 'dating' : 'features';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );



$membership = new AdminMembership();

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

$template = 'features.html';

//Get and adapt input data
if ( @$_POST['feature'] )
{
	switch( $membership->UpdateMembershipFeatureList( @$_POST['feature'], $dating ) )
	{
		case 1:
			$frontend->RegisterMessage( 'Features set has been updated' );
			
			if ( $dating )
			{
    			$advancedSearch = in_array(2, $_POST['feature']);
    			
    			if ( $advancedSearch )
    			{
    			    adminNavigation::enableDocumentMenuItems('profile_search');
    			}
    			else
    			{
    			    adminNavigation::disableDocumentMenuItems('profile_search');
    			}
    			
    			if ( in_array(51, $_POST['feature']) )
    			{
    			    adminNavigation::enableDocumentMenuItems('match_list');
    			}
    			else
    			{
    			    adminNavigation::disableDocumentMenuItems('match_list');
    			}
			}
			
			break;
		case -1:
		case 0:
			$frontend->RegisterMessage( 'Update failed', 'error' );
	}
	
	redirect( sk_make_url() );
}


// Get list of the features:
$frontend->assign( 'features', $membership->GetMembershipFeatureList($dating) );


$_page['title'] = "Features";

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

$frontend->display( $template );



?>
