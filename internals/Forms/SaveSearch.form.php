<?php

class form_SaveSearch extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('save_search');
	}
	
	
	public function setup()
	{
		$field = new fieldType_text('name');
		$field->maxlength = 14;
		parent::registerField($field);
		
		parent::registerAction('form_SaveSearch_Process');
	}
	
}

class form_SaveSearch_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('save');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('name');
			
		parent::setup($form);
	}
	
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$url_info = parse_url($_SERVER["HTTP_REFERER"]);
		parse_str($url_info['query'], $criterion);
		try {
			$result = app_SearchCriterion::saveCriterion(SK_HttpUser::profile_id(), $post_data['name'], $criterion);
			$response->exec('window.location.reload();');
		}
		catch (SK_SearchCriterionException $e){
			if ($e->getCode() == 2) {
				$response->addError(SK_Language::text("%forms.save_search.messages." . $e->getErrorKey()));
			}
		}
		
	}
}
