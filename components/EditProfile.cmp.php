<?php

class component_EditProfile extends component_FieldForm
{
	private $step = 1;

	private $all_fields = array();

	public $profile_id;

        public $completeMode = false;

	public function __construct( array $params = null )
	{
		parent::__construct('edit_profile');
	}


	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->profile_id = SK_HttpUser::profile_id();
		$this->step = isset(SK_HttpRequest::$GET["step"]) ? SK_HttpRequest::$GET["step"] : 1;

                $this->completeMode = !empty(SK_HttpRequest::$GET["complete"]);

		parent::prepare($Layout, $Frontend);
	}

	public function fields()
	{
		$reliant_value = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'sex');
		$this->all_fields = app_FieldForm::formStepFields(app_FieldForm::FORM_EDIT , $reliant_value );
		return $this->all_fields[$this->step];
	}

	public function render( SK_Layout $Layout, SK_Frontend $Frontend )
	{
                if ( $this->completeMode )
                {
                    $requiredFields = app_FieldForm::getRequredFields( $this->profile_id );
                    $requiredSteps = array();
                }

                //printArr($requiredFields); exit;
		$pages = array();
                $total_fields = array();
		foreach (array_keys($this->all_fields) as $step)
                {
                    if ( $this->completeMode )
                    {
                        $rf = array_intersect($this->all_fields[$step], $requiredFields);

                        if ( !empty($rf) && !app_Profile::isFieldsCompleted($this->profile_id, $rf) )
                        {
                            SK_HttpRequest::redirect(SK_Navigation::href('profile_edit', array(
                                'step' => $step
                            )));
                        }
                    }

                    $pages[$step]['label'] = SK_Language::section('profile_fields.page_edit')->text($step);
                    $pages[$step]['href'] = SK_Navigation::href('profile_edit', array('step'=>$step));
                    $pages[$step]['active'] = ($step==$this->step);
                    $pages[$step]['class'] = 'step_' . $this->step;

                    $total_fields = array_merge( $total_fields, $this->all_fields[$step] );
		}

		$Layout->assign('pages',$pages);

		$fbSynch = false;
		if ( SK_Config::section('facebook_connect')->allow_synchronize )
		{
            $fbSynch = new component_FBCButton(array('type' => 'synchronize'));
		}
		$Layout->assign('fbcButton', $fbSynch);

                $fieldsValues = app_Profile::getFieldValues( $this->profile_id, $total_fields );

                $handler = new SK_ComponentFrontendHandler( 'EditProfile' );
		$this->frontend_handler = $handler;
		$handler->construct(array(
                    'fieldsCount' => count($this->fields),
                    'complatedFieldsCount' => count(array_filter($fieldsValues)),
                    'totalFieldsCount' => count($total_fields)
                ));

		return parent::render($Layout);
	}

	public function getValue($field_name)
	{
		if ($field_name =='location') {
			return app_Profile::getFieldValues($this->profile_id, array('country_id','zip','state_id','city_id','custom_location'));
		}

		if (strpos($field_name, 're_')===0)
        {
			$field_name = substr($field_name,3);
		}

		return app_Profile::getFieldValues($this->profile_id, $field_name);
	}

	public function handleForm(SK_Form $Form)
	{
		$sex = app_Profile::getFieldValues($this->profile_id, 'sex');

		$Form->selectAction($this->step . "___" . $sex);

		foreach ($Form->fields as $name => $field) {

			$value = $this->getValue($name);
			try {
				if ($value) {
					$field->setValue($value);
				}
			} catch (SK_FormFieldValidationException $e){}

		}


		parent::handleForm($Form);
	}

}
