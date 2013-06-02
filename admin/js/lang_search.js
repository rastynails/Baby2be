
function checkLanguageSearch(F)
{
	var search_input = $jq("#lang_search_input");
	
	var by_key = $jq("#lang_search_keys_chbox_label input");
	var by_value = $jq("#lang_search_values_chbox_label input");
	
	if (!search_input.val()) {
		search_input.focus();
		return false;
	}
	
	if ( !by_key.attr('checked') && !by_value.attr('checked') ) {
		by_value.attr('checked', 'checked');
		
		return false;
	}
	
	return true;
}