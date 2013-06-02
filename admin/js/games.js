
function CheckForm_create_game()
{
	if ( !$('games_name').value.trim() )
		return fail_alert( $('games_name'), 'Game name missing' );
		
	if ( !$( 'games_code' ).value.trim() )
		return fail_alert( $('games_code'), 'Game code missing' );
		
	return true;
}

function CheckForm_edit_game( game_id )
{
	if ( !$('games_name_'+game_id).value.trim() )
		return fail_alert( $('games_name_'+game_id), 'Game name missing' );
		
	if ( !$( 'games_code_'+game_id ).value.trim() )
		return fail_alert( $('games_code_'+game_id), 'Game code missing' );
		
	return true;
}

function ConfirmForm_delete_game( name )
{
	return confirm( 'Delete '+name+' game?' );
}
