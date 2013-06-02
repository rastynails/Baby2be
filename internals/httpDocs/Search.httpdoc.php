<?php

class httpdoc_Search extends SK_HttpDocument
{
	public function __construct( array $params = null ) 
	{
		parent::__construct('search');
		
		if ( !app_Features::isAvailable(2) )
		{
		    SK_HttpRequest::showFalsePage();
		}
	}
	
}
