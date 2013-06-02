/* Admin Serrvices JS file */

function manageAutoCropAccess( checked )
{
	$( 'autocrop_password' ).disabled = ( checked ) ? false : true;
	$( 'autocrop_username' ).disabled = ( checked ) ? false : true;
}