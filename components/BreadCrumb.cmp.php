<?php

class component_BreadCrumb extends SK_Component
{
	private $bread_crumb;
	
	public function __construct( array $params = null )
	{
		parent::__construct('bread_crumb');
		//$this->cache_lifetime = 10;
		
	}
	
	public function prepare(SK_Layout $Layout , SK_Frontend $Frontend)
	{
		$nav = SK_HttpRequest::getDocument();
		$bread_crumb = SK_Navigation::BreadCrumb();
		
		$crumb_actions = SK_Navigation::getCrumbActions();
		
		if(count($bread_crumb) <=1) $bread_crumb = array();
		
		for ($i=0; $i < $crumb_actions["remove"]; $i++) {
			array_shift($bread_crumb);	
		}
		
		$_bread_crumb= array();
		foreach ($bread_crumb as $key => $item )
		{
			$_bread_crumb[$key]['label'] = SK_Language::section('nav_doc_item')->text($item);
			$_bread_crumb[$key]['document_key'] = $item;
			$_bread_crumb[$key]['url'] = SK_Navigation::href($item);

		}
		
		$this->bread_crumb = array_reverse($_bread_crumb);
		
		$_add_crumb = array();
		if ((bool)count($crumb_actions["add"])) {
			foreach ($crumb_actions["add"] as $item) {
				$new_item = array(
					'label'	=> $item['label'],
					'url' =>  isset($item['url']) ? $item['url'] : null,
					'document_key' => $nav->document_key
				);
				array_push($this->bread_crumb, $new_item);
			}
		}
		
		$this->bread_crumb = array_merge($this->bread_crumb, $_add_crumb);
		
		parent::prepare($Layout , $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('bread_crumb', $this->bread_crumb);
		return parent::render($Layout);
	}
	
}

