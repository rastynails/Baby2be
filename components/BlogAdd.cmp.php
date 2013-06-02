<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 *
 */

class component_BlogAdd extends SK_Component
{
	public function __construct()
	{
		parent::__construct( 'blog_add' );
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		$service = new SK_Service( 'add_blog_post', SK_httpUser::profile_id() );
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL || SK_HttpUser::isModerator() )
		{
			//TODO remove tracking
		}
		else
		{
			$Layout->assign( 'err_message', $service->permission_message['message'] );
		}
		
		
		$Layout->assign('menu_array',httpdoc_BlogWorkshop::getBlockMenuArray('add'));
		
		$Layout->assign('images', new component_BlogPostImage());
		
		if( SK_HttpUser::isModerator() && app_Features::isAvailable(37))
		{
			$Layout->assign( 'mdr', true );
		}
		
		return parent::render( $Layout );
	}
}