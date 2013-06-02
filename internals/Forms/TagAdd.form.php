<?php
require_once DIR_APPS.'appAux/Tag.php';


/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 24, 2008
 * 
 */

class form_TagAdd extends SK_Form
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'tag_add' );
	}
	
	public function setup()
	{		
		$this->registerField( new fieldType_text( 'tags' ) );
		$this->registerField( new fieldType_hidden('entity_id') );
		$this->registerField( new fieldType_hidden('feature') );
		$this->registerAction( new TagAddFormAction() );
	}
	
}

class TagAddFormAction extends SK_FormAction
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'add_tag' );
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
		$service = app_TagService::newInstance( $data['feature'] );
		
		if( $service === null || (int)$data['entity_id'] <= 0 )
		{
			$response->addError( 'error' );
			return;
		}
		
		$tags = $service->addTagsToEntity( $data['entity_id'], $data['tags'] );

        if( sizeof($tags) > 0 )
        {
            $response->addMessage( SK_Language::text( 'components.tag_edit.msg_tag_add' ) );
        }
        else
        {
            $response->addError( SK_Language::text( 'components.tag_edit.msg_tag_not_add' ) );
        }
		
		return array( 'entity_id' => $data['entity_id'], 'feature' => $data['feature'] );
		
	}
	
	/**
	 * @see SK_FormAction::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup( SK_Form $form )
	{
		$this->required_fields = array( 'tags' );
		parent::setup($form);
	}
}