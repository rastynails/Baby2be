var check_field = function()
{
	if ( $('username_field').value.trim().length > 0 )
		$('add_submit').disabled = false;
	else
		$('add_submit').disabled = true;
}

var check_form = function()
{
	if( !$('username_field').value.trim() )
	{
		alert( 'Please enter username' );
		$('username_field').focus();
		return false;
	}
	
	return true;
}

var checkbox_onclick = function( input )
{
	if( input.checked == true )
		$('ch_counter').value++;
	else
		$('ch_counter').value--;
	
	
	if ( $('ch_counter').value > 0 )
		$('action_submit').disabled = false;
	else
		$('action_submit').disabled = true;
	
}