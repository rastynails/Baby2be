<?php

function smarty_function_smileset( $params, SK_Layout $Layout )
{
	if ( !@$params['for'] ) {
		$Layout->trigger_error('missing param `for`', E_USER_WARNING);
		return;
	}
	
	// including js file
	SK_Layout::frontend_handler()->include_js_file(URL_STATIC.'jquery.selection.js');
	SK_Layout::frontend_handler()->include_js_file(URL_STATIC.'smileset.js');	
	
	$target_auto_id = $Layout->current_component()->getTagAutoId( $params['for'] );
	
	SK_Layout::frontend_handler()->onload_js("new smileSet('$target_auto_id');");							 
		
	$smiles = app_TextService::stGetSmiles();
	$smile_interface = implode($smiles);	
	
	// rendering html output
	return
'<div id="cs-' . $target_auto_id . '">
	<span class="text_formatter">
		<a href="javascript://" title="emoticon" class="chat_smile chat_control"></a>
	</span>	
	
	<div style="position: relative;">
		<div class="add_smile_block" style="display: none;">
			' . $smile_interface . '
		</div>
	</div>
</div>';
}
