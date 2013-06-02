<?php

class form_Report extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('report');
	}

	public function setup()
	{
		$reason = new fieldType_textarea('reason');
		parent::registerField($reason);
		
		$reporter_id = new fieldType_hidden('reporter_id');
		parent::registerField($reporter_id);
		
		$entity_id = new fieldType_hidden('entity_id');
		parent::registerField($entity_id);
		
		$type = new fieldType_hidden('type');
		parent::registerField($type);
		
		parent::registerAction('formReport_Add');
	}

}

class formReport_Add extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('add');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('reporter_id', 'entity_id', 'type', 'reason');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$report_added = app_Reports::addReport($post_data['reporter_id'], $post_data['entity_id'], $post_data['type'], $post_data['reason']);
		
		switch ( $report_added )
		{
			case 1:
				//report added
				$response->addMessage(SK_Language::section('forms.report.msg')->text('report_added'));
				break;
			case -2:
				//wrong parameters
				$response->addError(SK_Language::section('forms.report.msg')->text('report_not_added'));
				break;
			case -3:
				//content already reported by member				
				$response->addError(SK_Language::section('forms.report.msg')->text('already_reported'));
		}
	}
}

