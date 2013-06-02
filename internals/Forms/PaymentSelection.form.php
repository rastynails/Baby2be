<?php

class form_PaymentSelection extends SK_Form
{
    public function __construct()
    {
        parent::__construct('payment_selection');
    }

    public function setup()
    {
        $type_id = new fieldType_hidden('type_id');
        parent::registerField($type_id);

        $plan_id = new fieldType_custom_select('plan_id');
        parent::registerField($plan_id);

        $provider = new fieldType_select('provider_id');
        $provider->setType('select');
        parent::registerField($provider);

        $coupon = new fieldType_text('coupon');
        parent::registerField($coupon);
        
        $payment_providers = app_Finance::GetPaymentProviders();
        foreach ($payment_providers as $prov_id)
        $providers[] = $prov_id['fin_payment_provider_id'];
        	
        $provider->setValues($providers);

        parent::registerAction('formPaymentSelection_Checkout');

        if (isset($_SESSION['order_item']))
        unset($_SESSION['order_item']);
    }

    public function renderStart( array $params = null )
    {
        if ( isset($params['provider_id']) ) {
            $this->getField('provider_id')->setValue($params['provider_id']);
        }

        if ( isset($params['plan_id']) ) {
            $type = app_Membership::getMembershipTypeInfoByPlanId($params['plan_id']);
            $this->getField('type_id')->setValue($type['membership_type_id']);

            $this->getField('plan_id')->setValue($params['plan_id']);
        }

        return parent::renderStart($params);
    }
}

class formPaymentSelection_Checkout extends SK_FormAction
{
    public $provider_id;
    public $plan_id;
    public $is_recurring;

    public function __construct()
    {
        parent::__construct('checkout');
    }

    public function setup( SK_Form $form )
    {
        $this->required_fields = array('type_id', 'plan_id', 'provider_id');

        parent::setup($form);
    }
    /**
     * Checks post data
     * Claims membership if its type is trial
     *
     * @param SK_FormResponse $response
     * @param int $type_id
     * @param int $plan_id
     * @param int $provider_id
     * @return int
     * <ul>Possible return values:
     * <li>  2 : membership claimed</li>
     * <li>  1 : all params are valid, mamber can pay</li>
     * <li> -1 : plan or type undefined</li>
     * <li> -2 : provider is unavailable</li>
     * <li> -3 : provider needs synchronizing and is not configured</li>
     * <li> -4 : membership type is not available for sex</li>
     * <li> -5 : an error occured during claim</li>
     * <li> -6 : claim is under admin's consideration</li>
     * <li> -7 : profile already claimed the trial membership</li>
     * </ul>
     */
    private function checkSubscribeData( SK_FormResponse $response, $type_id, $plan_id, $provider_id )
    {
        $type_id = intval($type_id);
        $plan_struct = array();
        $plan_struct = explode('_',$plan_id);
        $plan_id = intval($plan_struct[1]);

        $plan = app_Membership::getMembershipTypePlanInfo($plan_id);
        $type = app_Membership::getMembershipTypeInfoByPlanId($plan_id);

        if( !$plan || !$type )
        return -1;
        	
        $provider_id = intval($provider_id);
        $provider = app_Finance::GetPaymentProviderInfo( $provider_id );
        if( !$provider || $provider['is_available'] != 'y' )
        return -2;

        elseif ( $provider['is_required_plan_synchronizing'] == 'y' && !app_Finance::isPlanConfiguredForProvider($plan_id, $provider_id) && !($type['type'] == 'trial' && (int)$plan['price'] == 0) )
        return -3;

        $profile_id = SK_HttpUser::profile_id();
        $profile_sex = app_Profile::getFieldValues( $profile_id, 'sex' );

        if( !( intval($type['available_for']) & intval($profile_sex) ) )
        return -4;

        $this->provider_id = $provider_id;
        $this->plan_id = $plan_id;
        $this->is_recurring = $plan['is_recurring']=='y' ? 'y' : 'n';

        if ( $type['type'] == 'trial' )
        {
            switch ( app_Membership::checkIfProfileClaimedTheMembership( $profile_id, $type['membership_type_id'], 'type' ) )
            {
                case -1:
                    return -5;
                    break;
                case -2:
                    return -6;
                    break;
                case -3:
                    return -7;
                    break;
                default:
                    if( $plan['price'] == 0 )
                    {
                        if ( app_Membership::claimTrialMembership( $profile_id, $plan_id ) )
                        return 2;
                        	
                        else
                        return -5;
                    }
                    else return 1;
            }
        }
        else return 1;
    }

    /**
     * Process subscribe form
     *
     *	$__SALE_INFO_ARR => array
     *	(
     *		'custom' - hash code,
     *		'sale_info_id' - id of the transaction,
     *		'provider_name' - provider base name,
     *		'provider_info' - array - provider info,
     *		'currency' - active currency,
     *		'membership_type_id',
     *		'membership_type_name',
     *		'membership_type_plan_id',
     *		'price' - amount of the transaction,
     *		'period' - period of the plan,
     *		'units' - units of the period,
     *		'is_recurring' - 'y' | 'n',
     *		'membership_description',
     *		'provider_plan_id' - plan id,
     *	)
     */
    public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
    {
        $process_result = $this->checkSubscribeData( $response, $post_data['type_id'], $post_data['plan_id'], $post_data['provider_id'] );

        switch ( $process_result )
        {
            case 1:
                $profile_id = SK_HttpUser::profile_id();

                $code = trim($post_data['coupon']);
                if ( strlen($code) )
                {
                    if ( !app_CouponCodes::codeIsValid($code) )
                    {
                        $response->addError(SK_Language::text('%forms.payment_selection.error_msg.code_not_valid'));
                        return;
                    }
                    
                    if ( !SK_Config::section('coupon_codes')->get('allow_recurring') )
                    {
                        $plan = app_Membership::getMembershipTypePlanInfo($this->plan_id);
                        if ( $plan['is_recurring'] == 'y' )
                        {
                            $response->addError(SK_Language::text('%forms.payment_selection.error_msg.code_recurring_membership'));
                            return;
                        }
                    }
                    
                    if ( !SK_Config::section('coupon_codes')->get('allow_reuse') && app_CouponCodes::codeUsed($profile_id, $code) )
                    {
                        $response->addError(SK_Language::text('%forms.payment_selection.error_msg.code_already_used'));
                        return;
                    }
                    
                    $coupon = app_CouponCodes::getCode($code);
                    $ms = app_Membership::getMembershipTypeInfo($coupon['membership_type_id']);
                    $m_title = SK_Language::text('%membership.types.'.$coupon['membership_type_id']);
                                        
                    if ( !$ms )
                    {
                        $response->addError(SK_Language::text('%forms.payment_selection.error_msg.code_not_valid'));
                        return;
                    }
                    
                    if ( $coupon['membership_type_id'] != $post_data['type_id'] )
                    {
                        $response->addError(SK_Language::text('%forms.payment_selection.error_msg.membership_mismatch', array('membership' => $m_title)));
                        return;
                    }
                    
                    if ( $coupon['percent'] == 100 )
                    {
                        $res = app_CouponCodes::useFullDiscountCoupon($profile_id, $code, $coupon['membership_type_id'], $this->plan_id);
                        if ( $res )
                        {
                            $response->addMessage(SK_Language::text('%forms.payment_selection.msg.coupon_successfully_used', array('membership' => $m_title)));
                            $response->exec('window.location.reload()');
                            return;
                        }
                    }
                    else 
                    {
                        $provider = app_Finance::GetPaymentProviderInfo($post_data['provider_id']);
                        if ( $provider['is_required_plan_synchronizing'] == 'y' )
                        {
                            $response->addError(SK_Language::text('%forms.payment_selection.error_msg.dynamic_pricing_required'));
                            return;
                        }
                        
                        $discount = floatval($coupon['percent']);
                    }
                }
                else 
                {
                    $discount = null;
                }
                
                $__SALE_INFO_ARR = app_Finance::FillSaleInfo(
                    $profile_id, $this->provider_id, $this->plan_id, $this->is_recurring, $discount, $code
                );
                	
                if ( isset($__SALE_INFO_ARR) )
                {
                    $__SALE_INFO_ARR['membership_type_name'] =
                    SK_Language::section('membership.types')->text($__SALE_INFO_ARR['membership_type_id']);

                    $__SALE_INFO_ARR['membership_description'] = app_Membership::getMembershipDescription($__SALE_INFO_ARR);
                    	
                    $__SALE_INFO_ARR['provider_plan_id'] =
                    app_Finance::getProviderPlanIdForMembershipPlan($this->provider_id,	$this->plan_id);
                     
                    $__SALE_INFO_ARR['site_name'] = SK_Config::section('site.official')->get('site_name');
                    
                    $_SESSION['__SALE_INFO_ARR'] = $__SALE_INFO_ARR;

                    $_SESSION['order_item'] = 'membership';

                    $response->exec('window.location.href="' . URL_CHECKOUT . $__SALE_INFO_ARR['provider_name'] . '/pre_checkout.php";');
                }
                else
                $response->exec('window.location.reload()');

                break;
            case 2:
                $response->addMessage(SK_Language::section('forms.payment_selection.msg')->text('successfully_claim'));
                break;
            case -1:
                $response->addError(SK_Language::section('forms.payment_selection.error_msg')->text('try_again'));
                break;
            case -2:
                $response->addError(SK_Language::section('forms.payment_selection.error_msg')->text('try_again'));
                break;
            case -3:
                $response->addMessage(SK_Language::section('forms.payment_selection.error_msg')->text('plan_not_configured'));
                break;
            case -4:
                $response->addError(SK_Language::section('forms.payment_selection.error_msg')->text('not_available'));
                break;
            case -5:
                $response->addError(SK_Language::section('forms.payment_selection.error_msg')->text('error_claim'));
                break;
            case -6:
                $response->addMessage(SK_Language::section('forms.payment_selection.error_msg')->text('consideration_claim'));
                break;
            case -7:
                $response->addMessage(SK_Language::section('forms.payment_selection.error_msg')->text('again_claim'));
                break;
        }
    }

}

