<?php

class httpdoc_TaggedPhotoList extends SK_HttpDocument
{
   
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('tagged_photo_list');
    }
    
    public function render( SK_Layout $layout )
    {
        $layout->assign('tagNavigator', new component_TagNavigator('photo', SK_Navigation::href('photos'), 50 ));
        
        return parent::render($layout);
    }
    
}