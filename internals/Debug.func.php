<?php

/**
 * Prints or returns a mixed var.
 *
 * @param mixed $var
 * @param boolean $return
 * @return string [optional]
 */
function printArr( $var, $return = false )
{
	ob_start();
	var_dump($var);
	$_var_dump = ob_get_clean();
	
	$out = '
<div style="
	margin:10px 2px;
	border:1px inset #efefef;
	padding:10px;
	background: #efefef;
	color: #0000E0;
	font-family: \'Courier New\';
	font-size: 12px;
	text-align: left;
	">'.str_replace('  ', ' &nbsp;', nl2br(
		htmlspecialchars($_var_dump)
	)).'</div>';
	
	if ( $return )
		return $out;
	
	else
		echo $out;
}

function printArr2( $var, $return = false )
{
	ob_start();
	var_dump($var);
	$_var_dump = ob_get_clean();
	
	$out = '
<div style="
	margin:10px 2px;
	border:1px inset #efefef;
	padding:10px;
	background: #efefef;
	color: #CF0000;
	font-family: \'Courier New\';
	font-size: 12px;
	text-align: left;
	">'.str_replace('  ', ' &nbsp;', nl2br(
		htmlspecialchars($_var_dump)
	)).'</div>';
	
	if ( $return )
		return $out;
	
	else
		echo $out;
}

function printArr3( $var, $return = false )
{
	ob_start();
	var_dump($var);
	$_var_dump = ob_get_clean();
	
	$out = '
<div style="
	margin:10px 2px;
	border:1px inset #efefef;
	padding:10px;
	background: #efefef;
	color: #006F00;
	font-family: \'Courier New\';
	font-size: 12px;
	text-align: left;
	">'.str_replace('  ', ' &nbsp;', nl2br(
		htmlspecialchars($_var_dump)
	)).'</div>';
	
	if ( $return )
		return $out;
	
	else
		echo $out;
}

function printArr4( $var, $return = false )
{
	ob_start();
	var_dump($var);
	$_var_dump = ob_get_clean();
	
	$out = '
<div style="
	margin:10px 2px;
	border:1px inset #efefef;
	padding:10px;
	background: #000000;
	color: #4ECF00;
	font-family: \'Courier New\';
	font-size: 12px;
	text-align: left;
	">'.str_replace('  ', ' &nbsp;', nl2br(
		htmlspecialchars($_var_dump)
	)).'</div>';
	
	if ( $return )
		return $out;
	
	else
		echo $out;
}

function print_arr( $var, $return = false )
{
	$type = gettype( $var );
	
	$out = print_r( $var, true );
	$out = htmlspecialchars( $out );
	$out = str_replace('  ', '&nbsp; ', $out );
	$out = '<div style="
		border:2px inset #666;
		background:black;
		font-family:Verdana;
		font-size:11px;
		color:#6F6;
		text-align:left;
		margin:20px;
		padding:16px">
			<span style="color: #F66">('.$type.')</span> '.nl2br( $out ).'</div><br /><br />';
	
	if( !$return )
		echo $out;
	else
		return $out;
	
}

function pv( $var, $renturn = false )
{
	printArr( $var, $renturn );
}

function pve( $var, $renturn = false )
{
	print_arr( $var, $renturn ); 
	exit;
}

class DebugException extends Exception
{
	public function __construct( $expression, $code = 0 )
	{
		ob_start();
		var_dump($expression);
		$var_dump = ob_get_clean();
		
		parent::__construct("Debug: $var_dump", $code);
	}
}

/**
 * Keeps a debug microtimers data.
 *
 * @param mixed $timer_id
 * @param float $microtime to register a new microtimer
 * @return float
 */
function _microtimesKeeper( $timer_id, $start = false )
{
	static $timers_data = array();
	
	list( $msec, $sec ) = explode(" ", microtime());
	$_microtime = ( (float)$msec + (float)$sec );
	
	if ( !$start ) {
		return $_microtime - $timers_data[$timer_id];
	}
	
	$timers_data[$timer_id] = $_microtime;
}


/**
 * Starts a new microtimer.
 *
 * @param string $timet_id
 */
function startMicrotime( $timet_id = null )
{
	_microtimesKeeper($timet_id, true);
}

/**
 * Prints an idintified microtimer data.
 *
 * @param string $timer_id
 */
function printMicrotime( $timer_id = null, $return = false )
{
	if ( $return ) {
		return _microtimesKeeper($timer_id);
	}
	
	printArr(
		'['.$timer_id.']: ' . sprintf('%.5f', _microtimesKeeper( $timer_id ))
	);
}
