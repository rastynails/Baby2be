function SetTextColor( color )
{
	document.getElementById( 'watermark_text_input_text' ).style.color=color;
	document.getElementById( 'txt_color' ).value = color;
}

function SetBackGroundColor( color )
{
	document.getElementById( 'watermark_text_input_text' ).style.backgroundColor=color;
	document.getElementById( 'bg_color' ).value = color;	
}

function CheckWaterMarkForm( F )
{
	for (var i = 0; i < F.watermark.length; i++ )
	{
		if ( F.watermark[i].checked )
			var checked = i;
	}
	
	switch ( F.watermark[checked].value )
	{
		case '1':
			if ( !F.txt.value.trim() )
			{
				return fail_alert( F.txt, 'Missing watermark text' );			
			}
			break;
		case '2':
		if ( !F.watermark_img.value && !F.img.value )
		{
			return fail_alert( F.watermark[checked], 'Browse for watermark image' );			
		}
		break;
	}	
	
	return true;
}

