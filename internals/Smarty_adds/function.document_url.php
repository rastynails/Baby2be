<?php

function smarty_function_document_url(  array $params, SK_Layout $Layout )
{
	
	if(isset($params['doc_key']) && ($document_key = trim($params['doc_key'])) ){
		unset($params['doc_key']);
	}
	else 
		return '';
	
	$query_params = array();
		
	if(isset($params['query']) && ($query = trim($params['query'])) ){
		parse_str($query, $query_params);
		unset($params['query']);
	}
	foreach ($params as $key => $item)
		$query_params[$key] = $item;
	
	
	return SK_Navigation::href($document_key, $query_params);
}

