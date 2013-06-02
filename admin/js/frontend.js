/* --- Admin Frontend JS File --- */

function hightlight( obj_block, light )
{
	if( light )
	{
		obj_block.style.color='#2f2f26';
		obj_block.style.backgroundColor='#ffee9f';
	}
	else
	{
		obj_block.style.color='';
		obj_block.style.backgroundColor='';
	}
}

function page_message( msg ,type )
{
	if(typeof(type)=='undefined') type='message';
	class_name = 'page_'+type;
	if($jq('#content .'+class_name).is('div'))
		$jq('#content .'+class_name).text(msg);
	else
	{
		$jq('#content').prepend("<div class='"+class_name+"' style='display:none'>"+msg+"</div>");
		$jq('#content .'+class_name).fadeIn('fast');
	}
}

function $(obj)
{
	return document.getElementById( obj );
}

String.prototype.trim = function()
{
     return this.replace( /^\s+|\s+$/g, "" );
}

function fail_alert( field, message )
{
	alert( message );
	if($jq(field).css('display') == 'none')
		$jq(field).css('display','inline');
	field.focus();
	
	return false;
}

if( 'undefined' == typeof String.prototype.trim )
{
	String.prototype.trim = function()
	{
		return this.replace( /^\s+/, '' ).replace( /\s+$/, '' );
	}
}

function str2lower( value )
{
	rExp	= /[^\w]/gi;
	low_value = value.toLowerCase();	
	return low_value.replace( rExp, "_" );
}

function in_array( needle, haystack )
{
	for( var i in haystack )
	{
		if( haystack[i] == needle )
			return true;
	}
	
	return false;
}
