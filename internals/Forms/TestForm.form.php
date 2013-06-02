<?php

class form_TestForm extends SK_Form
{
	public function __construct() {
		parent::__construct('test_input_items');
	}
	
	public function setup()
	{
		$id_items = new fieldType_custom_select('id_entries');
		parent::registerField($id_items);
		
		$text = new fieldType_text('text');
		parent::registerField($text);
		
		$textarea = new fieldType_textarea('textarea');
		parent::registerField($textarea);
		
		$file = new field_profile_photo('test_image');
		parent::registerField($file);
		
		parent::registerAction('form_TestForm_submit');
	}
	
}


class form_TestForm_submit extends SK_FormAction
{
	public function __construct() {
		parent::__construct('submit');
	}
	
	public function process( array $data, SK_FormResponse $response, SK_Form $form )
	{
		$response->debug($data);
		
	}
}