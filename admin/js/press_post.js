
var category_list_height;

function expand_collapse_categories()
{
	var btn = document.getElementById('cat_exp_col_btn');
	
	if( document.getElementById('cat_exp_col_btn').className == 'collapse_btn' )
	{
		if( !category_list_height )
			category_list_height = document.getElementById('category_list').clientHeight ? document.getElementById('category_list').clientHeight : ( document.getElementById('category_list').scrollHeight - 20 );
		
		expand_cat_step();
	}
	else
	{
		document.getElementById('category_list_tr').style.display = '';
		collapse_cat_step();
	}
}

function expand_cat_step()
{
	var cat_list = document.getElementById('category_list');
	var height = cat_list.clientHeight ? cat_list.clientHeight : cat_list.scrollHeight;
	
	if( height > 10 )
	{
		var step = ( height > 30 ) ? 20 : 15;
		cat_list.style.height = ( height/3 )+'px';
		setTimeout( 'expand_cat_step()', 15 );
	}
	else
	{
		document.getElementById('category_list_tr').style.display = 'none';
		document.getElementById('cat_exp_col_btn').className = 'expand_btn';
	}
}

function collapse_cat_step()
{
	var cat_list = document.getElementById('category_list');
	var height = cat_list.clientHeight ? cat_list.clientHeight : cat_list.scrollHeight;
	
	if( height < category_list_height )
	{
		cat_list.style.height = ( height + (category_list_height/6) )+'px';
		
		setTimeout( 'collapse_cat_step()', 15 );
	}
	else
	{
		document.getElementById('cat_exp_col_btn').className = 'collapse_btn';
	}
}

var cursor = 0;

function wrapText( F, button, lft, rgt )
{
	var field = F.content;
	field.focus();
	
	// IE
	if( document.selection )
	{
		strSelection = document.selection.createRange().text;
		if(strSelection)
		{
			document.selection.createRange().text = lft + strSelection + rgt;
		}
		else
		{
			var sel = document.selection.createRange();
			document.selection.createRange().setEndPoint('StartToStart', sel);
			
			if( button.className == 'f_btn' )
			{
				sel.text = lft;
				button.className = 'f_btn_active';
			}
			else
			{
				sel.text = rgt;
				button.className = 'f_btn';
			}
		}
		return;
	}

	// Mozilla Firefox
	else
	{
		var selLength = field.textLength;
		var selStart = field.selectionStart;
		var selEnd = field.selectionEnd;
		if( selEnd==1 || selEnd==2 ) selEnd=selLength;
		var s1 = field.value.substring(0,selStart);
		var s2 = field.value.substring(selStart, selEnd);
		var s3 = field.value.substring(selEnd, selLength);
		
		if(s2)
		{
			field.value = s1 + lft + s2 + rgt + s3;
			cursor = ( selStart + (s2.length) + (lft.length) + (rgt.length) );
		}
		else
		{
			if( button.className == 'f_btn' )
			{
				s2 = lft;
				button.className = 'f_btn_active';
			}
			else
			{
				s2 = rgt;
				button.className = 'f_btn';
			}
			
			field.value = s1 + s2 + s3;
			cursor = ( selStart + (s2.length) );
		}

		field.selectionStart = field.selectionEnd = cursor;
		return;
	}
}

function AddTag( F, tag )
{
	var lft = '<'+tag+'>';
	var rgt = '</'+tag+'>';
	var button = eval('F.'+tag);
	
	wrapText( F, button, lft, rgt );
	return do_preview();
}

function AddPromptTag( F, tag, msg )
{
	var button = eval( 'F.'+tag );
	
	button.className = 'f_btn_active';
	
	var pr = prompt( msg, 'http://' );
	var txt = '['+tag+']'+pr+'[/'+tag+']';

	if( pr != null )
		wrapText( F, button, '', txt );

	button.className = 'f_btn';
	return do_preview();
}



function do_preview()
{
	var p_block = document.getElementById('preview_block');
	
	if( p_block.style.display != 'none' )
		p_block.innerHTML = txt2preview( document.getElementById('post_textarea').value );
	
	return;
}




function TextColor( color )
{
	var field = document.getElementById('post_textarea');
	var lft = '<font color="'+ color +'">';
	var rgt = '</font>';
	
	hide_palette( 'f_panel_colorbox' );
	
	field.focus();
	
	// IE
	if( document.selection )
	{
		strSelection = document.selection.createRange().text;
		if( strSelection )
		{
			document.selection.createRange().text = lft + strSelection + rgt;
		}
	}
	
	// Firefox
	else
	{
		var selLength = field.textLength;
		var selStart = field.selectionStart;
		var selEnd = field.selectionEnd;
		
		if( selEnd==1 || selEnd==2 ) selEnd=selLength;
		
		var s1 = field.value.substring(0,selStart);
		var s2 = field.value.substring(selStart, selEnd);
		var s3 = field.value.substring(selEnd, selLength);
		
		if( s2 )
		{
			field.value = s1 + lft + s2 + rgt + s3;
			cursor = ( selStart + (s2.length) + (lft.length) + (rgt.length) );
		}
		
		field.selectionStart = field.selectionEnd = cursor;
	}
	
	return do_preview();
}








function break_lines()
{
	var btn = document.getElementById('btn_nl2br');
	var nl2br = document.getElementById('nl2br');
	
	if( nl2br.value == '1' )
	{
		btn.className = 'f_btn';
		nl2br.value = '0';
	}
	else
	{
		btn.className = 'f_btn_active';
		nl2br.value = '1';
	}
	
	return do_preview();
}


function txt2preview( text )
{
	if( document.getElementById('nl2br').value )
		text = text.replace( /\n/g, '<br />' );
	
	text = text.replace( /<-page->/g, '<br clear="all" /><div class="page_break"><b>&lt; - page - &gt;</b></div>' );
	
	text = text.replace( /<left>/g, '<div style="text-align: left">' );
	text = text.replace( /<\/left>/g, '</div>' );
	
	text = text.replace( /<center\>/g, '<div style="text-align: center">' );
	text = text.replace( /<\/center>/g, '</div>' );
	
	return text + '<br clear="all" />';
}

function preview()
{
	var p_block = document.getElementById('preview_block');
	var textarea = document.getElementById('post_textarea');
	var btn = document.getElementById('btn_preview');
	
	if( p_block.style.display == 'none' )
	{
		p_block.innerHTML = txt2preview( textarea.value );
		
		textarea.onkeyup = function()
		{
			document.getElementById('preview_block').innerHTML = txt2preview( document.getElementById('post_textarea').value );
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





function AddTagMore(F)
{
	var tag = "<-page->";
	var field = F.content;
	
	field.focus();
	
	// IE
	if( document.selection )
	{
		var sel = document.selection.createRange();
		document.selection.createRange().setEndPoint('StartToStart', sel);
		
		sel.text = tag;
		
		return do_preview();
	}

	// Mozilla Firefox
	else
	{
		var selLength = field.textLength;
		var selStart = field.selectionStart;
		var selEnd = field.selectionEnd;
		
		if( selEnd==1 || selEnd==2 ) selEnd=selLength;
		
		var s1 = field.value.substring(0,selStart);
		var s2 = field.value.substring(selStart, selEnd);
		var s3 = field.value.substring(selEnd, selLength);
		
		if(s2)
			field.value = s1 + tag + s2 + s3;
		else
			field.value = s1 + tag + s3;
		
		cursor = ( selStart + (tag.length) );
		
		field.selectionStart = field.selectionEnd = cursor;
		
		return do_preview();
	}
}





var lf_opacity = {};
var lf_st = {};

function show_palette( palette_id )
{
	var block = document.getElementById(palette_id);
	var shadow = document.getElementById(palette_id+'_shadow');
	
	block.style.display = 'block';
	shadow.style.display = 'block';
	
	lf_opacity[palette_id] = 0;
	
	// Mozilla Firefox
	moz_show_palette( palette_id );
	
}

function moz_show_palette( palette_id )
{
	var block = document.getElementById( palette_id );
	var shadow = document.getElementById(palette_id+'_shadow');
	
	clearTimeout( lf_st[palette_id] );
	
	if( lf_opacity[palette_id] < 9 )
	{
		lf_st[palette_id] = setTimeout( 'moz_show_palette(\''+palette_id+'\')', 30 );
		
		lf_opacity[palette_id] ++;
		
		block.style.opacity = '0.'+lf_opacity[palette_id];
		shadow.style.opacity = '0.'+( lf_opacity[palette_id] / 2 );
	}
	else
	{
		block.style.opacity = '1';
		shadow.style.opacity = '0.5';
	}
}




function hide_palette( palette_id )
{
	// Mozilla Firefox
	moz_hide_palette( palette_id );
}

function moz_hide_palette( palette_id )
{
	var block = document.getElementById( palette_id );
	var shadow = document.getElementById( palette_id+'_shadow' );
	
	clearTimeout( lf_st[palette_id] );
	
	if( lf_opacity[palette_id] > 0 )
	{
		lf_st[palette_id] = setTimeout( 'moz_hide_palette(\''+palette_id+'\')', 10 );
		
		lf_opacity[palette_id] --;
		
		block.style.opacity = '0.'+lf_opacity[palette_id];
		shadow.style.opacity = '0.'+( lf_opacity[palette_id] / 2 );
	}
	else
	{
		block.style.opacity = '0';
		shadow.style.opacity = '0';
		block.style.display = 'none';
		shadow.style.display = 'none';
	}
}

var image_popup;

function open_image_popup( post_id )
{
	image_popup = window.open('press_images.php?post_id='+post_id, 'press_image_popup', 'width=700,height=500,resizable=yes')
}

function open_href_popup()
{
	var field = document.getElementById('post_textarea');
	
	var link_text;
	
	field.focus();
	
	// IE
	if( document.selection )
	{
		link_text = document.selection.createRange().text;
	}
	
	// Firefox
	else
	{
		var selLength = field.textLength;
		var selStart = field.selectionStart;
		var selEnd = field.selectionEnd;
		
		if( selEnd==1 || selEnd==2 ) selEnd=selLength;
		
		link_text = field.value.substring(selStart, selEnd);
	}	
	
	href_popup = window.open('press_href.php?link_text='+link_text, 'press_href_popup', 'width=360,height=144,resizable=yes')
}

function check_post_form( cat_ids )
{
	if( !document.getElementById('post_title').value )
		return fail_alert( document.getElementById('post_title'), 'Please, enter post title' );
	if( !document.getElementById('post_textarea').value )
		return fail_alert( document.getElementById('post_textarea'), 'No post content' );
	
	var category_checked = false;
	
	for( var i in cat_ids )
	{
		if( $( 'press_category_' + cat_ids[i] ).checked )
			category_checked = true;
	}
	
	if( !category_checked )
	{
		alert('At least one category must be checked');
		
		if( document.getElementById('cat_exp_col_btn').className == 'expand_btn' )
		{
			document.getElementById('category_list_tr').style.display = '';
			collapse_cat_step();
		}
		
		return false;
	}
}



