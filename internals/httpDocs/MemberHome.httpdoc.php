<?php

class httpdoc_MemberHome extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('member_home');
		
		$this->cache_lifetime = 5;
				
	}
	
	public function prepare( SK_Layout $Layout, &$display_params )
	{
		$Layout->register_function('memberhome_href', array(&$this,'tpl_href'));
				
		parent::prepare( $Layout, $display_params);
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('username', SK_HttpUser::username());
		
		return parent::render($Layout);
	}
	
	
	public function tpl_href( array $params = array())
	{
		if(!SK_HttpUser::is_authenticated())
			return '';
		
		if(!($unit = trim($params['unit']))) return '';
		
		$lang_section = SK_Language::section('components.memberhome');
		
		$class =  isset($params['class']) ? 'class="'.$params['class'].'"' : 'class="bold"';
		
		$_output = '<div class="memhome_link"><img height="25" width="25"/>';
		switch ( $params['unit'] )
		{
			case 'match':
							
				$_output.= '<a href="'.SK_Navigation::href( 'match_list' ).'" '.$class.'>'.$lang_section->text( 'href_my_match').'</a>';
				$_output.= '('.app_ProfileList::CountProfileMatch( SK_HttpUser::profile_id() ).')';
				break;
				
			case 'mailbox':
				$_output.= '<a href="'.SK_Navigation::href( 'mailbox' ).'" '.$class.'>'.$lang_section->text( 'href_my_mailbox').'</a>';
				
				$_mailbox_info = appMessages::getMailboxInfo( SK_HttpUser::profile_id() );
				$_output.= ' ('.$_mailbox_info['new_received'].'/'.$_mailbox_info['all'].')';
				break;
			
			case 'bookmark':
				//TODO if ( isFeatureAvailable( 18 ) ){
					$_output.= '<a href="'.SK_Navigation::href( 'bookmark_list' ).'" '.$class.'>'.$lang_section->text( 'href_my_hotlist').'</a>('.app_Bookmark::countHotList( SK_HttpUser::profile_id() ).')';
				//}
					
				break;
				
			case 'blocklist':
				//if ( isFeatureAvailable( 19 ) )				
				//{
					$_output.= '<a href="'.SK_Navigation::href( 'profile_block_list' ).'" '.$class.'>'.$lang_section->text( 'href_my_blocklist').'</a>';
					$_output.= '('.app_Bookmark::countBlockList( SK_HttpUser::profile_id() ).')';
				//}
				break;
				
			case 'friendlist':
				//if ( isFeatureAvailable( 14 ) )
				//{
					$_output.= '<a href="'.SK_Navigation::href( 'profile_friend_list' ).'" '.$class.'>'.$lang_section->text( 'href_my_friendlist').'</a>';
					$_output.= '('.app_FriendNetwork::countFriends( SK_HttpUser::profile_id() ).')';
				//}
				break;
				
			case 'birthday_list':
				
				$_output.= '<a href="'.SK_Navigation::href( 'profile_birthday_list' ).'" '.$class.'>'.$lang_section->text( 'href_my_birthday_list').'</a>';
				$_output.= ' ('.app_ProfileList::BirthdayList( 'today', true ).')';
				break;
				
			case 'location_list':
				$distance = app_NeighbourhoodList::getDefaultDistance(SK_HttpUser::profile_id());
				$mode = app_NeighbourhoodList::getProfileSelectedLocationforNeigh(SK_HttpUser::profile_id());
				$_output.= '<a href="'.SK_Navigation::href( 'profile_location_list' ).'?mode='.app_NeighbourhoodList::getProfileSelectedLocationforNeigh(SK_HttpUser::profile_id()).( $distance ? '&distance='.$distance : '' ).'" '.$class.'>'.$lang_section->text( 'href_my_location_list').'</a>';
				$_output.= ' ('.app_NeighbourhoodList::getList($mode , SK_HttpUser::profile_id(), true, ($distance ? $distance : false) ).')';
				break;
				
			case 'who_checked_me_out':
					$_output = '<a href="'.SK_Navigation::href( 'who_checked_me_out' ).'" '.$class.'>'.$lang_section->text( 'href_my_who_checked_me_out');
					$_output .= ' <b>('.appProfileViewedList::getViewerProfilesCount( SK_HttpUser::profile_id(), 0, time() ).')</b></a>';
				break;
				
			case 'new_members_list':
				
				$_output.= '<a href="'.SK_Navigation::href( 'profile_new_members_list' ).'" '.$class.'>'.$lang_section->text( 'href_my_new_members_list').'</a>';
				$_output.= ' ('.app_ProfileList::NewMembersList( true ).')';
				break;				                      				
		}
		$_output.= '</div>';
		return $_output;
	}
	
}
