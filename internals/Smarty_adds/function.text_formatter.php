<?php

function smarty_function_text_formatter( $params, SK_Layout $Layout )
{
	if ( !@$params['for'] ) {
		$Layout->trigger_error('missing param `for`', E_USER_WARNING);
		return;
	}
	
    if ( !@$params['entity'] ) {
        $Layout->trigger_error('missing param `entity`', E_USER_WARNING);
        return;
    }
	
	$controls = empty($params['controls']) ? null : array_map('trim', explode(',', $params['controls']));
	
	// getting current form
    $form = SK_Layout::getInstance()->get_template_vars('form');
    // getting target input auto-id
    $targetAutoId = $form->getTagAutoId($params['for']);
    
    $textFormatter = new component_TextFormatter($targetAutoId, $params['entity'], $controls);
    
	return SK_Layout::getInstance()->renderComponent($textFormatter, 'tf-' . $targetAutoId);
}
