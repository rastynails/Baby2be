<?php

class component_ForumSearch extends SK_Component
{
	
	public function __construct( array $params = null )
	{	
		parent::__construct('forum_search');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_FrontendHandler $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{							
		$handler = new SK_ComponentFrontendHandler('ForumSearch');
		
		$handler->construct();
		
		$this->frontend_handler = $handler;
		
		parent::prepare($Layout, $Frontend);
	}	
			
	public function handleForm( SK_Form $form )
	{
		
		$forums = app_Forum::getForumsList( true );
		$form->getField('search_in_forums')->setValues($forums);
		$form->getField('search_in_forums')->setValue('all');
		
		$form->frontend_handler->bind('success', 'function( data ) {	
			if (data) {				
				this.ownerComponent.hideSearchBox();
				if(data.error)
					this.ownerComponent.error( data.error );
				else
					this.ownerComponent.redirect( "'.SK_Navigation::href('forum_search_result' ).'" );
			}
		}');
	}

	

	
}