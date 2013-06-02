<?php

function smarty_function_print_r( $params, SK_Layout $layout ) {
	return printArr($params['var'], true);
}
