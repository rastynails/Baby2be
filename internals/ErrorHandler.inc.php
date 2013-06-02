<?php

if ( !defined('SK_ERROR_REPORTING') ) {
	define('SK_ERROR_REPORTING', DEV_MODE ? E_ALL : E_ALL & ~E_NOTICE & ~E_USER_NOTICE);
}

error_reporting(SK_ERROR_REPORTING);

function sk_exception_handler( Exception $exception )
{
	// ajax exception
	if ( SK_HttpRequest::isXMLHttpRequest() ) {
		$jsException = array(
			'class_name'	=>	get_class($exception),
			'code'			=>	$exception->getCode(),
			'message'		=>	$exception->getMessage(),
			'trace_str'		=>	$exception->getTraceAsString(),
			'file'			=>	$exception->getFile(),
			'line'			=>	$exception->getLine()
		);

		header('SK-Exception: '.json_encode($jsException), null, 500);
	}
	else {
		$output = "<div style=\"font-family:'Courier New'; font-size:12px\">\n";

		$output .= "Uncaught exception with message:\n<b>" . $exception->getMessage() . '</b> ';
		$output .= "code: <b>" . $exception->getCode() . "</b><br />\n";

		$output .= "Trace: <br />";
		$output .= "<div style=\"margin: 2px 6px\">\n" . nl2br($exception->getTraceAsString()) . "</div>\n";

		$output .= 'thrown in: <b>' . $exception->getFile() . '</b>';
		$output .= ' on line <b>' . $exception->getLine() . '</b><br />';

		$output .= '</div>';

		echo $output;
	}
}

set_exception_handler('sk_exception_handler');

/*
function sk_error_handler( $errno, $errstr, $errfile, $errline )
{
	if ( $errno & ~SK_ERROR_REPORTING ) {
		return true;
	}

	$errtype = array(
		E_ERROR              => 'Error',
		E_WARNING            => 'Warning',
		E_PARSE              => 'Parsing Error',
		E_NOTICE             => 'Notice',
		E_CORE_ERROR         => 'Core Error',
		E_CORE_WARNING       => 'Core Warning',
		E_COMPILE_ERROR      => 'Compile Error',
		E_COMPILE_WARNING    => 'Compile Warning',
		E_USER_ERROR         => 'User Error',
		E_USER_WARNING       => 'User Warning',
		E_USER_NOTICE        => 'User Notice',
		E_STRICT             => 'Runtime Notice',
		E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
	);

	// ajax error
	if ( SK_HttpRequest::isXMLHttpRequest() )
	{
		global $response;
		$response->addError("{$errtype[$errno]}: $errstr\nin: $errfile on line $errline");
	}
	else {
		$output = '<div style="font-family:Courier New;font-size:12px">';

		$output .= '<b>'.$errtype[$errno].':</b>' . " $errstr ";
		$output .= "in: <b>$errfile</b> on line <b>$errline</b><br />";

		$output .= '</div>';

		echo $output;
	}

	return true;
}

set_error_handler('sk_error_handler');
*/