<?php

class component_ForumAddTopic extends SK_Component
{
	
	public function __construct( array $params = null )
	{
		parent::__construct('forum_add_topic');
				
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form ) 
	{
		$form->getField('forum_id')->setValue( SK_HttpRequest::$GET['forum_id'] );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$service = new SK_Service('forum_write');
		
		$no_permission = '';
		
		if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
			$no_permission = $service->permission_message['message'];
		
		$Layout->assign( 'no_permission', $no_permission );
	}



}