<?php
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Feb 09, 2009
 * 
 */

class component_CustomHtml extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var integer
	 */
	private $cmp_id;
	
	/**
	 * @var app_ProfileComponentService
	 */
	private $pcService;
	
	/**
	 * @var CustomHtml
	 */
	private $custom_html;
	
	/**
	 * @var boolean
	 */
	private $ownerMode;
	
	/**
	 * @var SK_Service
	 */
	private $service;
	
	/**
	 * Constructor
	 *
	 * @param array $params
	 */
	public function __construct( array $params = null )
	{
		parent::__construct( 'custom_html' );
		$this->profile_id = (int)$params['profile_id'];
		$this->cmp_id = (int)$params['cmp_id'];
		$this->pcService = app_ProfileComponentService::newInstance();
		
		$this->custom_html = $this->pcService->getCustomHtmlForCmp($this->cmp_id);
		
		$this->ownerMode = ( $params['profile_id'] == SK_HttpUser::profile_id() ) ? true : false;
		
		$this->service = new SK_Service( 'custom_html', $this->profile_id );
		
		if ( $this->service->checkPermissions()!= SK_Service::SERVICE_FULL && !$this->ownerMode ) {
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
		$handler = new SK_ComponentFrontendHandler('CustomHtml');
		$this->frontend_handler = $handler;
		$param = $this->ownerMode ? 1 : 0;
		$handler->construct($param);
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$Layout->assign('cap_label',($this->custom_html == null || $this->custom_html->getCap_label() === null ? SK_Language::text('components.custom_html.default_cap_label') : $this->custom_html->getCap_label()));
		$Layout->assign('html',($this->custom_html == null || $this->custom_html->getHtml_code() === null ? SK_Language::text('components.custom_html.default_html') : app_TextService::stOutputFormatter($this->custom_html->getHtml_code(),'profile_view', false)));
		
		$Layout->assign('owner', $this->ownerMode);
		$Layout->assign('permission', $this->service->permission_message['message']);	
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		
		$label = ($this->custom_html === null || $this->custom_html->getCap_label() === null) ? '' : $this->custom_html->getCap_label();
		$html =  ($this->custom_html === null || $this->custom_html->getHtml_code() === null) ? '' : $this->custom_html->getHtml_code();
		
		$form->getField('html_cap')->setValue($label);
		$form->getField('custom_html')->setValue($html);
		$form->getField('cmp_id')->setValue($this->cmp_id);
		
		$form->frontend_handler->bind("success", "function(data) {
			this.ownerComponent.submitCustomHtml({cmp_id:$this->cmp_id,profile_id:$this->profile_id});
		}");
		
		
	}

	
	/**
	 * Ajax method for component adding
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
//	public static function ajax_updateCustomHtml( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
//	{
//		$service = app_ProfileComponentService::newInstance();
//		
//		
//	} 

}