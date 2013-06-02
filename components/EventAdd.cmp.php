<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 16, 2008
 * 
 */

class component_EventAdd extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'event_add' );
		
		if( !SK_HttpUser::is_authenticated() )
			$this->annul();		
		
		$this->event_service = app_EventService::newInstance();
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render( SK_Layout $Layout)
	{
		$service = new SK_Service( 'event_submit', SK_httpUser::profile_id() );
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			//TODO remove tracking
		}
		else 
		{
			$Layout->assign( 'err_message', $service->permission_message['message'] );
		}
		
		
		return parent::render( $Layout );		
	}

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $js_code = "
        (function(){
            var contexts = $('.event_date_form');
            $.each( contexts,
                function( index, obj ){
                    var context = $(obj);
                    $(\"select[name='date[day]']\", context).change(function(){ $(\"select[name='endDate[day]']\", context).val($(this).val());});
                    $(\"select[name='date[month]']\", context).change(function(){ $(\"select[name='endDate[month]']\", context).val($(this).val());});
                    $(\"select[name='date[year]']\", context).change(function(){ $(\"select[name='endDate[year]']\", context).val($(this).val());});
                }
            );
        })();
        ";



        $Frontend->onload_js($js_code);
    }
	
}