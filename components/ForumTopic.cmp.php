<?php

class component_ForumTopic extends SK_Component
{
	private $topic_info;
	private $topic_id;

	public function __construct( array $params = null )
	{
		parent::__construct('forum_topic');
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->topic_id = SK_HttpRequest::$GET['topic_id'];

		if( !$this->topic_id )
			SK_HttpRequest::redirect(SK_Navigation::href('forum'));

		$this->topic_info = app_Forum::getTopic($this->topic_id);
		$this->topic_info['text'] = strip_tags($this->topic_info['text']);
		$this->topic_info['text'] = app_Forum::forumTagsToHtmlChars($this->topic_info['text']);

		$handler = new SK_ComponentFrontendHandler('ForumTopic');

		$handler->construct( $this->topic_info['forum_post_id'], $this->topic_info['forum_id'] );

		$this->frontend_handler = $handler;

		parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{
		$this->topic_info['is_deleted'] = app_Profile::isProfileDeleted( $this->topic_info['profile_id'] );
		$this->topic_info['username'] = app_Profile::username( $this->topic_info['profile_id'] );
		SK_Language::defineGlobal('topictitle', strip_tags($this->topic_info['title']));

		$service = new SK_Service('forum_write');
		$no_permission = ( $service->checkPermissions()==SK_Service::SERVICE_FULL );

		$Layout->assign( 'no_permission', $no_permission );

		$Layout->assign('topic_info', $this->topic_info);
		$Layout->assign('profile_id', SK_HttpUser::profile_id());
		$Layout->assign('moderator', SK_HttpUser::isModerator() );
		$Layout->assign('cur_page', component_ForumPostList::getCurrent_page());

		if ( $group_id = app_Groups::getGroupByForumTopicID($this->topic_id))
		{
			$group = app_Groups::getGroupById($group_id);
			$is_member = app_Groups::isGroupMember(SK_HttpUser::profile_id(), $group_id);
			$is_moderator = app_Groups::isGroupModerator(SK_HttpUser::profile_id(), $group_id)
                || app_Profile::isProfileModerator(SK_HttpUser::profile_id());

			if ( $group['browse_type'] == 'private' && !$is_member && !$is_moderator )
			{
			    SK_HttpRequest::redirect(SK_Navigation::href('group', array('group_id' => $group_id)));
			}

			$Layout->assign('group_forum', true);
			$Layout->assign('is_member', $is_member);
			$Layout->assign('group_moderator', $is_moderator);
			$Layout->assign('group', $group);
		}

		return parent::render($Layout);
	}

	public function handleForm( SK_Form $form )
	{
		if( $form->getName()=='forum_edit_topic' )
		{
			$topic = app_Forum::getTopic($this->topic_id);

			$form->getField('topic_id')->setValue($this->topic_id);
			$form->getField('title')->setValue( $topic['title'] );
			$form->getField('first_post')->setValue( $topic['text'] );

			$form->frontend_handler->bind('success', 'function( data ) {
					if (data.error) {
						this.ownerComponent.hideEditBox();
						this.ownerComponent.error( data.error );
					}
					else{
						this.ownerComponent.refreshTopic(data.title, data.text);
					}
				}');
		}
		elseif ( $form->getName()=='forum_move_topic' )
		{
			$forums = app_Forum::getForumsList();
			$form->getField('topic_id')->setValue($this->topic_id);
			$form->getField('to_forum_id')->setValues($forums);

			$form->frontend_handler->bind('success', 'function( data ) {
				if (data) {
					this.ownerComponent.hideMoveBox();
					this.ownerComponent.redirect();
				}
			}');
		}

	}

	public static function ajax_ReplyPost( $params , SK_ComponentFrontendHandler $handler)
	{
		$post_info = app_Forum::getGroupForumTopicPostInfo( $params->post_id, 'post' );

		$profile = app_Profile::username( $post_info['profile_id'] );

		$create_date = app_Forum::getQuoteDate($post_info['create_stamp']);

		$handler->replyPost( $profile, $post_info['text'], $create_date );
	}

	public static function ajax_EditTopic( $params , SK_ComponentFrontendHandler $handler )
	{
		$topic_info = app_Forum::getGroupForumTopicPostInfo( $params->post_id, 'post' );

		$handler->showEditBox( $params->post_id, $post_info['title'], $post_info['text'] );
	}

	public static function ajax_DeleteTopic( $params , SK_ComponentFrontendHandler $handler )
	{
		app_Forum::DeleteTopic( $params->topic_id );

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_FORUM_ADD_TOPIC, $params->topic_id);
            app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $params->topic_id, ENTITY_TYPE_FORUM_ADD_TOPIC);
        }

		$handler->redirect( SK_Navigation::href('forum', array( 'forum_id'=>$params->forum_id )) );
	}

	public static function ajax_LockTopic( $params , SK_ComponentFrontendHandler $handler )
	{
		$lang_msg = SK_Language::section('components.forum_topic.messages') ;

		app_Forum::CloseTopic( $params->topic_id );
		$handler->message( $lang_msg->text('topic_locked') );
		$handler->redirect();
	}

	public static function ajax_StickyTopic( $params , SK_ComponentFrontendHandler $handler )
	{
		$lang_msg = SK_Language::section('components.forum_topic.messages') ;

		app_Forum::stickyTopic( $params->topic_id );
		$handler->message( $lang_msg->text('topic_is_sticky') );
	}

	public static function ajax_UnLockTopic( $params , SK_ComponentFrontendHandler $handler )
	{
		$lang_msg = SK_Language::section('components.forum_topic.messages') ;

		app_Forum::OpenTopic( $params->topic_id );
		$handler->message( $lang_msg->text('topic_unlocked') );
		$handler->redirect();
	}

	public static function ajax_UnStickyTopic( $params , SK_ComponentFrontendHandler $handler )
	{
		$lang_msg = SK_Language::section('components.forum_topic.messages') ;

		app_Forum::unstickyTopic( $params->topic_id );
		$handler->message( $lang_msg->text('topic_is_unsticky') );
	}

	public static function ajax_BanProfile( $params , SK_ComponentFrontendHandler $handler)
	{
		$post_info = app_Forum::getGroupForumTopicPostInfo( $params->post_id, 'post' );

		$handler->showBanBox( $post_info['profile_id'] );
	}
}