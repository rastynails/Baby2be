var node_opacity = {};
var node_st = {};

function show_node( node_id )
{
	var block = $( node_id );
	
	block.style.display = 'block';
	
	node_opacity[node_id] = 0;
	
	if (navigator.appVersion.indexOf('MSIE') != -1)
		ie_show_div( node_id );
	else
		moz_show_div( node_id )
}

function ie_show_div( node_id )
{
	var block = $( node_id );

	clearTimeout( node_st[node_id] );
	
	if( node_opacity[node_id] < 100 )
	{
		node_st[node_id] = setTimeout( 'ie_show_div(\''+node_id+'\')', 10 );
		
		node_opacity[node_id] +=30;
		
		block.style.filter = 'alpha(opacity='+node_opacity[node_id]+')';
	}
	else
		block.style.filter = 'alpha(opacity=100)';
}

function moz_show_div( node_id )
{
	var block = $( node_id );
	
	clearTimeout( node_st[node_id] );
	
	if( node_opacity[node_id] < 1 )
	{
		node_st[node_id] = setTimeout( 'moz_show_div(\''+node_id+'\')', 30 );
		
		node_opacity[node_id] +=0.1;
		
		block.style.opacity = node_opacity[node_id];
	}
	else
		block.style.opacity = '1';
}

function hide_node( node_id )
{
	if (navigator.appVersion.indexOf('MSIE') != -1)
		ie_hide_node( node_id )
	else
		moz_hide_node( node_id );
}

function moz_hide_node( node_id )
{
	var block = $( node_id );
		
	clearTimeout( node_st[node_id] );
	
	if( node_opacity[node_id] > 0 )
	{
		node_st[node_id] = setTimeout( 'moz_hide_node(\''+node_id+'\')', 30 );
		
		node_opacity[node_id] -=0.1;
		
		block.style.opacity = node_opacity[node_id];
	}
	else
	{
		block.style.opacity = '0';
		block.style.display = 'none';
	}
}

function ie_hide_node( node_id )
{
	var block = $( node_id );
		
	clearTimeout( node_st[node_id] );
	
	if( node_opacity[node_id] > 0 )
	{
		node_st[node_id] = setTimeout( 'ie_hide_node(\''+node_id+'\')', 10 );
		
		node_opacity[node_id] -=30;
		
		block.style.filter = 'alpha(opacity='+node_opacity[node_id]+')';
	}
	else
	{
		block.style.filter = 'alpha(opacity=0)';
		block.style.display = 'none';
	}
}