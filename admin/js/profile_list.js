
function checkSelected( form )
{
	if ( !$jq(form).find(".chbox_container input:checkbox:checked").length )
	{		
		alert('No profiles selected');
		
		return false;
	}
	
	return true;
}

function checkSendProfileMsgForm( form )
{
	if ( !checkSelected(form) )
	{
		return false;
	}
	
	if ( !$( 'msg_subject' ).value.trim() )	
		return fail_alert( $( 'msg_subject' ), 'Missing message subject' );
	
	if ( !$( 'msg_txt' ).value.trim() )	
		return fail_alert( $( 'msg_txt' ), 'Missing message text' );
	
	$jq("#action").val("send_msg");
	document.forms["profiles"].submit();
	return true;
}

function ValidateGiveMembershipFormNew( F )
{
	if ( !checkSelected(F) )
	{
		return false;
	}
	
	var check = /^[0-9\.]+$/;
	
	var foo = false;
	
	$jq.each( F.elements, function(index,data){
		if ( $jq(data).attr('type') == 'radio' && $jq(data).attr('checked') )
		{
			var $numbers = $('numbers[' + $jq(data).val() + ']' );
			if ( $numbers != undefined && !check.test($('numbers[' + $jq(data).val() + ']' ).value) )
			{
				fail_alert($('numbers[' + $jq(data).val() + ']'), 'Incorrect data. Please, check and try again.');
				foo = true;
				return false;
			}
		}
	});
	
	if ( foo )
	{
		return false;
	}
	
	$jq("#action").val("set_membership");
	document.forms["profiles"].submit();
	return true;
}

function checkSendVerifyMail( form )
{
	if ( !checkSelected(form) )
	{
		return false;
	}
	
	$jq("#action").val("send_verify_email");
	document.forms["profiles"].submit();
	return true;
}

function checkSetStatus( form )
{
	if ( !checkSelected(form) )
	{
		return false;
	}
	
	$jq("#action").val("set_status");
	document.forms["profiles"].submit();
	return true;
}

function confirmDelete( form )
{
	if ( !checkSelected(form) )
	{
		return false;
	}
	
	if ( !confirm('Do you really want to delete selected profiles?') )
	{
		return false;
	}
	
	$jq("#action").val("delete_profiles");
	document.forms["profiles"].submit();
	return true;	
}

function ValidateGiveMembershipForm( F )
{
	var check = /^[0-9\.]+$/;
	for( var i in F.elements )
	{
		if (typeof(F.elements[i]) != 'object' || !F.elements[i].name || F.elements[i].type != 'radio')
			continue;

		if ( $( 'numbers[' + F.elements[i].value + ']' ) && F.elements[i].checked)
			if ( !check.test( $( 'numbers[' + F.elements[i].value + ']' ).value ) )
			{
				fail_alert( $( 'numbers[' + F.elements[i].value + ']' ), 'Incorrect data. Please, check and try again.' );
				return false;
			}
	}
	
	return true;
}