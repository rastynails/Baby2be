/* -- Finance JS File -- */

function CheckForm()
{
	if ( $jq("#date").attr('checked') )
	{
		var reg = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/;
		if( !reg.test( $jq("#date_from").val() ) && $jq("#date_from").val() != '' )
		{
			alert( 'Date format: YYYY-mm-dd. Please correct.' );
			$jq("#date_from").select();
			return false;
		}
		if( !reg.test( $jq("#date_to").val() ) && $jq("#date_to").val() != '' )
		{
			alert( 'Date format: YYYY-mm-dd. Please correct.' );
			$jq("#date_to").select();
			return false;
		}
		else if ( $jq("#date_to").val() == '' || $jq("#date_to").val() > '2039' )
		{
			now = new Date();
			var str;
			date = now.getDate();
			month = now.getMonth()+1;
			year = now.getYear();
			str = ( 1900 + year ) + ( ( month < 10 ) ? '-0' : '-') + month + ( ( date < 10 ) ? '-0' : '-' ) + date;
			$jq("#date_to").attr('value', str);
		}
	}
	
	
	if ( $jq("#profile").attr('checked') )
	{
		if ( $jq("#profile_name").val() == '' )
		{
			alert( 'Enter member\'s name or email.' );
			$jq("#profile_name").select();
			return false;
		}
	}

	if ( $jq("#order").attr('checked') )
	{
		if ( $jq("#order_number").val() == '' )
		{
			alert( 'Enter payment provider order number.' );
			$jq("#order_number").select();
			return false;
		}
	}

	return true;
}
