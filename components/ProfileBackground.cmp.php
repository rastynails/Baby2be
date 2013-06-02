<?php
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Feb 09, 2009
 * 
 */

class component_ProfileBackground extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	private $bgColor;
	
	private $bgImage;
	
	private $bgImageUrl;
	
	private $isOwner;
	
	private $bgImageMode;
	
	/**
	 * @var app_ProfileComponentService
	 */
	private $pc_service;
	
	/**
	 * @var array
	 */
	private $permission;
	
	
	/**
	 * Constructor
	 *
	 * @param array $params
	 */
	public function __construct( $profile_id )
	{
		parent::__construct( 'profile_background' );
		$this->profile_id = (int)$profile_id;
		$this->pc_service = app_ProfileComponentService::newInstance();
		$field_arr = $this->pc_service->getBgInfo($this->profile_id);
		
		$this->bgColor = $field_arr['bg_color'];
		$this->bgImage = $field_arr['bg_image'];
		$this->bgImageUrl = $field_arr['bg_image_url'];
		$this->bgImageMode = $field_arr['bg_image_mode'];
		
		if($profile_id == SK_HttpUser::profile_id())
			$this->isOwner = true;
			
		$service = new SK_Service('profile_background', $this->profile_id);
		
		$this->permission['is_available'] = ($service->checkPermissions()== SK_Service::SERVICE_FULL);
		$this->permission['message'] = $service->permission_message['message'];
		
		if ( !app_Features::isAvailable(36) || (!$this->permission['is_available'] && !$this->isOwner) ) {
			$this->annul();
		}
		
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		if ( !$this->permission['is_available'] ) {
			return ;
		}
		
		
		$Frontend->include_js_file(URL_STATIC.'color_picker.js');
		
		$handler = new SK_ComponentFrontendHandler('ProfileBackground');
		$this->frontend_handler = $handler;
			
		if( $this->bgImageMode )
		{
			$image = $this->bgImage ? URL_USERFILES.$this->bgImage : null;
		}
		else 
		{
			$image = $this->bgImageUrl ? $this->bgImageUrl : null;
		}
		
		if ( $this->pc_service->getBGImageStatus($this->profile_id)!='active' && !$this->isOwner ) {
			$image = null;
			$this->bgColor = null;
		}
		
		
		$handler->construct($this->bgColor, $image, $this->isOwner, $this->bgImageMode);
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout ) 
	{
		if( $this->bgImage )
			$Layout->assign('img_url', URL_USERFILES.$this->bgImage);
			
		if( !$this->isOwner )
			$Layout->assign('not_owner', 1);

		$Layout->assign('permission', $this->permission);
			
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form ) 
	{
		$form->getField('image_url')->setValue($this->bgImageUrl);
		$form->getField('image_mode')->setValue( ( $this->bgImageMode ? 1 : 2 ) );
		
		$form->frontend_handler->bind("success", "function() {
			 
			window.location.reload();
			
			
		}");
	}

	
	
	public static function ajax_setBgColor( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		if( !SK_HttpUser::is_authenticated() )
			return;
		
		$service = app_ProfileComponentService::newInstance();
		
		$service->saveBgColor(SK_HttpUser::profile_id(), $params->color);

        $service = new SK_Service('profile_background', SK_HttpUser::profile_id());
        $service->checkPermissions();
        $service->trackServiceUse();
	} 
	
//	public static function ajax_setBgImageMode( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
//	{
//		if( !SK_HttpUser::is_authenticated() )
//			return;
//		
//		$service = app_ProfileComponentService::newInstance();
//		
//		$service->saveBgImageMode(SK_HttpUser::profile_id(), $params->mode);
//	} 
	
	
	/**
	 * Ajax method for updating rates
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_deleteImage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_ProfileComponentService::newInstance();
		
		$service->deleteBgImage(SK_HttpUser::profile_id());
		
	}
	
//	public static function ajax_getBgImage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
//	{
//		$service = app_ProfileComponentService::newInstance();
//
//		$service->saveBgImageMode(SK_HttpUser::profile_id(),$params->mode);
//		
//		$result = $service->getBgInfo(SK_HttpUser::profile_id());
//
//		if( $result['bg_image_mode'] == 1 )
//			return array('image' => URL_USERFILES.$result['bg_image']);
//		else 
//			return array('image' => $result['bg_image_url']);
//		
//	}
	

}