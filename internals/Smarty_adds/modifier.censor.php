<?php
//require_once DIR_APPS.'TextService.app.php';

function smarty_modifier_censor( $text, $feature, $replacement = null )
{ 
	return app_TextService::stCensor( $text, $feature, $replacement );
}