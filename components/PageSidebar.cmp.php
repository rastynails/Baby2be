<?php

class component_PageSidebar extends SK_Component
{
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct('page_sidebar');
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('sign_in', !SK_HttpUser::is_authenticated());
		
		parent::render($Layout);
	}
	
}

