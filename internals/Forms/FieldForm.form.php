<?php

abstract class form_FieldForm extends SK_Form
{
	public function __construct($form_name)
	{
		parent::__construct($form_name);

	}

	protected abstract function fields();

	/*
	protected function prepare_tpl_fields($fields)
	{


	}*/

	protected function addField( $name )
	{

		if(!($name = trim($name)))
			return false;

		try {
				$profile_field = SK_ProfileFields::get($name);
			}
		catch (SK_ProfileFieldFormException  $e){
			return false;
		}

		if (!isset($this->fields[$name]))
		{
			$field = null;
                        switch ($profile_field->presentation)
                        {
                                case 'email':
                                        $field = new field_email($profile_field->name);
                                        break;
                                case 'callto':
                                case 'text':
                                case 'url':
                                        $field = new fieldType_text($profile_field->name);
                                        break;

                                case 'textarea':
                                        $field = new fieldType_textarea($profile_field->name);
                                        break;

                                case 'select':
                                case 'radio':
                                case 'fselect':
                                case 'fradio':
                                        $field = new fieldType_select($profile_field->name);

                                        $field->setType($profile_field->presentation);

                                        $field->setValues($profile_field->values);

                                        $field->setColumnSize($profile_field->column_size);

                                        $field->label_prefix = $profile_field->matching ? $profile_field->matching : $profile_field->name;
                                        break;

                                case 'multicheckbox':
                                case 'multiselect':

                                        $field = new fieldType_set($profile_field->name);

                                        $field->setType($profile_field->presentation);

                                        $field->setValues($profile_field->values);

                                        $field->setColumnSize($profile_field->column_size);

                                        $field->label_prefix = $profile_field->matching ? $profile_field->matching : $profile_field->name;
                                        break;

                                case 'age_range':
                                        $field = new fieldType_age_range($profile_field->name);

                                        if ($profile_field->matching) {
                                                list($start, $end) = explode('-',$profile_field->match_field->custom);
                                                $date = getdate();
                                                $range[0] = $date['year'] - $end;
                                                $range[1] = $date['year'] - $start;
                                        } else {
                                                $range = explode( '-', $profile_field->custom );
                                        }

                                        $field->setRange($range);

                                        $field->order(SK_Config::section("profile_fields")->Section("advanced")->agerange_display_order);
                                        break;

                                case 'password':
                                        $field = new fieldType_password($profile_field->name);
                                        break;

                                case 'birthdate':
                                case 'date':
                                        $field = $profile_field->presentation == 'data'
                                            ? new fieldType_date($profile_field->name)
                                            : new field_birthDate($profile_field->name);

                                        $field->setInvitePrefix('%profile_fields.select_invite_msg.'.$profile_field->profile_field_id . '_');
                                        $field->setRange($profile_field->custom);
                                        $field->order(SK_Config::section("profile_fields")->Section("advanced")->date_year_display_order);
                                        break;

                                case 'location':
                                        $field = new field_location($profile_field->name);
                                        break;

                                case 'photo_upload':

                                        $field = new field_join_photo($profile_field->name);
                                        break;

                                case 'system_checkbox':
                                case 'checkbox':
                                        $field = new fieldType_checkbox($profile_field->name);
                                        break;
                                default:
                                        return false;
                        }
                        
			parent::registerField($field);
		}

		switch ($profile_field->presentation){
			case 'text':
			case 'textarea':
			case 'callto':
			case 'email':
			case 'password':
				if (strlen(trim($profile_field->regexp))) {
					$this->fields[$profile_field->name]->setRegExPatterns($profile_field->regexp);
				}

				if ($profile_field->base_field) {
					$size = SK_MySQL::describe(TBL_PROFILE,$profile_field->name)->size();
				}
				else {
					$size = SK_MySQL::describe(TBL_PROFILE_EXTEND,$profile_field->name)->size();
				}
				if ($size = intval($size)) {
					$this->fields[$profile_field->name]->maxlength = $size;
				}

				break;
		}

		$this->fields[$name]->profile_field_id = $profile_field->profile_field_id;

		if ($profile_field->confirm && !isset($this->fields["re_".$name])) {
				switch ($profile_field->presentation){
					case 'password':
						$field = new fieldType_password('re_'.$name);
						break;
					default:
						$field = new fieldType_text('re_'.$name);
				}

			parent::registerField($field);
		}
	}

	private $avalible_actions = array();

	public $active_action;

	protected function addAction($uniqid, $action_class_name) {

		if (get_parent_class($action_class_name) != "formAction_FieldForm"  ) {
			throw new Exception("Action Class is not child of formAction_FieldForm");
		}

		if (!isset($this->active_action)) {
			$this->active_action = "action_" . $uniqid;
		}

		$action = new $action_class_name("action_" . $uniqid, $uniqid);
		parent::registerAction($action);
	}

	protected abstract function actionsPrepare();

	public function selectAction($uniqid) {
		$this->active_action = 'action_' . $uniqid;
	}

	public function setup()
	{
		$field_names = $this->fields();

		foreach ($field_names as $field_name)
		{
			$this->addField($field_name);
		}

		$this->actionsPrepare();
	}

}

