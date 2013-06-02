<?php

/**
 * Completes a sentence by a dot symbol if it's need.
 *
 * @param string $text
 * @return string
 */
function smarty_modifier_dot( $text )
{
	$text = trim($text);
	$strlen = strlen($text);
	
	if ( !in_array($text{$strlen-1}, array('.', '!', '?') ) ) {
		$text .= '.';
	}
	
	return $text;
}
