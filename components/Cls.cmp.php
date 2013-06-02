<?php

class component_Cls extends SK_Component
{
	public function __construct( array $params = null )
	{
	    if ( !app_Features::isAvailable(45) )
	    {
            SK_HttpRequest::showFalsePage();
	    }
	       
		parent::__construct('cls');
	}
	
	
	public function render( SK_Layout $Layout )
    {
        $search = !empty($_GET['search']) ? strip_tags($_GET['search']) : null;
        if ( $search )
        {
            $Layout->assign('searchResCmp', new component_ClsSearchResultList(array('keyword' => $search)));
        }

        $Layout->assign('search', $search);

		return parent::render($Layout);
	}
}
