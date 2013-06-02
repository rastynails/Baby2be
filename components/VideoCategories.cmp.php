<?php

class component_Videocategories extends SK_Component
{    
    public function __construct( array $params = null )
    {
        if ( !SK_Config::section('video')->Section('other_settings')->get('enable_categories') )
        {
            $this->annul();
        }
        
        parent::__construct('video_categories');
    }
    
    public function render( SK_Layout $Layout )
    {
        $categories = app_VideoList::getVideoCategoriesWithCount(false);
        $Layout->assign('categories', $categories);

        if ( isset($_GET['cat_id']) )
        {
            $Layout->assign('active', (int)$_GET['cat_id']);
        }
        
        return parent::render($Layout);
    }
}