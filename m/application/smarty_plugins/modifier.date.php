<?php

function smarty_modifier_date( $timestamp, $format = null )
{
	if ( !isset($format) ) {
		// TODO: get the default format from configs.
		$format = 'd/m/Y';
	}
	
	return date($format, $timestamp);
}
