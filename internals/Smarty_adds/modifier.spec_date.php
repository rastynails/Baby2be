<?php
require_once DIR_API.'I18n.class.php';

function smarty_modifier_spec_date( $time_stamp, $only_date = false )
{
	return SK_I18n::getSpecFormattedDate( $time_stamp, $only_date );
}