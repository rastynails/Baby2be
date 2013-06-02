<?php

class component_Report extends SK_Component
{
	private $reporter_id;
	
	private $entity_id;
	
	private $type; 
	
	private $show_link;

	/**
	 * Component Report constructor.
	 *
	 * @return component_Report
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('report');
		
		if ( !SK_Config::section('reports')->get('enable_report') ) 
			$this->annul();
		else {	
			$this->reporter_id = $params['reporter_id'];
			$this->entity_id = $params['entity_id'];
			$this->type = $params['type'];
			$this->show_link = $params['show_link'];
			
			if ($this->reporter_id == $this->entity_id && $this->type == 'profile')
				$this->annul();

            if ( $this->type == 'photo' )
            {
                $owner = app_ProfilePhoto::photoOwnerId($this->entity_id);
                if ( $owner == $this->reporter_id )
                    $this->annul();
            }

            if ( $this->type == 'video' )
            {
                $owner = app_ProfileVideo::getVideoOwnerById($this->entity_id);
                if ( $owner == $this->reporter_id )
                    $this->annul();
            }

			if ($this->type == 'classifieds' && $this->reporter_id == app_ClassifiedsItemService::stGetItemOwnerId( $this->entity_id ) ) {
				$this->annul();
			}

            if ( $this->type == 'blog' )
            {
                $post = app_BlogService::newInstance()->findBlogPostById($this->entity_id);
                if ( $post && $post->getProfile_id() == $this->reporter_id )
                    $this->annul();
            }
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('Report');
		$this->frontend_handler = $handler;
		$report_title = SK_Language::section('components.report')->text('label').' '. SK_Language::section('components.report.type')->text($this->type);  
		$handler->construct($report_title);
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign(array(
			'reporter_id'	=>	$this->reporter_id,
			'entity_id'		=>	$this->entity_id,
			'type'			=>	$this->type,
			'show_link'		=>  $this->show_link
		));
		
		return parent::render($Layout);
	}
	
	public function handleForm( SK_Form $form )
	{
		$form->getField('reporter_id')->setValue($this->reporter_id);;
		$form->getField('entity_id')->setValue($this->entity_id);
		$form->getField('type')->setValue($this->type);
		
		$form->frontend_handler->bind('success', 'function(){
			window.report_fl_box.close();
		}');
	}
	
}
