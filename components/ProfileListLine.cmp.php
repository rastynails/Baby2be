<?php

class component_ProfileListLine extends SK_Component
{
    private $count = false;
    
    private $sex = null;
    
    public function __construct( array $params = null )
    {
        if ( !empty($params["count"]) ) {
            $this->count = $params["count"];
        }
        
        if ( !empty($params["sex"]) ) {
            $this->sex = $params["sex"];
        }
        
        parent::__construct('profile_list_line');
    }
    
    public function render( SK_Layout $Layout )
    {
        $items = app_ProfileList::findLatestList(0, $this->count, $this->sex);
        
        $out = array();
        foreach ( $items['profiles'] as $item) {
            $out[] = (int) $item['profile_id'];
        }
        
        $Layout->assign('items', $out);
        
        $Layout->assign('sex', $this->sex);
        
        $Layout->assign('viewAllUrl', $this->getViewUrl());
        
        return parent::render($Layout);
    }
    
    
    private function getViewUrl()
    {
        $params = array();
        if ( !empty($this->sex) )
        {
            $params['sex'] = $this->sex; 
        }
        
        return SK_Navigation::href('latest_members', $params);
    }
}
