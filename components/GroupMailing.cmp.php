<?php

class component_GroupMailing extends SK_Component 
{
	private $group_id;
	
	private $group;
	
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
		
		parent::__construct('group_mailing');
	}
	
	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();
		$bc_item_1 = app_TextService::stCensor($this->group['title'], FEATURE_GROUP, true);
		SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('group', array('group_id'=>$this->group_id)));
		$lang_key = SK_Language::text('%forms.group_send_message.page_header');
		SK_Navigation::addBreadCrumbItem($lang_key);
		SK_Language::defineGlobal('group_edit_page', $lang_key);
		
		$is_creator = app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $this->group_id);
		
		if ( !$is_creator)
			$Layout->assign('error', SK_Language::text('components.group_claims.no_access'));
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField('group_id')->setValue( $this->group_id );
	}
}