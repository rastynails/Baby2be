<?php

class component_SearchResultCount extends SK_Component
{
    public function __construct()
    {
        parent::__construct('search_result_count');
        
        if ( !in_array(@$_GET['search_type'], array('main', 'username', 'keyword', 'quick_search')) )
        {
            $this->annul();
            return;
        }
    }

    public function render( SK_Layout $Layout )
    {        
        $values_arr = ( $_GET ) ? $_GET : $_COOKIE['search_preference'];
        $Layout->assign( 'count', app_ProfileSearch::generateResultList(app_ProfileSearch::getSearchType(), $values_arr, true));
        parent::render($Layout);
    }
}