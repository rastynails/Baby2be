<?php
class component_PhotoAuthenticate extends SK_Component
{
    /**
     * 
     * @var dto_PhotoAuthenticate
     */
    private $request;
    
    public function __construct( dto_PhotoAuthenticate $request )
    {
        parent::__construct('photo_authenticate');

        $this->request = $request;   
    }
    
    public function render( SK_Layout $layout )
    {
        $url = app_PhotoAuthenticate::isResponded($this->request)
            ? app_PhotoAuthenticate::getPhotoUrlByRequest($this->request) 
            : false;    
        
        $layout->assign('code', $this->request->code);
        $layout->assign('photoUrl', $url);
    }
    
}


