<?php

class component_LatestActivity extends SK_Component
{
	/**
	 * Viewer `profile_id`.
	 *
	 * @var integer
	 */
	private $viewer_id;


	private $list = array();
    public $userId;
    public $actor;
    public $counter;

	/**
	 * Constructor.
	 */
	public function __construct( array $params = null )
	{
		$service = new SK_Service( 'view_latest_activity', SK_HttpUser::profile_id() );

		$isGuest != (bool) intval( SK_HttpUser::profile_id() );

		$isUserself = !$isGuest && !empty($params['userId']) && $params['userId'] == SK_HttpUser::profile_id();

		$allowed = $isUserself ||  $service->checkPermissions() == SK_Service::SERVICE_FULL;

		if( !app_Features::isAvailable(41) || !$allowed )
		{
			$this->annul();
			return;
		}

        $this->actor = $params['actor'];
        $this->userId = !empty($params['userId']) ? $params['userId'] : null;
        $this->counter = !empty($params['counter']) ? $params['counter'] : 1;

		switch( $params["actor"] ){
			case "user":
				$this->list = $this->getList($this->counter, $params['userId']);

				break;

			case 'user-friends':
			default:
				$this->list = $this->getList($this->counter);
				break;
		}

		parent::__construct('latest_activity');
	}

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('LatestActivity');
        $this->frontend_handler = $handler;
        $handler->construct($this->actor, $this->userId, $this->counter);
    }

	public function render( SK_Layout $layout )
	{
		$list = $this->list;

		foreach ($list as $key=>$item)
		{
			foreach($item->feeds as $key_d2=>$feed)
			{
				switch( $feed->type )
				{
					case 'blog_post_add':
					case 'news_post_add':

						$href = SK_Navigation::href( 'blog_post_view', array( 'postId'=>$feed->items[0] ) );

						$service = app_BlogService::newInstance();

						$post = $service->findBlogPostById($feed->items[0]);

                        if( !$post )
                        {
                            continue;
                        }

						$title = $post->getTitle();
						$text = $post->getText();

						$text = str_replace("<", "[", $text);
						$text = str_replace(">", "]", $text);
						$text = (strlen($text) > 80 )? substr($text, 0, 3).'...' : $text;

						$list[$key]->feeds[$key_d2]->tpl_vars = array(
						'title' => $title,
						'text' => $text,
						'href' => $href,
						);

						break;

					case 'media_upload':
						
						$hash = app_ProfileVideo::getVideoHash($feed->items[0]);

						if (SK_Config::section("navigation")->section("settings")->mod_rewrite === true)
							$url = nav_video::profile_video(array('video_id'=>$feed->items[0]));
						else 	
							$url = SK_Navigation::href("profile_video_view", array("videokey" => $hash), true);

						
						$ownerId = app_ProfileVideo::getVideoOwnerById($feed->items[0]);
						$info = app_ProfileVideo::getVideoInfo($ownerId, $hash );

						$list[$key]->feeds[$key_d2]->tpl_vars = array(
						'href' => $url,
						'title' => $info['title'],
						'desc' => $info['description']
						);
							
						break;

					case 'music_upload':
						$url = nav_music::profile_music(array('music_id'=>$feed->items[0]));

						$hash = app_ProfileMusic::getMusicHash( $feed->items[0] );

						$ownerId = app_ProfileMusic::getMusicOwnerById( $feed->items[0] );
						
						$music = app_ProfileMusic::getMusicInfo( $ownerId, $hash );
												
						$list[$key]->feeds[$key_d2]->tpl_vars = array(
						'href' => $url,
						'title' => $music['title']);

						break;
							
					case 'photo_upload':
						
						if (SK_Config::section("navigation")->section("settings")->mod_rewrite === true)
							$url = nav_photo::profile_photo(array('photo_id'=>$feed->items[0]));
						else 
							$url = SK_Navigation::href("profile_photo", array("photo_id" => $feed->items[0]), true);

						$list[$key]->feeds[$key_d2]->tpl_vars = array(
						'photo_url' => $url);						

						break;

					case 'add_group':

						$groupId = $feed->items[0];

						$group = app_Groups::getGroupById($groupId);

                        if( !$group )
                        {
                            continue;
                        }

						$list[$key]->feeds[$key_d2]->tpl_vars = array(
							'title' => $group['title'],
							'href' => SK_Navigation::href('group', array('group_id'=>$group['group_id']))
						);

						break;

					case 'event_add':
						$event_id = $feed->items[0];
						$url = SK_Navigation::href( 'event', array('eventId'=>$event_id) );


						$event = app_EventService::newInstance()->findById($event_id);

                        if( !$event )
                        {
                            continue;
                        }

						$list[$key]->feeds[$key_d2]->tpl_vars = array(
						'event_url' => $url,
						'title' => $event->getTitle() );
						break;						
						
					default:

						switch( $feed->type )
						{
							case 'photo_comment':
								
								$photoId = $feed->items[0];
								$href = app_ProfilePhoto::getPermalink($photoId);
								
								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'photo_url' => $href,
								);
								break;

							case 'blog_post_comment':
							case 'news_post_comment':

								$postId = $feed->items[0];
								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'href' => SK_Navigation::href('blog_post_view', array( 'postId'=> $postId )),
								);

								break;

							case 'profile_comment':
								$profileId = $feed->items[0];

								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'userId' => $profileId 
								);
								break;

							case 'video_comment':
								$videoId = $feed->items[0];
 								$hash = app_ProfileVideo::getVideoHash($videoId);
 								
								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'url' => app_ProfileVideo::getVideoViewURL($hash),
								);
								break;
								
							case 'event_comment':
								$eventId = $feed->items[0];

								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'url' => SK_Navigation::href('event', array('eventId'=> $eventId))
								);

								break;
								
							case 'group_comment':
								$id = $feed->items[0];

								$group = app_Groups::getGroupById($id);
								
								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'group_url' => SK_Navigation::href('group', array('group_id'=> $id)),
									'title' => $group['title'],
								);

								break;

							case 'music_comment':
								$id = $feed->items[0];

								$list[$key]->feeds[$key_d2]->tpl_vars = array(
									'music_url' => nav_music::profile_music( array('music_id'=>$id) )
								);

								break;								
						}
						break;
				}
			}

		}

		$layout->assign('activity_list', $list);

		return parent::render($layout);
	}

	private function getList($counter=1,$userId=null){

		if($userId == null)
		return app_UserActivities::get_list($counter, SK_HttpUser::profile_id() );

		return app_UserActivities::getUserActivityList($counter, $userId );
	}

}
