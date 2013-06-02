
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

function checkIsCorrectEmail( obj, error_message )
{
	var reg = /^[a-zA-Z0-9_\-\.]+@([a-zA-Z0-9_\.-])+$/i
	if ( !reg.test( obj.value ) )
	{
		alert( error_message );
		obj.select();
		return false;
	}
	
	return true;
}

function checkAffiliateLoginForm( F )
{
	if ( !checkIsCorrectEmail( F.affiliate_email, 'Please correct your email address') )
		return false;

	if ( !F.affiliate_pass.value.length )
	{
		alert( 'Please enter your password.' );
		F.affiliate_pass.select();
		return false;
	}

	return true;
}


function ShowAffiliateInputFields( is_edit )
{
	field_arr = new Array( 'field_name','field_password','field_email','field_im','field_phone','field_address','field_payment_details','field_edit' );
	input_arr = new Array( 'input_name','input_password','input_email','input_im','input_phone','input_address','input_payment_details','input_btn','input_cancel' );

	var field = 'none';
	var input = 'block';
	if ( !is_edit )
	{
		field = 'block';
		input = 'none';
	}
	for (var i in field_arr)
		document.getElementById( field_arr[i] ).style.display = field;

	for (var i in input_arr)
		document.getElementById( input_arr[i] ).style.display = input;
}