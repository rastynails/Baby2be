<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov , 2008
 * 
 */

class form_BgImageUpload extends SK_Form 
{
	public function __construct()
	{
		parent::__construct( 'bg_image_upload' );
	}
	
	/**
	 * @see SK_Form::setup()
	 *
	 */
	public function setup() 
	{		
		$this->registerField( new BgImageUploadField('bg_image') );
		
		$this->registerField( new fieldType_text('image_url') );
		
		$this->registerField(new fieldType_hidden('image_mode'));
		
		$this->registerAction( new BgImageUploadAction() );
	}

}

class BgImageUploadField extends fieldType_file
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * @see fieldType_file::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup ( SK_Form $form )
	{
		$this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
		//$this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
		$this->max_file_size = 2*1024*1024;
		
		parent::setup( $form );
	}

	
	/**
	 * @see fieldType_file::preview()
	 *
	 * @param SK_TemporaryFile $tmp_file
	 */
	public function preview ( SK_TemporaryFile $tmp_file )
	{
		$file_url = $tmp_file->getURL();
		return '<div><img src="'.$file_url.'" width="100" />&nbsp;&nbsp;&nbsp;<a class="delete_file_btn" style="cursor:pointer;">Delete</a></div>';
		
	}
}

class BgImageUploadAction extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct( 'bg_image_upload' );
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
	    $bg_service = new SK_Service('profile_background', SK_HttpUser::profile_id());
	    
	    if ( $bg_service->checkPermissions() != SK_Service::SERVICE_FULL )
	    {
	        $response->addError($bg_service->permission_message['message']);
	    }
	    else
	    {
    		$file = null;
    		$file_url = null;
    		
    		if( isset( $data['file'] ) && $data['file'] != '' )
    		{
    			$tmp_file = new SK_TemporaryFile( $data['file'] );
    
    			$file_name = 'profile_bg_image_'.SK_HttpUser::profile_id().'.'.$tmp_file->getExtension();
    			
    			$tmp_file->move(DIR_USERFILES.$file_name);
    			
    			$file = $file_name;
    		}
    		
    		if( isset( $data['image_url'] ) && trim($data['image_url']) != '' )
    		{
    			$file_url = $data['image_url'];
    		}
    		
    		$service = app_ProfileComponentService::newInstance();
    		
    		if( is_null($file) )
    			$service->saveBgImageUrl(SK_HttpUser::profile_id(), $file_url);
    		else
    		{
    			if( file_exists(DIR_USERFILES.$file_name) )
    			{
    				$service->saveBgImage(SK_HttpUser::profile_id(), $file, $file_url);
    			}
    		}
    		if( !$data['image_mode'] )
    			$mode = null;
    		elseif( intval($data['image_mode']) === 1 )
    			$mode = 1;
    		else 
    			$mode = 0; 	
    
    		if( $mode === 0 && !isset($file_url) )
    		{
    			$response->addError('');
    			return;	
    		}
    			
    		$service->saveBgImageMode(SK_HttpUser::profile_id(), $mode );
    			
    		$response->addMessage( SK_Language::text('components.profile_background.msg_background_image_submit') );
    		
    		if ( isset($mode) ) {
    			$config = new SK_Config_Section('site');
    			$status = ( $config->Section('automode')->bg_image_status ) ? 'active' : 'approval';
    			
    			$service->setBGImageStatus( SK_HttpUser::profile_id(), $status );
    			
    			$response->addMessage( SK_Language::text('components.profile_background.msg_admin_will_approve') );
    			
                $bg_service->trackServiceUse();
    		}	
	    }	
	}
}




