<?php

function smarty_function_attachment( $params, SK_Layout $Layout )
{
    if ( !@$params['entity'] ) {
        $Layout->trigger_error('missing param `entity`', E_USER_WARNING);
        return;
    }
    
    if ( !@$params['entityId'] ) {
        $Layout->trigger_error('missing param `entityId`', E_USER_WARNING);
        return;
    }
    
    $cmp = new component_Attachment($params);
    
    return SK_Layout::getInstance()->renderComponent($cmp);
}
