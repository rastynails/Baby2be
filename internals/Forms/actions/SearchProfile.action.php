<?php

class formAction_SearchProfile extends SK_FormAction
{
	/**
	 * The name of a form which uses this action.
	 *
	 * @var string
	 */
	protected $form_name;
	
	public function __construct()
	{
        parent::__construct('search');
	}
	
	
	public function setup( SK_Form $form )
	{
		$this->form_name = $form->getName();
		
		parent::setup($form);
	}
	
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		$service = new SK_Service('search');
		if ($service->checkPermissions() != SK_Service::SERVICE_FULL ) {
			$response->addError($service->permission_message['message']);
			return false;
		}
		
		$service->trackServiceUse();
		
		$data = array();
		foreach ($post_data as $key=>$item)
		{
			if (!empty($post_data[$key])) {
				$data[$key] = $post_data[$key];
			}
		}
		
		$data['new_search'] = 1;
		$data['search_type'] = $form->getSearchType();
		
		$url_info = parse_url($_SERVER["HTTP_REFERER"]);
		parse_str($url_info['query'], $query);
				
		if (isset($query['search_list']) && intval($query['search_list'])) {
			app_SearchCriterion::updateCriterion(intval($query['search_list']), $data);
		}
		
		$url = SK_Navigation::href('profile_list',$data);
		
		$response->redirect($url);
			
	}
	
}
