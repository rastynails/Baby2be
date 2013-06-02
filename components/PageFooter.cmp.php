<?php

class component_PageFooter extends SK_Component
{
	public function __construct()
	{
		parent::__construct('page_footer');
	}
	
	public function render( SK_Layout $Layout )
	{
		$bottom =  SK_Navigation::menu('bottom');
		
		$Layout->assign('bottom_menu', $bottom);
		
		$Layout->assign('site_name', SK_Config::section("site")->Section("official")->site_name);
			
		$Layout->assign('year', date("Y"));
				
		return parent::render($Layout);
	}
}
