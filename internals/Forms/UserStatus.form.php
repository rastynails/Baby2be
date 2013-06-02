<?php

class form_UserStatus extends SK_Form
{

	public function __construct() {
		parent::__construct('user_status');
	}

	public function setup()
	{
		$user_status = new fieldType_text('status');
		$user_status->maxlength = 40;
		parent::registerField($user_status);

		parent::registerAction('form_UserStatus_update');
	}

}


class form_UserStatus_update extends SK_FormAction
{
        const EMAIL_PATTERN = '/([\w\-\.\+\%]+)@((?:[A-Za-z0-9\-]+\.)+[A-Za-z]{2,})/';

	/**
	 * Constructor.
	 */
	public function __construct() {
            parent::__construct('update');
	}


	public function setup( SK_Form $form ) {
		//$this->required_fields = array('');
		parent::setup($form);
	}

        public function checkData(array $data, SK_FormResponse $response, SK_Form $form)
        {
            parent::checkData($data, $response, $form);

            if ( preg_match(self::EMAIL_PATTERN, $data['status']) )
            {
                $response->addError(SK_Language::text('components.profile_status.email_error_message'));
                return;
            }


            $service = new SK_Service("change_user_status");
            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $response->addError($service->permission_message["message"]);
                return;
            }

            $service->trackServiceUse();
        }

	/**
	 * Update user status.
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function process( array $data, SK_FormResponse $response, SK_Form $form )
	{
		if ( !SK_HttpUser::is_authenticated() ) {
			throw new SK_HttpRequestException(SK_HttpRequestException::AUTH_REQUIRED);
		}

		$status = empty($data['status']) ? '' : $data['status'];

		if (
			app_Profile::updateUserStatus(SK_HttpUser::profile_id(), $status)
		) {

		    $status = app_TextService::stHandleSmiles(SK_Language::htmlspecialchars($status));
			$status_value = empty($status) ? '' : app_Profile::username().' '.$status;
			$response->exec(
				'this.ownerComponent.$("#user_status_curr_value").html('.json_encode($status_value).');'
			);
		}

		// emptying input value
		$response->exec('this.$("#status").val("");');
	}
}

