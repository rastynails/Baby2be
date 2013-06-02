<?php

class component_ProfileStatus extends SK_Component
{
	/**
	 * User profile id.
	 *
	 * @var integer
	 */
	private $profile_id;

	/**
	 * Constructor.
	 *
	 * @param integer $params['profile_id'] optional
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('profile_status');

		if ( isset($params['profile_id']) && (
			$profile_id = intval($params['profile_id'])
		) ) {
			$this->profile_id = $profile_id;
		}
		elseif ( SK_HttpUser::is_authenticated() ) {
			$this->profile_id = SK_HttpUser::profile_id();
		}
		else {
			// no $params `profile_id` & user not authenticated
			$this->annul();
		}
	}

	/**
	 * Preparing component.
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('ProfileStatus');
		$this->frontend_handler = $handler;

		$profile_status = app_Profile::getFieldValues($this->profile_id, 'status');

		if ( $profile_status == 'active' ) {
			$toggle_btn_label = SK_Language::text('components.profile_status.set_on_hold');
		}
		elseif ( $profile_status == 'on_hold' ) {
			$toggle_btn_label = SK_Language::text('components.profile_status.set_active');
		}
		else { // profile suspended
			return false;
		}

		$status_label = SK_Language::section('components.profile_status.status')->text($profile_status);

		$handler->display($toggle_btn_label, $status_label, $profile_status);
	}

	/**
	 * Rendering component.
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render( SK_Layout $layout )
	{
		// getting membership info
		$membership = app_Membership::ProfileCurrentMembershipInfo($this->profile_id);
		if ( isset($membership['expiration_stamp']) ) {
			$membership['expiration_time'] = SK_I18n::period(time(), $membership['expiration_stamp']);
		}
		$layout->assign('membership', $membership);

		// getting user status
		$user_status = app_Profile::getUserStatus($this->profile_id);
        $user_status = app_TextService::stOutputFormatter($user_status);
        $user_status = SK_I18n::getHandleSmile( $user_status );
		$layout->assign('user_status', $user_status);

	    if ( app_Features::isAvailable(44) )
        {
            $layout->assign('show_balance', true);
            $layout->assign('credits_balance', app_UserPoints::getProfilePointsBalance($this->profile_id));
        }

		return parent::render($layout);
	}

	/**
	 * Toggle profile status ajax callback.
	 *
	 * @param string $params->curr_status
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function toggleProfileStatus( SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler )
	{
		$params->validate(array(
			'curr_status'	=>	'string'
		), true);

		$new_status = ($params->curr_status == 'active') ? 'on_hold' : 'active';

		$status_updated = app_Profile::setFieldValue(SK_HttpUser::profile_id(), 'status', $new_status);

		if ( $status_updated ) {
			$btn_label = SK_Language::text('components.profile_status.set_'.$params->curr_status);
			$status_label = SK_Language::section('components.profile_status.status')->text($new_status);
			$handler->display($btn_label, $status_label, $new_status);
		}
	}
}

