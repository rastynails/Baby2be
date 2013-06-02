<?php

class SK_FormFrontendHandler extends SK_ComponentFrontendHandler
{
	protected $constructor_prefix = 'form_';
	
	public function bind( $event_type, $function )
	{
		$json_type = json_encode($event_type);
		$this->operations[] = "bind($json_type, $function)";
	}
	
	
}
