<?php

class component_RssWidget extends SK_Component
{
    /**
     * @var integer
     */
    private $profile_id;
    
    /**
     * @var integer
     */
    private $cmp_id;
    
    /**
     * @var boolean
     */
    private $ownerMode;
    
    private $widgetDto;
    
    private $settings;
    
    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct( array $params = null )
    {
        parent::__construct( 'rss_widget' );
        
        $this->profile_id = (int)$params['profile_id'];
        $this->cmp_id = (int)$params['cmp_id'];
        
        $this->ownerMode = ( $params['profile_id'] == SK_HttpUser::profile_id() ) ? true : false;
        
        $this->settings = new stdClass();
        $this->settings->title = SK_Language::text('components.rss_widget.title');
        $this->settings->count = 10;
        $this->settings->showDesc = true;
    }
    
    /**
     * @see SK_Component::prepare()
     *
     * @param SK_Layout $Layout
     * @param SK_Frontend $Frontend
     */
    public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        if ($this->ownerMode)
        {
            $this->frontend_handler = new SK_ComponentFrontendHandler('RssWidget');
            $this->frontend_handler->construct();
        }
    }
    
    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     * @return unknown
     */
    public function render ( SK_Layout $Layout ) 
    {
        $service = new SK_Service('profile_rss', $this->profile_id );
        $permission = $service->checkPermissions() == SK_Service::SERVICE_FULL 
            ? false
            : $service->permission_message;
            
        if ($permission !== false && !$this->ownerMode)
        {
            return false;
        }
        
        $Layout->assign('permission', $permission['messages']);
        
        $this->widgetDto = app_RssWidget::find($this->cmp_id);
        
        $rss = array();
        if ($this->widgetDto)
        {
            $this->settings->title = $this->widgetDto->title;
            $this->settings->showDesc = $this->widgetDto->showDesc;
            $this->settings->count = $this->widgetDto->count;
            $this->settings->url = $this->widgetDto->url;
            
            $rss = app_RssWidget::getRss($this->cmp_id, $this->widgetDto->count);
            $rss = empty($rss) ? array() : $rss;
        }            
        
        $Layout->assign('rss', $rss);
        
        $Layout->assign('settings', $this->settings);
        $Layout->assign('owner', $this->ownerMode);
        
        return parent::render( $Layout );
    }
    
    /**
     * @see SK_Component::handleForm()
     *
     * @param SK_Form $form
     */
    public function handleForm( SK_Form $form )
    {
        if (!empty($this->settings->url))
        {
            try 
            {
                $form->getField('url')->setValue($this->settings->url);
            }
            catch (SK_FormFieldValidationException $e) {}
        }
                    
        $form->getField('count')->setValue($this->settings->count);
        $form->getField('title')->setValue($this->settings->title);
        $form->getField('showDesc')->setValue($this->settings->showDesc);

        $form->getField('cmp_id')->setValue($this->cmp_id);
        
        $onSubmitParams = json_encode(array(
            'cmp_id' => $this->cmp_id,
            'profile_id' => $this->profile_id
        ));
        
        $form->frontend_handler->bind("success", "function(data) {
            this.ownerComponent.onSubmit($onSubmitParams);
        }");
    }
}