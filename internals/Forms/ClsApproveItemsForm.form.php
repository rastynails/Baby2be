<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 * 
 */

final class form_ClsApproveItemsForm extends SK_Form 
{
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( )
	{
		parent::__construct( 'cls_index_approve_list' );
	} 
	
	/**
	 * @see SK_Form::setup()
	 *
	 */ 
	public function setup() 
	{		

		$items = new field_checkbox_set ( 'item' );
		$this->registerField( $items );
		$this->registerAction( new ClsApproveItemsFormAction() );
	}
	

 
}

class ClsApproveItemsFormAction extends SK_FormAction
{

	/**
	 * Class constructor
	 *
	 * @param string $key
	 */
	public function __construct( )
	{
		parent::__construct( "approve_items_list" );
		
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
		$lang_errors = SK_Language::section('components.cls_item.error_msg');
		
		if (!isset($data['item']) || empty($data['item']) ) {

			$response->addError( $lang_errors->text('empty_approve_list'));
			return;
		}

	}	
	
	/**
	 * @see SK_FormAction::process()
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function process( array $data, SK_FormResponse $response, SK_Form $form )
	{	
		$affected_Rows = 0;
		foreach ($data['item'] as $id)
		{			
			$affected_Rows += app_ClassifiedsItemService::stApproveItem( $id );
		}
		if ($affected_Rows)
		{		
			$response->addMessage( SK_Language::text('components.cls_item.messages.item_list_approved') );
		}
		else
		{
			$response->addMessage( SK_Language::text('components.cls_item.error_msg.cannot_approve_list') );
		}

		$response->reload();		
	}
	
	/**
	 * @see SK_FormAction::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup($form) 
	{
		$this->process_fields = array( 'item' );
		$this->required_fields = array( 'item' );
		parent::setup($form);
	}
	
}




