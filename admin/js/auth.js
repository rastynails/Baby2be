function checkLoginForm()
{
	if ( !$( 'username' ).value.trim() )
		return fail_alert( $( 'username' ), 'Missing username' );
		
	if ( !$( 'password' ).value.trim() )
		return fail_alert( $( 'password' ), 'Missing password' );
		
	return true;
}

function checkRecoveryForm()
{
	if ( !$( 'email' ).value.trim() )
		return fail_alert( $( 'email' ), 'Email missing' );
		
	return true;
}
