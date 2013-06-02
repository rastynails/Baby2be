<?php

class component_TotalMembersOnline extends SK_Component
{
    private $sex = null; 
    
    public function __construct( array $params = null )
    {
        parent::__construct('total_members_online');
        
        if ( !empty($params['sex']) )
        {
            $this->sex = $params['sex'];
        }
    }
    
    public function render( SK_Layout $Layout )
    {
        $sex = empty($this->sex) ? '' : SK_Language::text('profile_field.value.sex_' . $this->sex);
        $Layout->assign('sex', $sex);
        $Layout->assign('url', SK_Navigation::href("online_list"));
        
        $count = app_ProfileList::findOnlineCount($this->sex);
        $Layout->assign('count', $count);
        
        return parent::render($Layout);
    }
    
}
