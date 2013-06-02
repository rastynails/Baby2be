
function checkAll( F, mark, omit_ids )
{
	for ( var i = 0; i < F.elements.length; i++ )
		if( F.elements[i] != null && F.elements[i].type == 'checkbox' && !in_array( F.elements[i].id, omit_ids ) )
				F.elements[i].checked = mark;
}

function checked_radiocheckbox( field )
{
	for( var i = 0; i < field.length; i++ )
	{	
		if( field[i].checked )
			return true;
	}
	
	
	return false;
}

function checked_multicheckbox( field )
{
	if ( !field.length && field.name && field.checked )
		return true;
	
	for (var i = 0; i < field.length; i++ )
	{
		if ( field[i].checked )
			return (true);
	}
	return false;
}

function checked_multiselect( field )
{
	for (var i = 0; i < field.length; i++ )
	{
		if ( field[i].selected )
			return (true);
	}
	return false;
}

function checked_select( field )
{
	return ( field.value > 0 ) ? true : false; 
}