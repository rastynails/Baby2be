<?php

class component_VideoEdit extends SK_Component
{
	private $video_id;
	
	private $hash;
	
    public function __construct( array $params = null )
    {
        if (isset($params['video_id'])) {
            $this->video_id = $params['video_id'];
        }

        parent::__construct('video_edit');
    }
    
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        if ( !$this->video_id )
            SK_HttpRequest::showFalsePage();
        
        // check owner
        if (app_ProfileVideo::getVideoOwnerById($this->video_id) != SK_HttpUser::profile_id())
            SK_HttpRequest::showFalsePage();
        
        $this->hash = app_ProfileVideo::getVideoHash($this->video_id);

        $this->frontend_handler = new SK_ComponentFrontendHandler('VideoEdit');
        
        $this->frontend_handler->construct();
    }
    
    public static function clearCompile($tpl_file = null, $compile_id = null) {
        $cmp = new self;
        return $cmp->clear_compiled_tpl();
    }

	public function render( SK_Layout $Layout )
	{
		$tags_enabled = app_Features::isAvailable(17);
		
		if ($tags_enabled)		
			$Layout->assign( 'tags_cmp', new component_TagEdit( array( 'entity_id' => $this->video_id, 'feature' => 'video' ) ) );
		else 	
			$Layout->assign( 'tags_cmp', false );
		
		$Layout->assign('hash', $this->hash);
		
		$Layout->assign('enable_categories', SK_Config::section('video')->Section('other_settings')->get('enable_categories'));
		
        $Layout->assign('video', app_ProfileVideo::getVideoInfo(SK_HttpUser::profile_id(), $this->hash));
		
		return parent::render($Layout);
	}
	
	
	public function handleForm(SK_Form $form)
	{
		$video = app_ProfileVideo::getVideoInfo(SK_HttpUser::profile_id(), $this->hash);
		
		$form->getField('hash')->setValue($this->hash);
		$form->getField('title')->setValue($video['title']);
		$form->getField('description')->setValue($video['description']);
		$form->getField('privacy_status')->setValue($video['privacy_status']);
		if ( in_array($video['category_id'], app_VideoList::getVideoCategories(true)) )
		{
            $form->getField('category')->setValue($video['category_id']);
		}
                $form->getField('password')->setValue($video['password']);
                $form->getField('profile_id')->setValue($video['profile_id']);
	}
	
}
