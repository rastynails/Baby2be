
function deleteCategory( id )
{
	if ( confirm("Are you sure you want to delete this category?") )
	{
		window.location.href = "?del_cat_id=" + id;
	}
	else
	{
		return false;
	}
}

function editCategory( id )
{
	var $edit_cont = $jq("#edit_container");
	var title = $jq("#cat_title-"+id).html();
		
	$edit_cont.find("input[name=cat_title]").val(title);
	$edit_cont.find("input[name=cat_id]").val(id);
	
	window.cat_edit_fb = new SK_FloatBox({
		$title		: "Edit category",
		$contents	: $edit_cont.children()
	});		
}

function deleteTemplate( id )
{
	if ( confirm("Are you sure you want to delete this template?") )
	{
		window.location.href = "?del_tpl_id=" + id;
	}
	else
	{
		return false;
	}
}

function editTemplate( id )
{
	var $edit_cont = $jq("#edit_cont-" + id);
	
	window.cat_edit_fb = new SK_FloatBox({
		$title		: "Edit template",
		$contents	: $edit_cont.children()
	});		
}

function selectCategory( select, url )
{
	var cat_id = $jq(select).val();
	
	if ( cat_id )
	{
		window.location.href = url + "?cat_id=" + cat_id;
	}
	else
	{
		window.location.href = url;
	}
}
