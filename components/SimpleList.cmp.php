<?php

class component_SimpleList extends SK_Component
{
	private $items = array();
	
	private $count = false;
	
	public function __construct( array $params = null )
	{
		$this->items = $params["items"];
		if ( isset($params["count"]) && ($params["count"] = intval($params["count"])) ) {
			$this->count = $params["count"];
		}
		
		parent::__construct('simple_list');
	}
	
	public function render( SK_Layout $Layout )
	{
		$out = array();
		$iteration = 1;
		foreach ($this->items as $item) {
			$out[] = $item;
			$iteration++;
			if ( ($this->count !== false) && ($iteration > $this->count) ) {
				break;
			}
		}
		$Layout->assign('items', $out);
		return parent::render($Layout);
	}
	
}
