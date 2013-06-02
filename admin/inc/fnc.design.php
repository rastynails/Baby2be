<?php
function cycleTrClass( $classPrefix = 'tr_' )
{
	static $tr_num;
	
	if( $tr_num == 1 )
	{
		$tr_num = 2;
		return $classPrefix.$tr_num;
	}
	
	$tr_num = 1;
	return $classPrefix.$tr_num;
}
?>