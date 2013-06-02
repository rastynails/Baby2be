<?php

class component_ForumSearchResult extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('forum_search_result');
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
		
	}

	
	public function render( SK_Layout $Layout )
	{
		$keywords = explode( " ", $_SESSION['forum_search'] );
		$forums = $_SESSION['search_in_forums'];
		$configs = new SK_Config_Section('forum');
		
		$result = app_Forum::getSearchResult( $keywords, $forums, $configs->post_count_on_page, SK_HttpRequest::$GET['page'] );
		$Layout->assign('result', $result);

		$Layout->assign('paging',array(
			'total'=> $result['total'],
			'on_page'=> $configs->post_count_on_page,
			'pages'=> $configs->show_page_count
		));	
		
		return parent::render($Layout);
	}
	
}
