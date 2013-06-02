<?php

class component_HotRate extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('hot_rate');
	}
		
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$first_item = app_ProfilePhoto::random_photo();
		
		if ($first_item) {
			$handler = new SK_ComponentFrontendHandler('HotRate');
		
			$handler->construct();
			
			$this->frontend_handler = $handler;
			
			$Layout->assign("rate", new component_Rate(array('entity_id'=>$first_item["photo_id"], 'feature'=>'photo', "block"=>false)));
		}
			
		$sexes = array();
		
		foreach (SK_ProfileFields::get("sex")->values as $item) {
			$sexes[] = array(
				"label" => SK_Language::text("profile_fields.value.sex_" . $item),
				"id"	=> $item
			);
		}
		
		$Layout->assign('sexes', $sexes);
		$Layout->assign('photo', $first_item);
		
		$Layout->assign('height', SK_Config::section("photo")->Section("general")->view_height);
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		return parent::render($Layout);
	}
	
	
	public static function ajax_changePhoto($params = null, SK_ComponentFrontendHandler $handler) {
		
		$sex = $params->sex ? $params->sex : 0;
		
		$photo = app_ProfilePhoto::random_photo($sex);
		if (!$photo) {
			return array('result'=> false, 'msg'=> SK_Language::text("%components.hot_rate.no_photo"));
		}
		
		$photo["profile_url"] = SK_Navigation::href("profile", array("profile_id"=>$photo["profile_id"]));
		$photo["result"] = true;
		return $photo;
	}
	
}
