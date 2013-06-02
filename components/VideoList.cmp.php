<?php

class component_VideoList extends SK_Component
{
	private $list_type;

	/**
	 * Component VideoList constructor.
	 *
	 * @return component_VideoList
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('video_list');

		$available_types = array('latest', 'toprated', 'most_viewed', 'discussed', 'tags', 'profile', 'categories');

		if (isset(SK_HttpRequest::$GET['profile_id']))
		{
			$this->list_type = 'profile';
		}
		elseif (!isset($params['list_type']) || !in_array($params['list_type'], $available_types))
			$this->list_type = 'latest';
		else
			$this->list_type = $params['list_type'];
		}

	public function render( SK_Layout $Layout )
	{

		$tag_limit = 50;

		switch ($this->list_type)
		{
		    case 'categories':
		        $cat_id = (int)SK_HttpRequest::$GET['cat_id'];
		        $video = app_VideoList::getVideoList( 'categories', SK_HttpRequest::$GET['page'], null, $cat_id);
		        $Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), $tag_limit ));
		        $category = app_VideoList::getVideoCategoryById($cat_id);
		        if ( $category )
		        {
                    SK_Language::defineGlobal('category', SK_Language::text('%video_categories.cat_'.$category['category_id']));
		        }
		        else {
                    SK_Language::defineGlobal('category', SK_Language::text('%components.video_categories.categories'));
                }
		        break;

			case 'latest':
				$video = app_VideoList::getVideoList( 'latest', SK_HttpRequest::$GET['page']);
				$Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), $tag_limit ));
				break;

			case 'tags':
				if (SK_HttpRequest::$GET['tag']) {
					$bc_item_3 = SK_HttpRequest::$GET['tag'];
					SK_Navigation::addBreadCrumbItem($bc_item_3);

					$video = app_VideoList::getTaggedVideo(SK_HttpRequest::$GET['tag'], SK_HttpRequest::$GET['page']);
					$Layout->assign('tag_words', SK_HttpRequest::$GET['tag']);
					$Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), $tag_limit ));
				}
				else  {
					$Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), 1000 ));
				}
				break;

			case 'toprated':
				$video = app_VideoList::getVideoList( 'toprated', SK_HttpRequest::$GET['page']);
				$Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), $tag_limit ));
				break;

			case 'discussed':
				$video = app_VideoList::getVideoList( 'discussed', SK_HttpRequest::$GET['page']);
				$Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), $tag_limit ));
				break;

			case 'profile':
				if (SK_HttpRequest::$GET['profile_id']) {
					$username = app_Profile::username(SK_HttpRequest::$GET['profile_id'], 'username');
					$Layout->assign('username', $username);
					$bc_item = SK_Language::section('components.video_list')->text('video_by').' '.$username;
					SK_Navigation::addBreadCrumbItem($bc_item);
					$page = isset(SK_HttpRequest::$GET['page']) ? SK_HttpRequest::$GET['page'] : 1;
					$video = app_ProfileVideo::getProfileVideo(SK_HttpRequest::$GET['profile_id'], 'active', false, $page);
					$Layout->assign('VideoTagNavigator', new component_TagNavigator('video', SK_Navigation::href('video_list'), $tag_limit ));
				}
				else SK_HttpRequest::showFalsePage();
				break;
		}

		$Layout->assign('list_type', $this->list_type);
		$Layout->assign('enable_categories', SK_Config::section('video')->Section('other_settings')->get('enable_categories'));

		SK_Language::defineGlobal( 'username', $username );

		if (isset($video))
		{
			$Layout->assign('list', $video['list']);
			$Layout->assign('total', $video['total']);

			$Layout->assign('paging',array(
				'total'=> $video['total'],
				'on_page'=> SK_Config::Section('video')->Section('other_settings')->display_media_list_limit,
				'pages'=> SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page,
			));
		}

		return parent::render($Layout);
	}

}

