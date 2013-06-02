<?php
require_once DIR_API.'I18n.class.php';

function smarty_modifier_smile( $string )
{
	return SK_I18n::getHandleSmile( $string );
}