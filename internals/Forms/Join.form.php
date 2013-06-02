<?php

class form_Join extends form_FieldForm   
{
	
	/**
	 * Current step
	 *
	 * @var int
	 */
	public $step;
	
	
	/**
	 * Link to join form session
	 *
	 * @var array
	 */
	protected $session;
	
	/**
	 * Form constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('profile_join');
	}
	
	
	/**
	 * Overloaded method of parent form.
	 * Returns field list of current step
	 *
	 * @return array
	 */
	protected function fields()
	{
		$fields = app_FieldForm::formFields(app_FieldForm::FORM_JOIN);
		return $fields;
	}
	
	
	public function setup(){
		if (app_JoinProfile::hasCaptcha()) {
			$field = new field_captcha();
			parent::registerField($field);
		}
		parent::setup();
	}
	
	public function addField($field_name){
		
		if (!array_key_exists($field_name, $this->fields))
		{
			if ($field_name=='username') {
				$field = new field_join_username();
				parent::registerField($field);
			}
			
			if ($field_name=='email') {
				$field = new field_join_email();
				parent::registerField($field);
			}
		}
		
		parent::addField($field_name);
	}
	
	protected function actionsPrepare()
	{
		$steps = app_FieldForm::formSteps(app_FieldForm::FORM_JOIN);
		
		$sexes = SK_ProfileFields::get("sex")->values;
		
		$this->addAction($steps[0] . "___", "formAction_JoinProfile");
		
		foreach ($sexes as $sex) {
			foreach ($steps as $step) {
				$this->addAction($step . "___" . $sex, "formAction_JoinProfile");
			}
		}
	}
}




class formAction_JoinProfile extends formAction_FieldForm
{
	public $step;
	
	public $reliant_field_value;
	
	public function __construct($name, $uniqid = null) {
		
		parent::__construct($name, $uniqid);
	}
		
	public function process_fields($uniqid)
	{
		list($step, $sex) = explode('___', $uniqid);
		$this->step = $step;
		$this->reliant_field_value = $sex;
		$sex = (isset($sex) && strlen($sex)) ? $sex : false;
		
		$all_fields = app_FieldForm::formStepFields(app_FieldForm::FORM_JOIN , $sex);
		$fields = $all_fields[$step];
		
		if (app_JoinProfile::hasCaptcha($step)) {
			$fields[] = "captcha";
		}
		
		return $fields;
	}
	
	public function checkData(array $data, SK_FormResponse $response, SK_Form $form )
	{
		if (SK_HttpRequest::isIpBlocked()) {
			$response->addError(SK_Language::text('%forms.sign_in.error_msg.blocked_member'));
		}
		
		if (isset($data['username']))
		{
			if ( app_Username::isUsernameInRestrictedList( $data['username'] ) ) {
				$error_msg = SK_Language::text('%forms._fields.username.errors.is_restricted');
				$response->addError($error_msg,'username');
			} else {
			
				$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE."` WHERE `username`='?'", $data['username']);
				if (SK_MySQL::query($query)->fetch_cell()) {
					$error_msg = SK_Language::text('%forms._fields.username.errors.already_exists');
					$response->addError($error_msg,'username');
				}
			}
		}
					
		if (isset($data['email']))
		{
			$query = SK_MySQL::placeholder("SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `email`='?'",$data['email']);
			if (SK_MySQL::query($query)->fetch_cell()) {
				$error_msg = SK_Language::text('%forms._fields.email.errors.already_exists');
				$response->addError($error_msg,'email');
			}
		}
		
		parent::checkData($data, $response, $form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		if (!intval($this->reliant_field_value)) {
			$reliant_field_value = isset($post_data["sex"]) ? $post_data["sex"] : false;
			app_JoinProfile::setSteps($reliant_field_value);
		}
		
		foreach ($post_data as $key => $value) {
			app_JoinProfile::setValue($key, $value);
		}
	
		if (app_JoinProfile::lastStep($this->step)) 
		{
                    if ( app_Security::getInstance()->check(app_JoinProfile::getValue('email')) )
                    {
                        app_JoinProfile::clearSession();
                        $response->redirect( SITE_URL . 'security.php' );
                        return;
                    }
			if (app_JoinProfile::joinProfile()) {
				$redirect_document = SK_Config::section('navigation')->Section('settings')->join_document_redirect;
				app_JoinProfile::clearSession();
				$response->redirect(SK_Navigation::href($redirect_document));	
			}
		} 
		else {
			app_JoinProfile::setStepProcessed($this->step);
			$response->exec('window.location.reload()');
		}
		
	}
	
}
