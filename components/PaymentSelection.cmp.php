<?php

class component_PaymentSelection extends SK_Component
{
	private $profile_sex;
		
	/**
	 * Component PaymentSelection constructor.
	 *
	 * @return component_PaymentSelection
	 */
	public function __construct( array $params = null )
	{
		if (!SK_HttpUser::profile_id()) {
			$this->annul();
		} else {
			/* --- Delete expired profiles memberships --- */
			app_Membership::checkSubscriptionTrialMembership(SK_HttpUser::profile_id());
		}	
		parent::__construct('payment_selection');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('PaymentSelection');
		$this->frontend_handler = $handler;
		
		$for_trial = app_Membership::checkTrialMembershipAvailability( SK_HttpUser::profile_id() );

		$handler->construct($for_trial);
		
		if (isset($_SESSION['messages']))
		{
			foreach ( $_SESSION['messages'] as $msg )
				$Frontend->onload_js('SK_drawMessage("'.$msg['message'].'","'.$msg['type'].'");');
			
			unset($_SESSION['messages']);
		}
	}
	
	public static function clearCompile($tpl_file = null, $compile_id = null) {
		$cmp = new self;
		return $cmp->clear_compiled_tpl();
	}
		
	private function getProfileAllMemberships()
	{
		$return = array();
		$membership_list = app_Membership::getProfileAllMemberships( SK_HttpUser::profile_id() );

		if ( count( $membership_list ) == 1 )
			return $membership_list;
		
		foreach ( $membership_list as $membership )
		{
			$flag = false;
			foreach ( $return as $key => $value )
				if ( $value['membership_type_id'] == $membership['membership_type_id'] )
				{
					if ( $value['limit'] == 'limited' )
					{
						$return[$key]['start_stamp'] = ( $value['start_stamp'] < $membership['start_stamp'] )? $value['start_stamp'] : $membership['start_stamp'];
						$return[$key]['expiration_stamp'] = ( $value['expiration_stamp'] < $membership['expiration_stamp'] )?
							$value['expiration_stamp'] + $membership['expiration_stamp'] - $membership['start_stamp'] :
							$membership['expiration_stamp'] + $value['expiration_stamp'] - $value['start_stamp'];
					}
					$flag = true;
				}
				if ( !$flag && ( $membership['type'] == 'credits' || $membership['limit'] == 'limited' ) )
					$return[] = $membership;
		}

		foreach ( $return as $key => $value )
		{
			if ( $value['start_stamp'] )
				$return[$key]['start_stamp'] = $value['start_stamp'];
			
			if ( $value['expiration_stamp'] )
				$return[$key]['expiration_stamp'] = $value['expiration_stamp'];
		}
		return $return;
	}
	
		
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id(); 
		
		$membership = app_Membership::profileCurrentMembershipInfo($profile_id);
		
		$membership['expiration_time'] = isset($membership['expiration_stamp']) ? SK_I18n::period(time(), $membership['expiration_stamp']) : null;
		$Layout->assign('membership', $membership);

		$this->profile_sex = app_Profile::getFieldValues( $profile_id, 'sex' );
		$mTypes = app_Membership::getAvailableMembershipTypes($this->profile_sex);
		
		foreach ( $mTypes as $type )
		{
            $types[$type['membership_type_id']] = $type;
		}

		$memberships = $this->getProfileAllMemberships();
		$status = $memberships[0] ? $memberships[0] : app_Membership::profileCurrentMembershipInfo($profile_id);
		$Layout->assign_by_ref('status', $status);
		
		$type_plans = array();
		
		foreach ($types as $type_key => $type)
		{
			$id = $type['membership_type_id'];
			if ( $type['type'] == 'trial' || ($type['type'] == 'subscription' && $type['limit'] == 'limited') )
			{
				$plans = app_Membership::getMembershipTypePlan($id);
								
				if ( count($plans) > 0 ) {
					$i = 0;
					foreach ( $plans as $key => $plan )
					{
						if ( $type['type']=='subscription' && $type['limit']=='limited' && $plan['is_recurring'] == 'y' )
							$plan_structure = 'recurring';
						elseif ( $type['type']=='credits' )
							$plan_structure = 'credits';
						elseif ( $type['type']=='trial' && $plan['price'] == 0 )
						{
						    if ( $id != $status['membership_type_id'] && app_Membership::checkIfProfileClaimedTheMembership( $profile_id, $id, 'type' ) == -3 )
						    {
						        unset($types[$id]);
                                continue(2);
						    }
							$plan_structure = 'free_trial';
							$type_plans[$type['membership_type_id']]['free'] = 1;
						}
						else
							$plan_structure = 'single';
						
						$plans[$key]['label'] = app_Membership::getFormatedPlan( $plan['period'], $plan['units'], $plan['price'], $plan_structure);
						if ( !$i && !$first_plan_id )
							$first_plan_id = $plan['membership_type_plan_id'];
						$i++;
					}
				}
				$type_plans[$id]['count'] = count($plans);
				$type_plans[$id]['type'] = $type['type'];
				$type_plans[$id]['membership_type_id'] = $id;
				$type_plans[$id]['plans'] = $plans;
			}
			else {
				$type_plans[$id]['membership_type_id'] = $id;
				$type_plans[$id]['count'] = 0;
				$type_plans[$id]['plans'] = null;
			}
		}
		
        $show_chart = SK_Config::Section('site')->Section('additional')->Section('subscribe')->show_permissions_diagram;
        $Layout->assign('show_chart', $show_chart);
        if ( $show_chart )
        {
            $diag = app_Membership::getPermissionDiagram($types);
            $Layout->assign_by_ref('diag', $diag);
        }
        
		$Layout->assign_by_ref('types', $types);
		$Layout->assign_by_ref('type_plans', $type_plans);
		$Layout->assign('col_width', round(580 / count($type_plans)) );
		$Layout->assign_by_ref('default_plan',$first_plan_id);
		$providers = app_Finance::GetPaymentProviders();
		$Layout->assign_by_ref('providers', $providers);
		
		$membership_type_id = app_Profile::getFieldValues($profile_id, 'membership_type_id');
		
		if ( app_Features::isAvailable(32) )
			$Layout->assign('sms_services', SK_Language::text('%components.important_tips.tip_sms_services', array('url' => SK_Navigation::href('sms_services'))));	
		
		if ( app_Features::isAvailable(44) )
		{
		    $Layout->assign('show_balance', true);
            $Layout->assign('credits_balance', app_UserPoints::getProfilePointsBalance($profile_id));
		}
		
		$unsub = app_Finance::getUnsubscribeBtn($profile_id);
		$Layout->assign('unsub', $unsub);
		
		$Layout->assign('coupons_enabled', app_Features::isAvailable(65));
					
		return parent::render($Layout);
	}
	
}

