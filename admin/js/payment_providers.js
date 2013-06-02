function showProviderFields( provider_id )
{
	if( $( 'provider_' + provider_id ).style.display == 'none' )
		$( 'provider_' + provider_id ).style.display = 'block';
	else
		$( 'provider_' + provider_id ).style.display = 'none';
}

function activateProvider( obj )
{
	if( obj.value != 'Activate')
	{
		obj.value = 'Activate';
		obj.form.status.value = 'inactive';
	}
	else
	{
		obj.value = 'Disable';
		obj.form.status.value = 'active';
	}
}