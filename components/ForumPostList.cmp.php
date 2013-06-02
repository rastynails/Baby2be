<?php

class component_ForumPostList extends SK_Component
{
	private $posts;
	private $topic_id;
	private $topic_info;
	private $moderator;

	public function __construct( array $params = null )
	{
		parent::__construct('forum_post_list');
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		if (!app_Features::isAvailable(22))
        {
			SK_HttpRequest::showFalsePage();
		}

		$profile_id = SK_HttpUser::profile_id();

		if ( app_Forum::isProfileBanned($profile_id) )
        {
			SK_HttpRequest::redirect( SK_Navigation::href('forum_banned_profile_list', array('profile_id'=>$profile_id)) );
		}

		$service = new SK_Service('forum_read', $profile_id);
		if ($service->checkPermissions()!=SK_Service::SERVICE_FULL)
        {
			$message = $service->permission_message['alert'];
			$_SESSION['messages'][] = array( 'message'=>$message,'type' => 'error' );
			SK_HttpRequest::redirect( SK_Navigation::href( 'payment_selection' ) );
		}
		else
		{
            $service->trackServiceUse();
		}


		$configs = new SK_Config_Section('forum');

		$this->topic_id = SK_HttpRequest::$GET['topic_id'];
		$this->topic_info = app_Forum::getTopic($this->topic_id);

		if( !$this->topic_info )
			SK_HttpRequest::redirect(SK_Navigation::href('forum_group_list'));

        $this->getDocumentMeta()->description = $this->topic_info['text'];

		$this->posts = app_Forum::getPostListByTopicId( $this->topic_id, $configs->post_count_on_page, self::getCurrent_page() );

		//unset first post
		if ( !self::getCurrent_page() || self::getCurrent_page()==1 )
			array_shift( $this->posts );

		//posts_ids array
		$this->moderator = SK_HttpUser::isModerator();
		$is_topic_lock = app_Forum::isTopicClosed( $this->topic_id );
        
        $post_arr = array();

		if ( $this->posts )
		{
			foreach ( $this->posts as $key=>$post )
			{
				$post_arr[] = $post['forum_post_id'];
				$is_owner = ( $post['profile_id']==$profile_id );
                $this->posts[$key]['is_owner'] = $is_owner;
				$this->posts[$key]['action_buttons'] =( (!$is_topic_lock && $is_owner) || $this->moderator );
                
			}
		}
        
        app_Attachment::getListByEntityIdList('forum_post', $post_arr);

		if ( $group_id = app_Groups::getGroupByForumTopicID($this->topic_id))
		{
			$group = app_Groups::getGroupById($group_id);
			$Layout->assign('group_forum', true);
			$Layout->assign('is_member', app_Groups::isGroupMember(SK_HttpUser::profile_id(), $group_id));
			$Layout->assign('group', $group);
		}

		$handler = new SK_ComponentFrontendHandler('ForumPostList');

		if( $post_arr )
        {
			$handler->construct( $post_arr, self::getCurrent_page() );
		}

		$handler->profileNotify();

		$this->frontend_handler = $handler;

		return parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();
		SK_Navigation::removeBreadCrumbItem();

		$forum_info = app_Forum::getForumInfo( $this->topic_info['forum_id'] );
		$subscribed = app_Forum::isProfileSubscribed( SK_HttpUser::profile_id(), $this->topic_id );
		$url = SK_Navigation::href( 'forum', array('forum_id'=>$forum_info['forum_id']) );

        $topic_title = app_TextService::stCensor($this->topic_info['title'], FEATURE_FORUM, true);
        $topic_title = htmlspecialchars(app_TextService::stOutputFormatter($topic_title, FEATURE_FORUM, false));

		if ( $group_id = app_Groups::getGroupByForumTopicID($this->topic_id) )
		{
			SK_Navigation::removeBreadCrumbItem();
			$group = app_Groups::getGroupById($group_id);

			$title = app_TextService::stCensor($group['title'], FEATURE_GROUP, true);
            $title = app_TextService::stOutputFormatter($title, FEATURE_GROUP, false);

			SK_Navigation::addBreadCrumbItem(SK_Language::text('%nav_doc_item.groups'), SK_Navigation::href('groups'));
			SK_Navigation::addBreadCrumbItem($title, SK_Navigation::href('group', array('group_id' => $group_id)));
			$title_lang = SK_Language::text('%components.forum_topic_list.group_forum_title', array('title' => $title));
			SK_Navigation::addBreadCrumbItem($title_lang, $url);
			SK_Navigation::addBreadCrumbItem($topic_title);
			SK_Language::defineGlobal('topictitle', $topic_title);

            $Layout->assign('is_blocked', app_Groups::isBlocked(SK_HttpUser::profile_id(), $group_id));

			$Layout->assign('group_forum', true);
			$Layout->assign('group', $group);
		}
		else
		{
			SK_Navigation::addBreadCrumbItem( $forum_info['name'], $url );
			SK_Navigation::addBreadCrumbItem( $topic_title );
		}

		$Layout->assign('topic_info', $this->topic_info);
		$Layout->assign('posts', $this->posts);
		$Layout->assign('moderator', $this->moderator);
		$Layout->assign('profile_id', SK_HttpUser::profile_id());
		$Layout->assign('subscribed', $subscribed);

		$configs = new SK_Config_Section('forum');
		$Layout->assign('paging',array(
			'total'=> app_Forum::getPostCount($this->topic_id),
			'on_page'=> $configs->post_count_on_page,
			'pages'=> $configs->show_page_count
		));

		return parent::render($Layout);
	}

	public function handleForm( SK_Form $form )
	{
		if ( $form->getName()=='forum_edit_post' )
		{
			$form->frontend_handler->bind('success', 'function( data ) {
					if (data.error) {
						this.ownerComponent.error( data.error );
						this.ownerComponent.hideEditBox(data.post_id);
					}
					else {
						this.ownerComponent.refreshPostText(data.id, data.text);
					}
				}');
		}
		elseif( $form->getName()=='forum_ban_profile' )
		{
			$form->getField('period')->setValues( app_Forum::getBanPeriods() );
			$form->frontend_handler->bind('success', 'function( data ) {
					if (data) {
						this.ownerComponent.hideBanBox();
					}
				}');
		}
	}

	public static function ajax_DeletePost( $params , SK_ComponentFrontendHandler $handler)
	{
		app_Forum::DeletePost( $params->post_id );
		$configs = new SK_Config_Section('forum');
		$pages_count = ceil( (app_Forum::getPostCount($params->topic_id)-1)/$configs->post_count_on_page );
		if ( $params->cur_page > $pages_count )
			$handler->redirect( SK_Navigation::href('topic', array('topic_id'=>$params->topic_id, 'page'=>$pages_count)) );
		else
			$handler->redirect();
	}

	public static function ajax_EditPost( $params , SK_ComponentFrontendHandler $handler )
	{
		$post_info = app_Forum::getGroupForumTopicPostInfo( $params->post_id, 'post' );

		$handler->showEditBox( $params->post_id, $post_info['text'] );
	}

	public static function ajax_ReplyPost( $params , SK_ComponentFrontendHandler $handler)
	{
		$post_info = app_Forum::getPostInfo( $params->post_id );

		$profile = app_Profile::username( $post_info['profile_id'] );

		$create_date = app_Forum::getQuoteDate($post_info['create_stamp']);

		$handler->replyPost( $profile, $post_info['text'], $create_date );
	}

	public static function ajax_BanProfile( $params , SK_ComponentFrontendHandler $handler)
	{
		$post_info = app_Forum::getGroupForumTopicPostInfo( $params->post_id, 'post' );

		$handler->showBanBox( $post_info['profile_id'] );
	}

	public static function ajax_UpdateSubscribe( $params, SK_ComponentFrontendHandler $handler)
	{
		$lang_msg = SK_Language::section('components.forum_post_list.messages');
		$is_subscribed = app_Forum::isProfileSubscribed(SK_HttpUser::profile_id(), $params->topic_id );
		if( $is_subscribed )
		{
			app_Forum::unSubscribeProfile( SK_HttpUser::profile_id(), $params->topic_id );
			$handler->message( $lang_msg->text('you_unsubscribed') );
		}
		else
		{
			app_Forum::subscribeProfile( SK_HttpUser::profile_id(), $params->topic_id );
			$handler->message( $lang_msg->text('you_subscribed') );
		}
	}

	public static function getCurrent_page()
	{
		$page = @SK_HttpRequest::$GET['page'];
		return $page;
	}

}
