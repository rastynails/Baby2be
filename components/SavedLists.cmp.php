<?php

class component_SavedLists extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('saved_lists');
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$profile_id = SK_HttpUser::profile_id();
		if (!$profile_id) {
			return false;
		}
		
		$lists = app_SearchCriterion::getCriterionList($profile_id);
		if (!count($lists)) {
			return false;
		}
		
		$handler = new SK_ComponentFrontendHandler('SavedLists');
		$handler->construct();
		
		$href = SK_Navigation::href('profile_list');
		
		
		
		foreach ($lists as $list){
			$handler->display($list->criterion_name, sk_make_url($href, array('search_list'=>$list->criterion_id)));	
		}
		
		$this->frontend_handler = $handler;
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		return parent::render($Layout);
	}
	
	
	public static function ajax_deleteList( $params, SK_ComponentFrontendHandler $handler )
	{
		if (app_SearchCriterion::deleteCriterion(SK_HttpUser::profile_id(), $params->list_name)) {
			$handler->remove($params->list_name);
		}
	}
	
	public static function ajax_editList( $params, SK_ComponentFrontendHandler $handler )
	{
		$criterion = app_SearchCriterion::getCriterion($params->list_name);
		app_ProfileSearch::generateResultList($criterion['search_type'],$criterion);
		$query = SK_MySQL::placeholder("SELECT `criterion_id` FROM `".TBL_SEARCH_CRITERION."` WHERE `criterion_name`='?'",$params->list_name);
		$criterion_id = SK_MySQL::query($query)->fetch_cell();
		$handler->redirect(SK_Navigation::href('profile_search',array('refine'=>true,'search_list'=>$criterion_id)));
	}
	
	
}
