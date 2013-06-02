
function checkDelete() 
{
	if (confirm("Do you really want to delete selected groups?")) 
	{	
		setAction('delete');
		//$jq('input[name=action]').attr('value','delete');
		$jq("#groups_form").submit();
		return true;
	}
	return false;
}

function setAction( action )
{
	$jq('input[name=action]').attr('value', action);
}

function checkSetStatus()
{
	setAction('set_status');
	$jq("#groups_form").submit();
	return true;
}