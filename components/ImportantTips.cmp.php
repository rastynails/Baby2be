<?php

class component_ImportantTips extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('important_tips');
		
		//$this->cache_lifetime=360;
	}

	public function render( SK_Layout $Layout )
	{
		
		$config = SK_Config::section('site')->Section('additional')->Section('tips');	
		
		$profile_info = app_Profile::getFieldValues( SK_HttpUser::profile_id() , array( 'has_photo', 'has_media', 'membership_type_id' ) );
		
		$tips =array();
		
		if ( $config->show_upload_photo_tip && ( $profile_info['has_photo'] == 'n' ) )
			$tips[] = SK_Language::text('%components.important_tips.tip_upload_photo' , array('url'=>SK_Navigation::href('profile_photo_upload')));

		if ( $config->show_upload_media_tip && ( $profile_info['has_media'] == 'n' ) )
			$tips[] = SK_Language::text('%components.important_tips.tip_upload_media', array('url'=>SK_Navigation::href('my_video')) );
			
		if ( $config->show_upgrade_tip && ( $profile_info['membership_type_id'] == SK_Config::section('membership')->default_membership_type_id ) )
			$tips[] = SK_Language::text('%components.important_tips.tip_upgrade_membership', array('url'=>SK_Navigation::href('payment_selection')));				
			
		$sms_service = '24hour_membership'; 
		if ( $config->show_sms_tip && app_Features::isAvailable(32) && app_SMSBilling::serviceIsActive($sms_service) && ( $profile_info['membership_type_id'] != app_SMSBilling::getServiceField($sms_service, 'membership_type_id') ) )
			$tips[] = SK_Language::text('%components.important_tips.tip_sms_services', array('url'=>SK_Navigation::href('sms_services')));	
												
		/*if ( $config->show_default_tip )			
			$tips[] = SK_Language::text('%components.important_tips.tip_default_tip', array( 'site_name' => 'Site Name TODO','url'=>SK_Navigation::href('press')) );	
		*/			
			
		if ($config->show_poll_tip && $pollId = intval(app_Poll::getSingleToVote(SK_HttpUser::profile_id())) )
		{
			$tips[] = SK_Language::text('%components.important_tips.tip_poll', array( 'question' => @SK_Language::section('polls')->text("poll_{$pollId}_question"), 
				'url'=>SK_Navigation::href('polls', array('id'=>$pollId))) );
		}				
			
		if (!count($tips)) {
			return false;
		}
		
		$Layout->assign( 'tips', $tips );
		
		return parent::render($Layout);
	}
	
}
