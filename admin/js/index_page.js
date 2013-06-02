
function confirmPageReset( )
{
	if (confirm( 'Are you sure you want to reset your page template to default?\nYour page will have default markup' ))
	{
		$jq("#form_action").attr('value','reset_code');
		$jq("#markup_form").submit();
	}
}

function savePageCode()
{
	$jq("#form_action").attr('value', 'save_code');
	$jq("#markup_form").submit();
}
