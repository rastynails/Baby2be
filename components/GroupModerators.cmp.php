<?php

class component_GroupModerators extends SK_Component 
{
	private $group_id;
	
	private $group;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if (!isset($params['group_id']))
		{
			$this->annul();
		}
		else
		{
			$this->group_id = intval($params['group_id']);
			$this->group = app_Groups::getGroupById($this->group_id); 
		}
		
		parent::__construct('group_moderators');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('GroupModerators');
		$this->frontend_handler->construct(
			$this->group_id,
			SK_Language::text('components.group_moderators.confirm_delete')
		);
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$is_creator = app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $this->group_id);
		$Layout->assign('moderators', app_Groups::getModerators($this->group_id));
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField('group_id')->setValue($this->group_id);
	}
	
	public static function ajax_RemoveModerator( SK_HttpRequestParams $params = null, SK_ComponentFrontendHandler $handler) 
	{	
		$profile_id = SK_HttpUser::profile_id();
		if ($params->has('group_id') && $params->has('mod_id') && $profile_id)
		{
			$group_id = $params->group_id;
			$moderator_id = $params->mod_id;
			
			if (app_Groups::isGroupCreator($profile_id, $group_id))
			{
				if (app_Groups::isGroupModerator($moderator_id, $group_id))
				{
					if (app_Groups::removeGroupModerator($moderator_id, $group_id))
					{
						$handler->message(SK_Language::text("%components.group_moderators.deleted"));
						return array('result' => true);
					}
				}
			}
			else 
			{
				$handler->error(SK_Language::text("%components.group_moderators.not_creator"));
				return array('result' => false);
			}			
		}
		return array('result' => false);
	}
}