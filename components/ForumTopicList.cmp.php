<?php

class component_ForumTopicList extends SK_Component
{
	private $forum_id;

	public function __construct( array $params = null )
	{
		parent::__construct('forum_topic_list');
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		if (!app_Features::isAvailable(22)) {
			SK_HttpRequest::showFalsePage();
		}

		if ( app_Forum::isProfileBanned(SK_HttpUser::profile_id()) ){
			SK_HttpRequest::redirect( SK_Navigation::href('forum_banned_profile_list',
									  					  array('profile_id'=>SK_HttpUser::profile_id())) );
		}

		$Frontend->onload_js(
			'$("#'.$this->getTagAutoId('new_topic_btn').'").click(
				function() {
					$("input[name=\'title\']").focus();
				}
		)');

		parent::prepare($Layout, $Frontend);
	}


	public function render( SK_Layout $Layout )
	{
		$this->forum_id = SK_HttpRequest::$GET['forum_id'];
		$cur_page = (int)SK_HttpRequest::$GET['page'];
		$configs = new SK_Config_Section('forum');

		if( !$this->forum_id )
			SK_HttpRequest::redirect(SK_Navigation::href('forum_group_list'));

		$forum_info = app_Forum::getForumInfo($this->forum_id);

		if( !$forum_info )
			SK_HttpRequest::redirect(SK_Navigation::href('forum_group_list'));

		$topics = app_Forum::getTopicListByForumId($this->forum_id, $configs->topic_count_on_page, $cur_page);

		SK_Navigation::removeBreadCrumbItem();

		if ( $group_id = app_Groups::getGroupByForumID($this->forum_id))
		{
			SK_Navigation::removeBreadCrumbItem();
			$group = app_Groups::getGroupById($group_id);
			SK_Navigation::addBreadCrumbItem(SK_Language::text('%nav_doc_item.groups'), SK_Navigation::href('groups'));
			SK_Navigation::addBreadCrumbItem($group['title'], SK_Navigation::href('group', array('group_id' => $group_id)));
			$title_lang = SK_Language::text('%components.forum_topic_list.group_forum_title', array('title' => $group['title']));
			SK_Navigation::addBreadCrumbItem($title_lang);
			SK_Language::defineGlobal('forumtitle', $title_lang);

			$Layout->assign('group_forum', true);
			$Layout->assign('is_member', app_Groups::isGroupMember(SK_HttpUser::profile_id(), $group_id));
			$Layout->assign('is_blocked', app_Groups::isBlocked(SK_HttpUser::profile_id(), $group_id));
			$Layout->assign('group', $group);
		}
		else
		{
			SK_Navigation::addBreadCrumbItem($forum_info['name']);
			SK_Language::defineGlobal('forumtitle', $forum_info['name']);
		}

		$service = new SK_Service('forum_write');
		$no_permission = ( $service->checkPermissions()==SK_Service::SERVICE_FULL );

		$Layout->assign( 'no_permission', $no_permission );

		$Layout->assign('forum_info', $forum_info);
		$Layout->assign('topics', $topics);
		$Layout->assign('profile_id', SK_HttpUser::profile_id());
		$Layout->assign('moderator', SK_HttpUser::isModerator() );

		$Layout->assign('paging',array(
			'total'=> app_Forum::getTopicCount($this->forum_id),
			'on_page'=> $configs->topic_count_on_page,
			'pages'=> $configs->show_page_count
		));
		return parent::render($Layout);
	}

	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField('forum_id')->setValue( $this->forum_id );
	}

}
