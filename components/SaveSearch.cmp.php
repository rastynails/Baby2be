<?php

class component_SaveSearch extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('save_search');
	}
		
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		if (!SK_HttpUser::is_authenticated()) {
			return false;			
		}		
		$Frontend->onload_js(
			'$("#'.$this->getTagAutoId('btn').'").click(
				function() {
					var $title = $("#'.$this->getTagAutoId('thick_title').'").children();
					var $contents = $("#'.$this->getTagAutoId('thick_content').'").children();
					
					var box = new SK_FloatBox({
						$title: $title,
						$contents: $contents,
						width: 230
					});
					
					$("#'.$this->getTagAutoId('cancel').'").click(function() {
						$title.appendTo("#'.$this->getTagAutoId('thick_title').'");
						$contents.appendTo("#'.$this->getTagAutoId('thick_content').'");
						$(":password", "#'.$this->getTagAutoId('thick_content').'").val("");
						box.close();
					});
				}
		)');
		
		parent::prepare($Layout, $Frontend);
	}
	
}	

