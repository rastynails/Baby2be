<?php

class component_Group extends SK_Component
{
	private $group_id;

	private $group;

	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if (!SK_HttpRequest::$GET['group_id'] || !app_Features::isAvailable(38))
			SK_HttpRequest::showFalsePage();
		else {
			$this->group_id = SK_HttpRequest::$GET['group_id'];
			$this->group = app_Groups::getGroupById($this->group_id);

			if (!$this->group['group_id'] || $this->group['status'] == 'suspended')
				SK_HttpRequest::showFalsePage();

		}

		parent::__construct('group');
	}

	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('Group');
		$this->frontend_handler->construct( array(
			'group_id'		=> $this->group_id,
			'profile_id'	=> SK_HttpUser::profile_id(),
			'url_edit'		=> SK_Navigation::href('group_edit', array('group_id' => $this->group_id)),
			'url_invite'	=> SK_Navigation::href('group_edit', array('group_id' => $this->group_id, 'action' => 'invite')),
			'url_claims'	=> SK_Navigation::href('group_edit', array('group_id' => $this->group_id, 'action' => 'claims')),
			'url_mails'		=> SK_Navigation::href('group_edit', array('group_id' => $this->group_id, 'action' => 'mails')),
			'confirm_msg'	=> SK_Language::text('components.group.confirm_msg'),
			'confirm_leave' => SK_Language::text('%components.group.confirm_leave')
		));

		parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{
		$bc_item_1 = app_TextService::stOutputFormatter(app_TextService::stCensor($this->group['title'], FEATURE_GROUP, true), FEATURE_GROUP, false);
		SK_Navigation::removeBreadCrumbItem();
		SK_Navigation::addBreadCrumbItem($bc_item_1);

		SK_Language::defineGlobal( array('grouptitle' => $bc_item_1) );

                if ( !empty($this->group['description']) )
                {
                    $this->getDocumentMeta()->description = $this->group['description'];
                }

		$Layout->assign('members', app_Groups::getGroupMembers($this->group_id, true));
		$Layout->assign('group', $this->group);
		$Layout->assign('group_image', $this->group['photo'] != 0 ? app_Groups::getGroupImageURL($this->group_id, $this->group['photo'], false) : null);
		$Layout->assign('moderators', app_Groups::getModerators($this->group_id));

		// user role
		$Layout->assign('is_creator', app_Groups::isGroupCreator( SK_HttpUser::profile_id(), $this->group_id));
		$Layout->assign('is_member', app_Groups::isGroupMember( SK_HttpUser::profile_id(), $this->group_id));
		$Layout->assign('is_moderator', app_Groups::isGroupModerator( SK_HttpUser::profile_id(), $this->group_id));
        $Layout->assign('is_site_moderator', SK_HttpUser::isModerator());
		$Layout->assign('is_blocked', app_Groups::isBlocked(SK_HttpUser::profile_id(), $this->group_id));

		$Layout->assign('show_invitation',
			app_Groups::profileIsInvited(SK_HttpUser::profile_id(), $this->group_id)
			&& !app_Groups::isGroupMember(SK_HttpUser::profile_id(), $this->group_id) );

		$Layout->assign('group_comments', new component_AddComment($this->group_id, 'group', 'group_add'));
		$Layout->assign('group_forum', new component_GroupForumLastTopicList(array('group_id' => $this->group_id)));

		return parent::render( $Layout );
	}

	public static function ajax_declineInvitation($params, SK_ComponentFrontendHandler $handler) {

		$profile_id = $params->profile_id;
		$group_id = $params->group_id;

		if (app_Groups::declineInvitaton($profile_id, $group_id))
			$handler->message( SK_Language::text('components.group.declined'));
	}

	public static function ajax_acceptInvitation($params, SK_ComponentFrontendHandler $handler) {

		$profile_id = $params->profile_id;
		$group_id = $params->group_id;

		if (app_Groups::acceptInvitaton($profile_id, $group_id))
			$handler->message( SK_Language::text('components.group.accepted'));
	}

	public static function ajax_LeaveGroup($params, SK_ComponentFrontendHandler $handler)
	{
		$group_id = $params->group_id;

		if (app_Groups::leaveGroup($group_id, SK_HttpUser::profile_id())) {
			$handler->message( SK_Language::text('components.group.unregistered'));
			return array('result' => true);
		}
	}

}