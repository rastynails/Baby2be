
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

function validateAffiliateConfigureForm( F )
{
	for( var i in F.elements )
	{
		if (typeof(F.elements[i]) != 'object' || !F.elements[i].name || F.elements[i].type != 'text')
			continue;

		if (F.elements[i].id == 'period')
		{
			if (!checkIsInteger( F.elements[i].id, 'Period must be integer. Please correct.' ))
			{
				F.elements[i].select();
				return false;
			}
		}
		else
		{
			if (!checkIsDecimal( F.elements[i].id, 'Please enter decimal number.' ))
			{
				F.elements[i].select();
				return false;
			}
		}
	}
	
	return true;
}


function ShowAffiliateInputFields( is_edit )
{
	var field_arr = new Array( 'field_name','field_password','field_email','field_im','field_phone','field_address','field_payment_details','field_edit','field_delete','field_status' );
	var input_arr = new Array( 'input_name','input_password','input_email','input_im','input_phone','input_address','input_payment_details','input_btn','input_cancel','input_status' );

	var field = 'none';
	var input = 'block';
	if ( !is_edit )
	{
		field = 'block';
		input = 'none';
	}
	for (var i=0; i<field_arr.length; i++)
		document.getElementById( field_arr[i] ).style.display = field;

	for (var i=0; i<input_arr.length; i++)
		document.getElementById( input_arr[i] ).style.display = input;
}


function confirmDelete(href)
{
	if (confirm("Do you really want to delete affiliate?")) {
		window.location = href;
		return true;
	}
	
	return false;
}