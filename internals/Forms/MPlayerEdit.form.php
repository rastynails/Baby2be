<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Feb 10, 2009
 * 
 */

final class form_MPlayerEdit extends SK_Form 
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( )
	{
		parent::__construct( 'm_player_edit' );
	}
	
	/**
	 * @see SK_Form::setup()
	 *
	 */
	public function setup() 
	{
		//$this->registerField(new fieldType_text('html_cap'));
		$text_field = new fieldType_textarea( 'custom_html' );
		$this->registerField(new fieldType_hidden('cmp_id'));
		
		$text_field->maxlength = 1000000;
		$this->registerField( $text_field );
		
		$this->registerAction( new MPlayerEditAction() );
	}

}

class MPlayerEditAction extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('m_player_edit');
	}
	
	public function process( array $data, SK_FormResponse $response, SK_Form $form )
	{
        $service_to_track = new SK_Service('m_player', SK_HttpUser::profile_id());
        
        if ( $service_to_track->checkPermissions() == SK_Service::SERVICE_FULL )
        {

            $service = app_ProfileComponentService::newInstance();
        
            $html = $service->getCustomHtmlForCmp($data['cmp_id']);
            
            if( $html === null )
                $html = new CustomHtml();
            
            //$html->setCap_label($data['html_cap']);
            $html->setHtml_code($data['custom_html']);
            $html->setCmp_id($data['cmp_id']);
            
            $service->saveCustomHtml($html);
        
            $service_to_track->trackServiceUse();
        }
	}
}



