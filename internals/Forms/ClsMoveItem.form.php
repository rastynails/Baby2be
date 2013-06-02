<?php

class form_ClsMoveItem extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('cls_move_item');
	}
	
	public function setup()
	{
		$item_id = new fieldType_hidden("item_id");
		$category = new field_category_select("category");
		
		parent::registerField($item_id);
		parent::registerField($category);
		
		parent::registerAction('form_ClsMoveItem_Process');
	}	
	
}

class form_ClsMoveItem_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('move');
	}
	
	/**
	 * @return app_ClassifiedsItemService
	 */
	private function getClassifiedsItemService()
	{
		return app_ClassifiedsItemService::newInstance();
	}	
	
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('category');
			
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
		$lang_errors = SK_Language::section('forms.cls_new_item.messages.error');
		
		if ( !(int)$data['category'] ) {
			$response->addError( $lang_errors->text('empty_category'), 'category' );
			return ;			
		}
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_errors = SK_Language::section('forms.cls_move_item.messages.error');
		$lang_message = SK_Language::section('forms.cls_move_item.messages.success');
		
		$item = $this->getClassifiedsItemService()->findById( $post_data['item_id'] );
				
		if ( !($profile_id = SK_HttpUser::profile_id()) ) {
			$response->addError( $lang_errors->text('guest_cannot_move_item') );
			$response->redirect( SK_Navigation::href('classifieds') );	
		}
		if ( !app_Profile::isProfileModerator($profile_id) ) {
			$response->addError( $lang_errors->text('your_cannot_move_item') );
			$response->redirect( SK_Navigation::href('classifieds') );				
		}
		
		$item->setGroup_id( (int)$post_data['category'] );
		$item->setEntity( app_ClassifiedsGroupService::stGetGroupEntity( (int)$post_data['category'] ) );

		$this->getClassifiedsItemService()->saveItem( $item );

		$response->addMessage( $lang_message->text('item_moved') );

		return true;
	}
}
