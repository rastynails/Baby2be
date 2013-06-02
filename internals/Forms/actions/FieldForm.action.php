<?php

abstract class formAction_FieldForm extends SK_FormAction
{
	protected $uniqid;
	/**
	 * The name of a form which uses this action.
	 *
	 * @var string
	 */
	
	public function __construct($name, $uniqid = null)
	{
		if ($uniqid) {
			$this->uniqid = $uniqid;	
		}
		
		parent::__construct($name);
	}
	
	protected abstract function process_fields($uniqid);
	
	public function setup( SK_Form $Form )
	{
		$fields = $this->process_fields($this->uniqid);
		
		if (!$fields) {
			$fields = array();
		}
		
		foreach ($Form->fields as $field)
		{
			if (!in_array($field->getName(), $fields)) {
				
				if (strpos($field->getName(), 're_') !== 0) {
					continue;
				} 
				
				$confirm_field = substr($field->getName(), 3);
				
				if (!in_array($confirm_field, $fields)) {
					continue;	
				}
			}
			
			$this->process_fields[] = $field->getName();
			
			if (strpos($field->getName(),'re_')===0 || $field->getName()=='captcha'){
				$this->required_fields[]=$field->getName();
			}
			elseif (SK_ProfileFields::get($field->getName())->required_field){
				$this->required_fields[] = $field->getName();
			}
		}	
		parent::setup($Form);
	}

	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
	{
		
		foreach ($data as $field=>$value)
		{
			try {
				$pr_field = SK_ProfileFields::get($field);
			}
			catch (Exception $e){
				continue;
			}
							
			if ($pr_field->confirm){

                                $confirmResult = ($value == $data["re_".$field]);
                                if ( $pr_field->name == 'email' )
                                {
                                    $value = strtolower($value);
                                    $confirmResult = ( $value == strtolower($data["re_".$field]) );
                                }

				if (!$confirmResult) {
					
					$confirmation_error_msg = SK_Language::text('%profile_fields.error_msg.confirm_'.SK_ProfileFields::get($field)->profile_field_id);
					$response->addError($confirmation_error_msg,'re_'.$field);	
				}
				unset($post_data["re_".$field]);
			}
		}
		
		parent::checkData($data, $response, $form);
	}
	
}