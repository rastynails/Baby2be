<?php

abstract class component_FieldForm extends SK_Component
{
	protected $fields = array();
	
	protected $tpl_fields = array();
	
	protected $enable_sections = true;
	
	public function __construct($namespace)
	{
		parent::__construct($namespace);
	}
	
	abstract function fields();
	
	protected function prepareFields()
	{
		$tpl_fields = array();
		$this->fields = $this->fields ? $this->fields : array();
		
		foreach ($this->fields as $field) {
			
			$pr_field = SK_ProfileFields::get($field);
			
			$tpl_field['name'] = $pr_field->name;
			$tpl_field['presentation'] = $pr_field->presentation;
			$tpl_field['confirm'] = $pr_field->confirm;
			$tpl_field['required'] = $pr_field->required_field;
			$tpl_field['id'] = $pr_field->profile_field_id;
			
			if ($this->enable_sections) {
				$this->tpl_fields[$pr_field->profile_field_section_id][$pr_field->name] = $tpl_field;	
			}
			else {
				$this->tpl_fields[$pr_field->name] = $tpl_field;	
			}
			
		}
	}
	
	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend){
		$this->fields = $this->fields();
		parent::prepare( $Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$this->prepareFields();
		
		$Layout->assign("fields", $this->tpl_fields);
		
		return parent::render($Layout);
	}
	
	public function handleForm(SK_Form $Form)
	{
		$fields = $Form->fields;
		
		foreach ($fields as $name => $field) 
		{
			if (strpos($name, "re_")!==0 && $name!="captcha" ) {
				
				$pr_field = SK_ProfileFields::get($name);
				if ($pr_field->confirm) {
					$value = $field->getValue();
					$Form->fields["re_" . $name]->setValue($value);
				}
				
			}	
		}
		
		parent::handleForm($Form);
	}
	
}
