<?php

class component_ProfileDetails extends SK_Component
{
	private $profile_id;

	private $pages;

	public function __construct( array $params = null )
	{
		$this->profile_id = isset($params["profile_id"]) ? $params["profile_id"] : SK_HttpUser::profile_id();

		if (!$this->profile_id) {
			$this->annul();
		}

		parent::__construct('profile_details');
	}

	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend)
	{
		$handler = new SK_ComponentFrontendHandler('ProfileDetails');

		$handler->construct();

		$this->pages = app_FieldView::steps();

		foreach ($this->pages as $item) {
			$handler->addPage($item, SK_Language::section("profile_fields.page_view")->text($item));
		}

		$handler->activatePage($this->pages[0]);

		$handler->complete();

		$this->frontend_handler = $handler;

		$Layout->assign('menu_items', array(array(
			'href'	=> 'javascript://',
			'label'	=> '',
			'active'=> false
		)));

		return parent::prepare($Layout, $Frontend);
	}

	public function render(SK_Layout $Layout)
	{
		$location_fields = array("country_id", "state_id", "zip", "city_id", "custom_location");

		$Layout->assign("username", app_Profile::username($this->profile_id));

		app_ProfileField::orderLocationFields($location_fields);

		$page_fields = app_FieldView::fields();

		$fields = array();
		foreach ($page_fields as $step_fields) {
			foreach ($step_fields as $item) {
				if ($item == "location") {
					foreach ($location_fields as $loc_item) {
						$fields[] = (in_array($loc_item,array('zip', 'custom_location'))) ? htmlspecialchars($loc_item) : substr($loc_item, 0,strlen($loc_item)-3);

					}

				} else {
					$fields[] = $item;
				}
			}
		}

		$field_values = app_Profile::getFieldValues($this->profile_id, $fields);
		$display_age = SK_Config::section('profile_fields')->Section('advanced')->date_display_config == 'age';

		$out = array();
		foreach ($page_fields as $step => $step_fields) {

			if (isset($step_fields[SK_ProfileFields::get("location")->profile_field_id])) {

				foreach ($location_fields as $loc_item) {
					$step_fields[] = (in_array($loc_item,array('zip', 'custom_location'))) ? htmlspecialchars($loc_item) : substr($loc_item, 0,strlen($loc_item)-3);
				}

				unset($step_fields[SK_ProfileFields::get("location")->profile_field_id]);
			}

			foreach ($step_fields as $name) {

				$value = $field_values[$name];
				if (in_array($name, array('state', "city", "country"))) {
					$name = $name . "_id";
				}


				try {
					$pr_f = SK_ProfileFields::get($name);
				} catch (SK_ProfileFieldException $e) {
					continue;
				}

				if ($pr_f->presentation == 'birthdate') {
					if (!$value || $value == '0000-00-00') {
						continue;
					}

					if ($display_age)
					{
						$value = app_Profile::getAge($value);
					}
					else
					{
                       $value = $this->formatDate($value);
					}
				}

			    if ($pr_f->presentation == 'date')
			    {
                    if (!$value || $value == '0000-00-00') {
                        continue;
                    }

                    $value = $this->formatDate($value);
			    }

				$f_type = $this->field_type($pr_f->presentation);

				if (!isset($value)) {continue;}

				if ($f_type=="text") {
					if (!strlen(trim($value))) {
						continue;
					}
					$value = SK_Language::htmlspecialchars($value);
				} else if ($f_type=="int") {
					if (!intval($value)) {
						continue;
					}
					$_value = $value;
					$value = array();
					if (count($pr_f->values)) {
						foreach ($pr_f->values as $item)
						{
							if (in_array($pr_f->presentation, array('fselect', 'fradio')))
							{
								if ($item == $_value) {
									$value[] = $item;
									$f_type = "array";
								}
							} else {
								if ((int)$item & (int)$_value) {
									$value[] = $item;
									$f_type = "array";
								}
							}
						}
					}
				}


				$section = in_array($pr_f->name, $location_fields)
				? SK_ProfileFields::get("location")->getSection()
				: $pr_f->getSection();

                                $out[$step][$section['order']]['sectionId'] = $section['profile_field_section_id'];
				$out[$step][$section['order']]['fields'][] = array(
                                        'id' => $pr_f->profile_field_id,
					'value'			=> $value,
					'presentation'	=> $pr_f->presentation,
					'match'	=> $pr_f->matching,
					'type'	=> $f_type,
					'name'	=> $pr_f->name,
				);

			}
		}

                foreach ( $out as & $step )
                {
                    ksort($step);
                    foreach ( $step as & $section )
                    {
                        ksort($section);
                    }
                }

		$Layout->assign('page_fields', array_filter($out, "count"));

		return parent::render($Layout);
	}

	private function formatDate( $dateString )
	{
	    list($year, $month, $day) = explode('-', $dateString);

	    $dmy = array(
            'm' => $month,
            'd' => $day,
            'y' => $year
        );

        $dateFormatString = SK_Config::section('site.official')->date_format;
        $dateFormatString = !empty($dateFormatString) ? $dateFormatString : 'd-m-y';
        $dateFormat = explode('-', $dateFormatString);

        $date = array();

        foreach ( $dateFormat as $i )
        {
            array_push($date, $dmy[$i]);
        }

        return implode('-', $date);
	}

	private function field_type($presentation)
	{
		switch ($presentation) {
			case "text":
                        case "system_text":
			case "textarea":
			case "password":
			case "date":
			case "birthdate":
			case "age_range":
			case "callto":
			case "email":
			case "url":
				return "text";
				break;
			default:
				return "int";
		}
	}
}
