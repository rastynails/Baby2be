<?php

class form_QuickSearch extends form_Search
{

	public function __construct()
	{
		$this->setSearchType('quick_search');

		parent::__construct('quick_search');

	}

	public function fields()
	{
		$fields = app_ProfileSearch::SearchTypeFields($this->getSearchType());

		return $fields;

	}

	public function addField($name){

	if ($name=="location")
        {
            $field = new field_simple_mileage($name, SK_Config::section("site")->Section("additional")->Section("profile_list")->quick_search_location_type == "zip");
            parent::registerField($field);
        }
        else
        {
            parent::addField($name);
        }

	}
}

