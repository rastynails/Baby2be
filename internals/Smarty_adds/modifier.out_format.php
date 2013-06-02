<?php
function smarty_modifier_out_format( $text, $feature = false, $nl2br = true )
{
	return app_TextService::stOutputFormatter( $text, $feature, $nl2br );
}