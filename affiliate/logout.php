<?php
$file_key = 'logout';

require_once( '../internals/Header.inc.php' );
require_once( DIR_AFFILIATE_INC.'class.affiliate_frontend.php' );

require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );


$frontend = new AffiliateFrontend();

if ( LogoutAffiliate() )
	$frontend->registerMessage( 'You have successfully logged out from your affiliate space.' );

SK_HttpRequest::redirect(SITE_URL);

?>