<?php

class component_ProfileCarousel extends SK_Component
{
    private $list_name;
    private $list;
    private $available_list_name = array( 'new_members_list', 'online_list', 'featured_list',
                                          'match_list', 'friends', 'bookmark_list', 
                                          'neighbourhood_list', 'birthday_list', 'moderator_list',
                                          'who_viewed_me', 'block_list', 'search_result',
                                          'got_requests', 'sent_requests', 'latest_members' );
    private $profile_id;
    private $page;

    private $prev_bt = array();
    private $back_to_list;
    private $next_bt = array();
    
    public function __construct( array $params = null )
    {
        parent::__construct( 'profile_carousel' );
        $this->list_name = SK_HttpRequest::$GET['list_name'];
        $this->page = !empty(SK_HttpRequest::$GET['page']) ? intval( SK_HttpRequest::$GET['page'] ) : 1;
        $this->profile_id = ( (int)SK_HttpRequest::$GET['profile_id'] > 0 ) ? (int)SK_HttpRequest::$GET['profile_id'] : null; 
    }
    
    public function prepare(SK_Layout $Layout, SK_Frontend $Frontend) 
    {
        if ( !in_array($this->list_name, $this->available_list_name) )
        {
            $this->annul();
        }
        
        parent::prepare($Layout, $Frontend);
    }
    
    public function render(SK_Layout $Layout) 
    {
        $this->list = $this->getProfileList( $this->list_name, $this->page );        
        
        if ( !empty( $this->list ) )
        {
            foreach ($this->list['profiles'] as $key => $profile )
            {
                if ( $profile['profile_id'] == $this->profile_id )
                {
                    if ( $key > 0 && $key < count($this->list['profiles']) - 1 )
                    {
                        $prev_profile = $this->list['profiles'][$key - 1];
                        $this->prev_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($prev_profile['profile_id']) );
                        $this->prev_bt['title'] = $prev_profile['username'] ;

                        $next_profile = $this->list['profiles'][$key + 1];
                        $this->next_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($next_profile['profile_id']) );
                        $this->next_bt['title'] = $next_profile['username'];

                        break;
                    }
                    else
                    {
                        if ( $key == 0 )
                        {
                            $item_on_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;

                            $next_profile = $this->list['profiles'][$key + 1];
                            if ( !empty($next_profile) )
                            {
                                $this->next_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($next_profile['profile_id']) );
                                $this->next_bt['title'] = $next_profile['username'];
                            }

                            if ( $this->page != 1 )
                            {
                                $prev_profiles = $this->getProfileList( $this->list_name, $this->page - 1);
                                $prev_profiles_count = count( $prev_profiles['profiles'] ) - 1;
                                $this->prev_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($prev_profiles['profiles'][$prev_profiles_count]['profile_id']) );
                                $this->prev_bt['title'] = $prev_profiles['profiles'][$prev_profiles_count]['username'];
                            }

                            break;
                        }
                        elseif ( $key == count($this->list['profiles']) - 1 ) 
                        {
                            $item_on_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;

                            if ( $this->page != ceil($this->list['total'] / $item_on_page) )
                            {
                                $next_profiles = $this->getProfileList( $this->list_name, $this->page + 1 );
                                $this->next_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($next_profiles['profiles'][0]['profile_id']) );
                                $this->next_bt['title'] = $next_profiles['profiles'][0]['username'];
                            }

                            $prev_profile = $this->list['profiles'][$key - 1];
                            $this->prev_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($prev_profile['profile_id']) );
                            $this->prev_bt['title'] = $prev_profile['username'];
                            break;
                        }
                    }
                }
            }
            
        }
                    $Layout->assign( 'prev_bt', $this->prev_bt );
            $Layout->assign( 'next_bt', $this->next_bt );
        $Layout->assign( 'back_to_list', $this->back_to_list );
        
        return parent::render($Layout);
    }
    
    private function getProfileList( $list_name, $page )
    {
        SK_HttpRequest::$GET['page'] = $page;
        $this->setBackToList( $list_name );
        switch( $list_name )
        {
            case 'new_members_list':
                return app_ProfileList::NewMembersList();
            
            case 'online_list':
                return app_ProfileList::OnlineList(@SK_HttpRequest::$GET['sex']);
            
            case 'featured_list':
                return app_ProfileList::FeaturedList();
            
            case 'match_list':
                return app_ProfileList::ProfileMatcheList( SK_HttpUser::profile_id());
            
            case 'friends':
            case 'got_requests':
            case 'sent_requests':
                $mode = isset(SK_HttpRequest::$GET['tab']) ? SK_HttpRequest::$GET['tab'] : 'friends';
                
                if ($mode == 'friends') {
			$items = app_FriendNetwork::FriendNetworkList( SK_HttpUser::profile_id(), $mode);
		} else {
			$items = app_FriendNetwork::FriendNetworkList(SK_HttpUser::profile_id(), $mode, false);
		}
		return $items;
                
            case 'bookmark_list':
                return app_Bookmark::HotList(SK_HttpUser::profile_id());
            
            case 'neighbourhood_list':
                $mode = @SK_HttpRequest::$GET['mode'];
		$distance = @SK_HttpRequest::$GET['distance'];
		return app_NeighbourhoodList::getList($mode , SK_HttpUser::profile_id(),false, $distance);
            
            case 'birthday_list':
                $mode = @SK_HttpRequest::$GET['mode'];
		$mode = isset($mode) ? $mode : 'today';
		
		return app_ProfileList::BirthdayList($mode);
            
            case 'moderator_list':
                return app_ProfileList::ModeratorList();
            
            case 'who_viewed_me':
                $period = isset(SK_HttpRequest::$GET['period']) ? SK_HttpRequest::$GET['period'] : 'month';
		
		switch($period)
		{			
                    case 'week':
                        $dayTime = time() - mktime(0, 0, 0);
                        $start_stamp = time() - $dayTime - date('w') * 60 * 60 * 24;
                        break;
                    
                    case 'day':
                        $start_stamp = mktime(0,0,0,date('m'), date('d'), date('Y'));
                        break;

                    case 'month':
                    default:
                        $start_stamp = mktime(0,0,0, date('m'), 0, date('Y'));
                        break;			
		}
		$list = app_ProfileViewHistory::getList(SK_HttpUser::profile_id(), $start_stamp, time());
		return $list;
            
            case 'block_list':
                return app_Bookmark::BlockList(SK_HttpUser::profile_id());
            
            case 'search_result':
                $this->searchResult();
                break;
            case 'latest_members':
                $sex = @SK_HttpRequest::$GET['sex'];
                $count = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
                $offset = $count * ( $page - 1 );
                return app_ProfileList::findLatestList($offset, $count, $sex);
        }
    }
    
    private function getProfileUrl( $profile_id = null )
    {
        if ( !empty($profile_id) )
        {
            $result['profile_id'] = $profile_id;
        }
        $result['list_name'] = SK_HttpRequest::$GET['list_name'];
        $result['page'] = SK_HttpRequest::$GET['page'];
        !empty( SK_HttpRequest::$GET['sex'] ) ? $result['sex'] = SK_HttpRequest::$GET['sex'] : null;
        !empty( SK_HttpRequest::$GET['tab'] ) ? $result['tab'] = SK_HttpRequest::$GET['tab'] : null;
        !empty( SK_HttpRequest::$GET['mode'] ) ? $result['mode'] = SK_HttpRequest::$GET['mode'] : null;
        !empty( SK_HttpRequest::$GET['period'] ) ? $result['period'] = SK_HttpRequest::$GET['period'] : null;
        !empty( SK_HttpRequest::$GET['distance'] ) ? $result['distance'] = SK_HttpRequest::$GET['distance'] : null;
        return $result;
    }
    
    private function setBackToList( $list_name )
    {
        if ( !empty($this->back_to_list) )
        {
            return;
        }
        
        switch ($list_name)
        {
            case 'online_list':
            case 'featured_list':
            case 'match_list':
            case 'bookmark_list':
            case 'moderator_list':
            case 'latest_members':
                $this->back_to_list = SK_Navigation::href( $list_name, 
                        $this->getProfileUrl() );
                break;   
            
            case 'new_members_list':
            $this->back_to_list = SK_Navigation::href('profile_new_members_list', 
                    $this->getProfileUrl() );
            break;
            
            case 'friends':
            case 'got_requests':
            case 'sent_requests':
                $this->back_to_list = SK_Navigation::href( 'profile_friend_list', 
                        $this->getProfileUrl() );
                break;
            
            case 'neighbourhood_list':
                $this->back_to_list = SK_Navigation::href( 'profile_location_list',
                    $this->getProfileUrl() );
                break;
            
            case 'birthday_list':
                $this->back_to_list = SK_Navigation::href( 'profile_birthday_list', 
                        $this->getProfileUrl() );
                break;
            
            case 'who_viewed_me':
                $this->back_to_list = SK_Navigation::href( 'who_checked_me_out',
                        $this->getProfileUrl() );
                break;
            
            case 'block_list':
                $this->back_to_list = SK_Navigation::href( 'profile_block_list',
                        $this->getProfileUrl() );
                break;
            
            case 'search_result':
                $this->back_to_list = SK_Navigation::href( 'profile_list',
                        $this->getProfileUrl() );
                break;
        }
    }
    
    private function searchResult()
    {
        $list_id = app_TempProfileList::getListSessionInfo( 'search', 'list_id' );
        if ( !empty($list_id) )
        {
            $profileNumber = app_TempProfileList::getNumberByProfileId( $list_id, $this->profile_id );

            $total = app_TempProfileList::getListSessionInfo( 'search', 'pr_total' );

            if ( (int)$profileNumber + 1 <= $total )
            {
                $nextProfileId = app_TempProfileList::getNextProfileId( $list_id, (int)$profileNumber );

                if( (int)$nextProfileId > 0 )
                {
                    $this->next_bt['profile_url'] = SK_Navigation::href( 'profile', $this->getProfileUrl($nextProfileId) );
                    $this->next_bt['title'] = app_Profile::username( $nextProfileId );
                }
            }

            if ( (int)$profileNumber - 1 > 0 )
            {
                $prevProfileId = app_TempProfileList::getPrevProfileId( $list_id, (int)$profileNumber );
                if ( (int)$prevProfileId > 0 )
                {
                    $this->prev_bt['profile_url'] = SK_Navigation::href('profile', $this->getProfileUrl($prevProfileId) );
                    $this->prev_bt['title'] = app_Profile::username( $prevProfileId );
                }
            }
        }
    }
}