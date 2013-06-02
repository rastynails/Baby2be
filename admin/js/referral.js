
function checkIsDecimal( obj, error_message )
{
	var reg = /^[0-9]{1,8}\.*[0-9]{0,2}$/;
	if ( !reg.test( document.getElementById( obj ).value ) )
	{
		alert( error_message );
		document.getElementById( obj ).select();
		return false;
	}

	return true;
}

function checkIsInteger( obj, error_message )
{
	var reg = /^[0-9]{1,10}$/;
	if ( !reg.test( document.getElementById( obj ).value ) )
	{
		alert( error_message );
		document.getElementById( obj ).select();
		return false;
	}

	return true;
}

function validateReferralConfigureForm( F )
{
	for( var i in F.elements )
	{
		if (typeof(F.elements[i]) != 'object' || !F.elements[i].name || F.elements[i].type != 'text')
			continue;

		if (!checkIsInteger( F.elements[i].id, 'Please enter integer number.' ))
		{
			F.elements[i].select();
			return false;
		}
	}
	
	return true;
}