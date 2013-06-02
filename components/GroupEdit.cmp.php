<?php

class component_GroupEdit extends SK_Component 
{
	private $group_id;
	
	private $group;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if ( !SK_HttpRequest::$GET['group_id'] )
		{
			SK_HttpRequest::showFalsePage();
		}
		else
		{
			$this->group_id = intval(SK_HttpRequest::$GET['group_id']);
			$this->group = app_Groups::getGroupById($this->group_id); 
		}
		
		parent::__construct('group_edit');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('GroupEdit');
		$this->frontend_handler->construct( 
			$this->group_id,
			SK_Language::text('components.group_edit.confirm_delete'),
			SK_Navigation::href('groups')
		);
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();

		$bc_item_1 = app_TextService::stOutputFormatter(app_TextService::stCensor($this->group['title'], FEATURE_GROUP, true), FEATURE_GROUP, false);
		
		SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('group', array('group_id'=>$this->group_id)));
		SK_Navigation::addBreadCrumbItem(SK_Language::text('%nav_doc_item.group_edit'));
		SK_Language::defineGlobal('group_edit_page', SK_Language::text('%components.group_edit.form_title'));
		
		$is_creator = app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $this->group_id);
		
		if (!$is_creator)
		{
			$Layout->assign('error', SK_Language::text('components.group_edit.not_creator'));
		}
		
		$Layout->assign('group_image', $this->group['photo'] != 0 ? app_Groups::getGroupImageURL($this->group_id, $this->group['photo'], false) : null);
		$Layout->assign('moderators_cmp', new component_GroupModerators(array('group_id' => $this->group_id)));
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField( 'group_id' )->setValue( $this->group_id );
		$form->getField( 'title' )->setValue( $this->group['title'] );		
		$form->getField( 'description' )->setValue( $this->group['description'] );		
		$form->getField( 'browse_type' )->setValue( $this->group['browse_type'] );		
		$form->getField( 'join_type' )->setValue( array('select' => $this->group['join_type'], 'checkbox' => $this->group['allow_claim'])  );
	}
	
	public static function ajax_DeleteGroup( SK_HttpRequestParams $params = null, SK_ComponentFrontendHandler $handler) 
	{	
		$group_id = $params->group_id;
		$profile_id = SK_HttpUser::profile_id();
		
		if ($params->has('group_id') && $group_id)
		{	
			if (!app_Groups::isGroupCreator($profile_id, $group_id))
			{
				$handler->error(SK_Language::text('components.group_edit.cannot_delete') );
				return array('result' => false);
			}
			else 
			{
				if ( app_Groups::removeGroup($group_id))
				{
					$handler->message(SK_Language::text('components.group_edit.group_deleted') );
					return array('result' => true);
				}
			}			
		}
		return array('result' => false);
	}
	
    public static function ajax_deleteImage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        $group = app_Groups::getGroupById($params->id);
        
        if ( !$group )
            return;

        if ( !SK_HttpUser::is_authenticated() || ( !SK_HttpUser::isModerator() && $group['owner_id'] != SK_HttpUser::profile_id() ) )
            return; 

        app_Groups::deleteGroupImage($params->id, $group['photo']);
    }
}