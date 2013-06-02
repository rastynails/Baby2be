<?php

class component_MemberHomeLinks extends SK_Component 
{
	public function __construct( array $params = null )
	{
		parent::__construct('member_home_links');
	}

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $Layout->register_function('memberhome_href', array(&$this,'tpl_href'));
        
        parent::prepare($Layout, $Frontend);
    }

	public function render( SK_Layout $Layout )
	{
		$Layout->assign('username', SK_HttpUser::username());

		return parent::render($Layout);
	}

	public function tpl_href( array $params = array() )
	{
		if ( !SK_HttpUser::is_authenticated() )
		{
			return '';
		}
		
		if ( !($unit = trim($params['unit'])) ) return '';
		
		$lang_section = SK_Language::section('components.member_home');
		
		$class =  isset($params['class']) ? 'class="'.$params['class'].'"' : 'class="bold"';
		
		
		switch ( $params['unit'] )
		{
			case 'match':
			    if ( app_Features::isAvailable(51) )
                {			    
    				$_output = '<div class="memhome_link">';			
    				$_output.= '<a href="'.SK_Navigation::href('match_list').'" '.$class.'>'.$lang_section->text('href_my_match').'</a>';
    				$_output.= ' ('.app_ProfileList::CountProfileMatch(SK_HttpUser::profile_id()).')';
    				$_output.= '</div>';
                }
				break;
				
			case 'mailbox':
			    $conv = app_MailBox::getProfileConversations(SK_HttpUser::profile_id(), 'inbox');
				$_output = '<div class="memhome_link">';
				$_output.= '<a href="'.SK_Navigation::href('mailbox').'" '.$class.'>'.$lang_section->text('href_my_mailbox').'</a>';
				$_output.= ' ('.app_MailBox::newMessages(SK_HttpUser::profile_id()).'/'.$conv['total'].')';
				$_output.= '</div>';
				break;
			
			case 'bookmark':
				if ( app_Features::isAvailable(18) )
				{
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('bookmark_list').'" '.$class.'>'.$lang_section->text('href_my_hotlist').'</a> ('.app_Bookmark::countHotList(SK_HttpUser::profile_id()).')';
					$_output.= '</div>';
				}
				break;

			case 'speed_dating_bookmark':
                if ( app_Features::isAvailable(53) )
                {
                    $speed_dating_bookmark_list = app_EventService::BookmarkSpeedDatingList(SK_HttpUser::profile_id());
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('event_speed_dating_bookmark_list').'" '.$class.'>'.$lang_section->text('href_my_speed_dating_bookmark_list').'</a> ('.$speed_dating_bookmark_list['total'].')';
					$_output.= '</div>';
                }
				break;

			case 'blocklist':
				if ( app_Features::isAvailable(19) )	
				{
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('profile_block_list').'" '.$class.'>'.$lang_section->text('href_my_blocklist').'</a>';
					$_output.= ' ('.app_Bookmark::countBlockList(SK_HttpUser::profile_id()).')';
					$_output.= '</div>';
				}
				break;
				
			case 'friendlist':
				if ( app_Features::isAvailable(14) )
				{
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('profile_friend_list').'" '.$class.'>'.$lang_section->text('href_my_friendlist').'</a>';
					$_output.= ' ('.app_FriendNetwork::countFriends(SK_HttpUser::profile_id()).')';
					$_output.= '</div>';
				}
				break;
				
			case 'birthday_list':
				$_output = '<div class="memhome_link">';
				$_output.= '<a href="'.SK_Navigation::href('profile_birthday_list').'" '.$class.'>'.$lang_section->text('href_my_birthday_list').'</a>';
				$_output.= ' ('.app_ProfileList::BirthdayList('today', true).')';
				$_output.= '</div>';
				break;
				
			case 'location_list':
				$_output = '<div class="memhome_link">';
				$distance = app_NeighbourhoodList::getDefaultDistance(SK_HttpUser::profile_id());
				$mode = app_NeighbourhoodList::getProfileSelectedLocationforNeigh(SK_HttpUser::profile_id());
				$_output.= '<a href="'.SK_Navigation::href('profile_location_list').'?mode='.app_NeighbourhoodList::getProfileSelectedLocationforNeigh(SK_HttpUser::profile_id()).( $distance ? '&distance='.$distance : '' ).'" '.$class.'>'.$lang_section->text('href_my_location_list').'</a>';
				$_output.= ' ('.app_NeighbourhoodList::getList($mode , SK_HttpUser::profile_id(), true, ($distance ? $distance : false) ).')';
				$_output.= '</div>';
				break;
				
			case 'who_checked_me_out':
				if ( app_Features::isAvailable(29) ) 
				{
					$start_stamp = mktime(0,0,0, date('m'), 0, date('Y'));
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('who_checked_me_out').'" '.$class.'>'.$lang_section->text('href_my_who_checked_me_out') . "</a>";
					$_output.= ' ('.app_ProfileViewHistory::getViewerProfilesCount(SK_HttpUser::profile_id(), $start_stamp, time()).')';
					$_output.= '</div>';
				}
				break;
				
			case 'new_members_list':
				$_output = '<div class="memhome_link">';
				$_output.= '<a href="'.SK_Navigation::href('profile_new_members_list').'" '.$class.'>'.$lang_section->text('href_my_new_members_list').'</a>';
				$_output.= ' ('.app_ProfileList::NewMembersList(true).')';
				$_output.= '</div>';
				break;	

			case 'moderation_list':
				if ( SK_HttpUser::isModerator() )
				{
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('moderator').'" '.$class.'>'.$lang_section->text('href_moderation_list').'</a>';
					$_output.= ' ('. (app_BlogService::stFindPostsForModerationCount() + app_EventService::stFindEventsToModerateCount() + app_ClassifiedsItemService::stGetItemsToApproveCount()) .')';
					$_output.= '</div>';
				}
				break;
			case 'my_groups_list':
				if ( app_Features::isAvailable(38) )
				{
					$groups = app_Groups::getGroupsProfileCreated(SK_HttpUser::profile_id());
					$_output = '<div class="memhome_link">';
					$_output.= '<a href="'.SK_Navigation::href('groups', array('owner_id' => SK_HttpUser::profile_id())).'" '.$class.'>'.$lang_section->text('href_my_groups_list').'</a>';
					$_output.= ' ('. ($groups['total']) .')';
					$_output.= '</div>';
				}
				break;
		}
		
		return $_output;
	}
}
