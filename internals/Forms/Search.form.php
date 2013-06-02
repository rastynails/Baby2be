<?php

class form_Search extends SK_Form
{
	protected $search_type;

	protected $search_list_id;

	protected $search_criterion;

	public function __construct($form_name)
	{
		parent::__construct($form_name);
	}

	protected function fields(){
		$fields = app_ProfileSearch::SearchTypeFields($this->search_type);

		return $fields;
	}

	protected function setSearchType($search_type){
		$this->search_type = trim($search_type);
	}

	public function getSearchType()
	{
		return $this->search_type;
	}

	protected function addField( $name )
	{
		$pr_field = SK_ProfileFields::get($name);

			switch ($pr_field->presentation)
			{
				case 'location':
					$field = new field_mileage($pr_field->name);
					break;

				case 'keyword':
				case 'text':
				case 'password':
				case 'callto':
				case 'url':
					$field = new fieldType_text($pr_field->name);
					break;

				case 'textarea':
					$field = new fieldType_textarea($pr_field->name);
					break;

				case 'system_checkbox':
				case 'checkbox':

					$field = new fieldType_checkbox($pr_field->name);

					break;

				case 'select':
				case 'radio':
				case 'multiselect':
				case 'multicheckbox':

        				if($pr_field->name == 'sex' || ( $pr_field->name == 'match_sex' && $pr_field->presentation == 'radio' ) ) {

						$field = new fieldType_select($pr_field->name);
						$field->setType('radio');
					}
                                        else
                                        {
                                            $field = new fieldType_set($pr_field->name);
                                            $field->setType('multicheckbox');
                                        }

					$field->setValues($pr_field->values);
					$field->setColumnSize($pr_field->column_size);
					$field->label_prefix = $pr_field->matching ? $pr_field->matching : $pr_field->name;
					break;

				case 'fselect':
				case 'fradio':
					$field = new fieldType_set($pr_field->name);
					$field->setType('multicheckbox');
					$field->setValues($pr_field->values);
					$field->setColumnSize($pr_field->column_size);
					$field->label_prefix = $pr_field->name;
					break;

				case 'date':
					$field = new fieldType_date($pr_field->name);
					foreach (array('year','month','day') as $item){
						$field->setInviteMsg($item, SK_Language::text('%profile_fields.select_invite_msg.'.$pr_field->profile_field_id . '_' . $item));
					}
					$field->setRange($pr_field->custom);
					$field->order(SK_Config::section("profile_fields")->Section("advanced")->date_year_display_order);
					break;

				case 'birthdate':

					$field = new fieldType_age_range($pr_field->name);

					$match_range = explode( '-', $pr_field->custom );
					$date	= getdate();

					$range[0]	= $date['year'] - $match_range[1];
					$range[1]	= $date['year'] - $match_range[0];

					$field->setRange($range);

					$field->order(SK_Config::section("profile_fields")->Section("advanced")->agerange_display_order);
					break;

			}

			$field->profile_field_id = $pr_field->profile_field_id;

			parent::registerField($field);

	}

	public function renderStart(array $params = null)
	{
		if (isset($_GET['search_list']) && $_GET['search_list'] ) {
			$list_id = intval($_GET['search_list']);
			$this->search_criterion = app_SearchCriterion::getCriterionById($list_id);
		}
		else {
			$this->search_criterion = app_ProfileSearch::getPreference();
		}

		foreach ($this->fields as $name => $field){
			$value = $this->get_value($name);
			try {
				if (isset($value)){
					$field->setValue($value);
				}
			} catch (SK_FormFieldValidationException $e){}
		}

		return parent::renderStart($params);
	}

	protected function get_value($field_name)
	{
		if (isset($_GET['refine']) && (bool)$_GET['refine'])
		{
			if ($field_name=='location') {
				if ( isset($this->search_criterion['location']) ) {
					return $this->search_criterion['location'];
				}

				$value = array();
				foreach (array('country_id','state_id','city_id','zip_third','radius_first', 'radius_third', 'custom_location','mcheck_country') as $item){
					$value[$item] = $this->search_criterion[$item];
				}
				return $value;
			}

			return $this->search_criterion[$field_name];

		}
		else
		{
			switch ($field_name){
				case 'match_sex':
				case 'sex':
					$value = app_ProfileSearch::getPreference($field_name);
					if(!isset($value) && SK_HttpUser::is_authenticated()){
						$value = app_Profile::getFieldValues(SK_HttpUser::profile_id(),$field_name);
					}

					return $value;
			}
		}
	}

	public function setup()
	{
		$field_names = $this->fields();

		foreach ($field_names as $field_name)
		{
			$this->addField($field_name);
		}

		parent::registerAction('formAction_SearchProfile');
	}

}

