function ads_preview()
{
	var p_block = $('ads_preview');
	var textarea = $('ads_code');
	var btn = $('btn_preview');
	
	if( p_block.style.display == 'none' && textarea.value.trim() )
	{
		p_block.innerHTML = textarea.value;
		
		textarea.onkeyup = function()
		{
			$('ads_preview').innerHTML = $('ads_code').value ;
		}
		
		p_block.style.display = 'block';
		btn.className = 'f_btn_active';
	}
	
	else
	{
		textarea.onkeyup = null;
		p_block.style.display = 'none';
		btn.className = 'f_btn';
	}
	
	return textarea.focus();
}

function CheckForm_create_template()
{
	if ( !$('ads_name').value.trim() )
		return fail_alert( $('ads_name'), 'Ads name missing' );
		
	if ( !$( 'ads_code' ).value.trim() )
		return fail_alert( $('ads_code'), 'Ads code missing' );
		
	return true;
}

function CheckForm_edit_template( template_id )
{
	if ( !$('ads_name_'+template_id).value.trim() )
		return fail_alert( $('ads_name_'+template_id), 'Ads name missing' );
		
	if ( !$( 'ads_code_'+template_id ).value.trim() )
		return fail_alert( $('ads_code_'+template_id), 'Ads code missing' );
		
	return true;
}

function ConfirmForm_delete_template( name )
{
	return confirm( 'Delete '+name+' template?' );
}
