<?php

class component_ClsNewItem extends SK_Component
{	
	
	public function __construct( array $params = null )
	{
	    if ( !app_Features::isAvailable(45) )
        {
            SK_HttpRequest::showFalsePage();
        }
        
		parent::__construct('cls_new_item');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{		
		$configs = array();
		$wanted_section = new SK_Config_Section('cls_wanted');
		$offer_section = new SK_Config_Section('cls_offer');
		
		$configs['wanted']['allow_comments'] = $wanted_section->allow_comments;
		$configs['wanted']['allow_bids'] = $wanted_section->allow_bids;
		$configs['offer']['allow_comments'] = $offer_section->allow_comments;
		$configs['offer']['allow_bids'] = $offer_section->allow_bids;			

        $Layout->assign('wanted_allow_comments', $wanted_section->allow_comments);
        $Layout->assign('wanted_allow_bids', $wanted_section->allow_bids);
        $Layout->assign('offer_allow_comments', $offer_section->allow_comments);
        $Layout->assign('offer_allow_bids', $offer_section->allow_bids);

		$handler = new SK_ComponentFrontendHandler('ClsNewItem');

		$handler->construct( $configs );	
		
		$item_type = @SK_HttpRequest::$GET['type'];
		if ( $item_type ) {
			$handler->hideItemType( $item_type, true );
		}		
		
		$this->frontend_handler = $handler;
		return parent::prepare( $Layout, $Frontend );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{
	    $service = new SK_Service('post_classifieds_item', SK_HttpUser::profile_id());

	    if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
	    {
	        $Layout->assign('no_permission', $service->permission_message['message']);
	    }
	    else 
	    {
            $Layout->assign('no_permission', '');
	    }

	    $Layout->assign('allow_payment', SK_Config::section('classifieds')->get('allow_payment'));
	    
		return parent::render($Layout);
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{	
		$configs = new SK_Config_Section('classifieds');
		
		$groups_arr = app_ClassifiedsGroupService::stGetItemCategories();
		$form->getField("category")->setValues( $groups_arr );
		
		$currencies = app_ClassifiedsItemService::stGetCurrencies();
		$form->getField("currency")->setValues( $currencies );
		$form->getField("currency")->setValue( $configs->currency );
		
		$date = array( 'year'=>date("Y"), 'month'=>date("n"), 'day'=>date("j") );
		$form->getField("start_date")->setValue( $date );
	}
}
