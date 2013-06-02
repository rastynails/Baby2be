<?php

class component_Join extends component_FieldForm
{
	private $reliant_field_value;
	
	private $all_fields = array();
	
	private $step = 1;
	
	private $suspended = false;
	
	public function __construct( array $params = null )
	{
		parent::__construct('join');
		$this->suspended = SK_Config::section('site.additional.profile')->suspend_registration;
	}
	
	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend){
		
		if ($this->suspended) {
			$this->tpl_file = 'suspend.tpl';
			return parent::prepare($Layout, $Frontend);
		}
		
		app_Invitation::controlRegistrationAccess();
		
		if (SK_HttpRequest::isIpBlocked()) {
			$Frontend->onload_js(
				'SK_drawError('.json_encode(SK_Language::text('%forms.sign_in.error_msg.blocked_member')).');'
			);
		}
			
		$this->step = app_JoinProfile::step();
		
		$this->reliant_field_value = app_JoinProfile::getValue("sex");
		
		return parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		if ($this->suspended) {
			return parent::render($Layout);
		}
		
		$Layout->assign('step', $this->step);
		
		$Layout->assign("has_captcha", app_JoinProfile::hasCaptcha($this->step));
		
		return parent::render($Layout);
	}
	
	public function fields()
	{
		if (!count($this->all_fields)) {
			$this->all_fields = app_FieldForm::formStepFields(app_FieldForm::FORM_JOIN , $this->reliant_field_value ? $this->reliant_field_value : false );	
		}
		$fields = $this->all_fields[$this->step];
		return $fields;
	}
	
	
	public function handleForm(SK_Form $Form) 
	{
		$fields = $this->fields();
		foreach ($fields as $field) {
			
			$value = app_JoinProfile::getValue($field);
			
			if (isset($value)) {
				$Form->fields[$field]->setValue($value);	
			}
		}
		
		$sex = app_JoinProfile::getValue("sex");
		$Form->selectAction($this->step . "___" . $sex);
		
		parent::handleForm($Form);
	}
	
	public static function clearCompile() {
		$cmp = new self();
		return $cmp->clear_compiled_tpl();
	}
}
