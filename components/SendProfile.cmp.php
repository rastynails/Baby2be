<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 24, 2008
 * 
 */

class component_SendProfile extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params )
	{
		parent::__construct( 'send_profile' );
		$this->profile_id = (int)$params['profile_id'];	
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('SendProfile');
		$this->frontend_handler->construct();
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout ) 
	{
		
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField( 'profile_id' )->setValue( $this->profile_id );
		
		$form->frontend_handler->bind("success", "function(data) { this.\$form[0].reset(); this.ownerComponent.floatBox.close(); }");
		
	}


}