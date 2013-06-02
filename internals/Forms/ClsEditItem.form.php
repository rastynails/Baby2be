<?php

class form_ClsEditItem extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('cls_edit_item');
	}
	
	public function setup()
	{
		$item_id = new fieldType_hidden("item_id");
		$title = new fieldType_text("title");
		$description = new fieldType_textarea("description");
		$file = new field_item_file("file");
		$price = new fieldType_text("price");
		$budget_from = new fieldType_text("budget_from");
		$budget_to = new fieldType_text("budget_to");
		$currency = new field_forums_select("currency");
		$allow_bids = new fieldType_checkbox("allow_bids");
		$allow_comments = new fieldType_checkbox("allow_comments");
		$wanted_limited_bids = new fieldType_checkbox("wanted_limited_bids");
		$offer_limited_bids = new fieldType_checkbox("offer_limited_bids");
		$start_date = new fieldType_date("start_date");
		$start_time = new fieldType_time("start_time");
		$end_date = new fieldType_date("end_date");
		$end_time = new fieldType_time("end_time");
		$payment_dtls = new fieldType_textarea("payment_dtls");
		
		parent::registerField($item_id);
		parent::registerField($title);
		parent::registerField($description);
		parent::registerField($file);
		parent::registerField($price);
		parent::registerField($budget_from);
		parent::registerField($budget_to);
		parent::registerField($currency);	
		parent::registerField($allow_bids);
		parent::registerField($allow_comments);
		parent::registerField($wanted_limited_bids);
		parent::registerField($offer_limited_bids);
		parent::registerField($start_date);
		parent::registerField($start_time);
		parent::registerField($end_date);
		parent::registerField($end_time);
		parent::registerField($payment_dtls);
		
		$description->maxlength = 65535;
		
		$price->setSize(4);
		$budget_from->setSize(4);
		$budget_to->setSize(4);
		
		$price->setRegExPatterns('/^[\d]{0,8}([.,]{1}[\d]{1,2})?$/');
		$budget_from->setRegExPatterns('/^[\d]{0,8}([.,]{1}[\d]{1,2})?$/');
		$budget_to->setRegExPatterns('/^[\d]{0,8}([.,]{1}[\d]{1,2})?$/');
		
		$start_date->setRange( array( 'min'=>date( 'Y', SK_I18n::mktimeLocal() ), 'max'=> (date( 'Y', SK_I18n::mktimeLocal() ) + 5 ) ) );
		$end_date->setRange( array( 'min'=>date( 'Y', SK_I18n::mktimeLocal() ), 'max'=> (date( 'Y', SK_I18n::mktimeLocal() ) + 5 ) ) );
		
		parent::registerAction('form_ClsEditItem_Process');
	}	
	
}

class form_ClsEditItem_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('edit');
	}
	
	/**
	 * @return app_ClassifiedsItemService
	 */
	private function getClassifiedsItemService()
	{
		return app_ClassifiedsItemService::newInstance();
	}	
	
	/**
	 * @return app_ClassifiedsFileService
	 */
	private function getClassifiedsFileService()
	{
		return app_ClassifiedsFileService::newInstance();
	}	
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('title', 'description');
			
		parent::setup($form);
	}
	
	/**
	 * @see SK_FormAction::checkData()
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function checkData($data, $response, $form)
	{
		$lang_errors = SK_Language::section('forms.cls_edit_item.messages.error');
		
		if ( !trim($data['item_id']) ) {
			$response->addError( $lang_errors->text('empty_item_id'), 'item_id' );
			return;
		}
		if ( !trim($data['title']) ) {
			$response->addError( $lang_errors->text('empty_title'), 'title' );
			return;			
		}		
		if ( !trim($data['description']) ) {
			$response->addError( $lang_errors->text('empty_description'), 'description' );
			return;			
		}
		if ( isset($data['budget_to']) && (int)$data['budget_from'] > (int)$data['budget_to'] ) {
			$response->addError( $lang_errors->text('incorrect_budget_to'), 'budget_to' );
			return;			
		}
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_errors = SK_Language::section('forms.cls_edit_item.messages.error');
		$item = $this->getClassifiedsItemService()->findById( $post_data['item_id'] );
				
		if ( !($profile_id = SK_HttpUser::profile_id()) ) {
			$response->addError( $lang_errors->text('guest_cannot_edit_item') );
			$response->redirect( SK_Navigation::href('classifieds') );	
			return;
		}
		if ( $profile_id != $item->getProfile_id() && !app_Profile::isProfileModerator($profile_id) ) {
			$response->addError( $lang_errors->text('your_cannot_edit_item') );
			$response->redirect( SK_Navigation::href('classifieds') );	
			return;			
		}
		if ( $post_data['start_date']['year'] ) {

            if (!empty( $post_data['end_time']['day_part'] ))
            {
                $hour = ($post_data['start_time']['day_part'] == 'am') ? (int)$post_data['start_time']['hour']
																					: (int)$post_data['start_time']['hour'] + 12;
            }
            else
            {
                $hour = (int)$post_data['start_time']['hour'];
            }
			$start_stamp = mktime( $hour,
			   (int)$post_data['start_time']['minute'], 0, (int)$post_data['start_date']['month'], 
			   (int)$post_data['start_date']['day'], (int)$post_data['start_date']['year'] );
		}
		if ( $post_data['end_date']['year'] ) 
        {
            if (!empty( $post_data['end_time']['day_part'] ))
            {
                $hour = ($post_data['end_time']['day_part'] == 'am') ? (int)$post_data['end_time']['hour']
																				: (int)$post_data['end_time']['hour'] + 12;
            }
            else
            {
                $hour = (int)$post_data['end_time']['hour'];
            }

			$end_stamp = mktime( $hour,
			   (int)$post_data['end_time']['minute'], 0, (int)$post_data['end_date']['month'], 
			   (int)$post_data['end_date']['day'], (int)$post_data['end_date']['year'] );
		}
		
		if ( isset($start_stamp) && isset($end_stamp) && $start_stamp > $end_stamp ) {
			$response->addError( $lang_errors->text('incorrect_end_date') );
			return;
		}	

		if ( $item->getEntity()=='wanted' && isset($post_data['budget_to']) ) {
			$limited_bids = ( isset($post_data['wanted_limited_bids']) ) ? intval( $post_data['wanted_limited_bids'] ) : null;
		}
		
		if ( $item->getEntity()=='offer' && isset($post_data['price']) ) {
			$limited_bids = ( isset($post_data['offer_limited_bids']) ) ? intval( $post_data['offer_limited_bids'] ) : null;
		}		
		
		$item->setAllow_bids( intval($post_data['allow_bids']) );
		$item->setAllow_comments( intval($post_data['allow_comments']) );
		$item->setBudget_max( (isset($post_data['budget_to']) ? floatval( str_replace(',', '.', $post_data['budget_to']) ) : null) );
		$item->setBudget_min( (isset($post_data['budget_from']) ? floatval( str_replace(',', '.', $post_data['budget_from']) ) : null) );
		$item->setDescription( trim($post_data['description']) );
		$item->setEdit_stamp( time() );
		$item->setEdited_by_profile_id( $profile_id );
		$item->setEnd_stamp( (isset($end_stamp) ? $end_stamp : null) );
		$item->setPrice( (isset($post_data['price']) ? floatval( str_replace(',', '.', $post_data['price']) ) : null) );
		$item->setCurrency( $post_data['currency'] );
		$item->setStart_stamp( (isset($start_stamp) ? $start_stamp : null) );
		$item->setTitle( trim($post_data['title']) );
		$item->setLimited_bids( $limited_bids );
		
		if ( !empty($post_data['payment_dtls']) )
		{
		    $item->setPayment_dtls($post_data['payment_dtls']);
		}

		$this->getClassifiedsItemService()->saveItem( $item );
		
		$post_data['file'] = ( $post_data['file'] ) ? $post_data['file'] : array();
		foreach ($post_data['file'] as $file_code) {
			if (!$file_code) {
				continue;
			}
			$this->getClassifiedsFileService()->saveItemFile( $file_code, $item->getId() );
		}

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_CLASSIFIEDS,
                    'entityType' => 'post_classifieds_item',
                    'entityId' => $item->getId(),
                    'userId' => $item->getProfile_id(),
                    'status' => ( $item->getIs_approved() == true ) ? 'active' : 'approval'
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }

		$response->redirect( SK_Navigation::href('classifieds_item', array('item_id'=>$item->getId())) );

	}
}
