
function CheckForm_create_template_set()
{
	if ( !$('set_name').value.trim() )
		return fail_alert( $('set_name'), 'Ads set name missing' );
	
	// TODO check if any template has been checked
	
		
	return true;
}

function CheckForm_edit_template_set( template_id )
{
	if ( !$('set_name').value.trim() )
		return fail_alert( $('set_name'), 'Ads set name missing' );
		
	//TODO if any template has been checked
		
	return true;
}

function ConfirmForm_delete_template_set( name )
{
	return confirm( 'Delete '+name+' template set?' );
}


