<?php

class component_SignIn extends SK_Component
{
	private $hidden = false;
	
	private $type = "default";
	
	public function __construct( array $params = null )
	{
		if (isset($params["type"]) && $params["type"]) {
			$this->type = $params["type"];
		}
		
		if (isset($params["hidden"]) && $params["hidden"]) {
			$this->hidden = true;
			$this->in_line = false;
		}
			
		parent::__construct('sign_in');
	}
	
	public function prepare(SK_Layout $layout, SK_Frontend $Frontend) {
		
		if (SK_HttpUser::is_authenticated() && !$this->hidden) {
			return false;
		}
		
		$this->tpl_file = $this->type . '.tpl';
				
		if ($this->hidden) {
			$handler = new SK_ComponentFrontendHandler('SignIn');
			$handler->saveLink();
			$this->frontend_handler = $handler;
		}
		
		$layout->assign("type", ($this->type != "default") ? $this->type : false);
		
		$layout->assign("hidden", $this->hidden);
		
		return parent::prepare($layout, $Frontend);
	}

}
