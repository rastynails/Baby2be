<?php

class component_ClsEditItem extends SK_Component
{	

	/**
	 * @var array
	 */
	private $item_info;
	
	/**
	 * @var array
	 */
	private $files;	
	
	public function __construct( array $params = null )
	{
		parent::__construct('cls_edit_item');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{		
		$item_id = SK_HttpRequest::$GET['item_id'];
		
		$this->item_info = app_ClassifiedsItemService::stGetItemInfo( $item_id );
		
		if ( !$this->item_info ) {
			SK_HttpRequest::redirect( SK_Navigation::href('classifieds') );
		}
		if ( $this->item_info['profile_id'] != SK_HttpUser::profile_id() && !app_Profile::isProfileModerator( SK_HttpUser::profile_id() ) ) {
			SK_HttpRequest::redirect( SK_Navigation::href('classifieds') );
		}
		
		
		$this->files = app_ClassifiedsFileService::stGetItemFiles( $item_id );

		$handler = new SK_ComponentFrontendHandler('ClsEditItem');
		$handler->construct( $this->files );
		
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
		$configs = ( $this->item_info['entity']=='wanted' ) 
			? new SK_Config_Section('cls_wanted') 
			: new SK_Config_Section('cls_offer');	
		
		$Layout->assign('moderator', app_Profile::isProfileModerator( SK_HttpUser::profile_id() ));
		$Layout->assign('files', $this->files);
		$Layout->assign('item_info', $this->item_info);
		$Layout->assign_by_ref('configs', $configs);
		
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
		$start_date = array( 
			'year'=>date("Y", $this->item_info['start_stamp']),
			'month'=>date("n", $this->item_info['start_stamp']), 
			'day'=>date("j", $this->item_info['start_stamp']) 
		);
        $end_date = array(
			'year'=>date("Y", $this->item_info['end_stamp']),
			'month'=>date("n", $this->item_info['end_stamp']),
			'day'=>date("j", $this->item_info['end_stamp'])
		);

        $military_time = SK_Config::section('site.official')->get('military_time');

        if ( $military_time )
        {
            $start_time = array(
                'hour'=>date("G", $this->item_info['start_stamp']),
                'minute'=>date("i", $this->item_info['start_stamp']),
                'day_part'=>date("a", $this->item_info['start_stamp'])
            );

            $end_time = array(
                'hour'=>date("G", $this->item_info['end_stamp']),
                'minute'=>date("i", $this->item_info['end_stamp']),
                'day_part'=>date("a", $this->item_info['end_stamp'])
            );
        }
        else
        {
            $start_time = array(
                'hour'=>date("g", $this->item_info['start_stamp']),
                'minute'=>date("i", $this->item_info['start_stamp']),
                'day_part'=>date("a", $this->item_info['start_stamp'])
            );

            $end_time = array(
                'hour'=>date("g", $this->item_info['end_stamp']),
                'minute'=>date("i", $this->item_info['end_stamp']),
                'day_part'=>date("a", $this->item_info['end_stamp'])
            );
        }

		$date = array( 'year'=>date("Y"), 'month'=>date("n"), 'day'=>date("j") );
		$form->getField("start_date")->setValue( $date );
		
		$currencies = app_ClassifiedsItemService::stGetCurrencies();
		$form->getField("currency")->setValues( $currencies );		
		
		$form->getField("item_id")->setValue( $this->item_info['item_id'] );
		$form->getField("title")->setValue( $this->item_info['title'] );
		$form->getField("description")->setValue( $this->item_info['description'] );
		$form->getField("currency")->setValue( $this->item_info['currency'] );	
		$form->getField("price")->setValue( $this->item_info['price'] );
		$form->getField("payment_dtls")->setValue( $this->item_info['payment_dtls'] );
		$form->getField("budget_from")->setValue( $this->item_info['budget_min'] );
		$form->getField("budget_to")->setValue( $this->item_info['budget_max'] );
		$form->getField("allow_bids")->setValue( $this->item_info['allow_bids'] );
		$form->getField("allow_comments")->setValue( $this->item_info['allow_comments'] );

		$form->getField("start_date")->setValue( $start_date );
		$form->getField("start_time")->setValue( $start_time );
		$form->getField("end_date")->setValue( $end_date );
		$form->getField("end_time")->setValue( $end_time );	
	
		$form->getField($this->item_info['entity']."_limited_bids")->setValue( $this->item_info['limited_bids'] );

	}
	
	/**
	 * Deletes item files
	 *
	 * @param int $file_id
	 */
	public static function ajax_DeleteItemFile( $params , SK_ComponentFrontendHandler $handler) 
	{
		return app_ClassifiedsFileService::stDeleteItemFile( $params->file_id );
	}
}
