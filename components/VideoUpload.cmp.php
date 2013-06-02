<?php

class component_VideoUpload extends SK_Component
{
	/**
	 * Source of the video
	 * 
	 * possible values:
	 * <ul>
	 * <li>file</li>
	 * <li>embed</li>
	 * </ul>
	 *
	 * @var string
	 */
	private $video_source;
	
	private $permissions = array();
	
	public function __construct( array $params = null )
	{
		parent::__construct('video_upload');
		
		$this->permissions['allow_embed'] = SK_Config::section('video')->Section('other_settings')->allow_embed_code;
		$this->permissions['allow_uploading'] = SK_Config::section('video')->Section('other_settings')->allow_upload_files;
		
		if (!$this->permissions['allow_embed'] && !$this->permissions['allow_uploading'])
			$this->annul();
			
		$sources = array('file', 'embed_code');
		$this->video_source = in_array(SK_HttpRequest::$GET['vs'], $sources) ? SK_HttpRequest::$GET['vs'] : 'file';
		
		if (!$this->permissions['allow_embed'])
			$this->video_source = 'file';
			
		if (!$this->permissions['allow_uploading'])
			$this->video_source = 'embed_code';
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('VideoUpload');
		
		$this->frontend_handler->construct();
	}
	
	public function render( SK_Layout $Layout )
	{
		$service = new SK_Service('upload_media', $profile_id);
		$service_state = $service->checkPermissions();
		$perm_msg = null;

		if ($service_state == SK_Service::SERVICE_FULL)
		{
			if ($this->permissions['allow_embed'])
			{
				$menu_items[] = array (
					'href'	=>	'embed_code',
					'label'	=>	SK_Language::section('components.video_upload')->text('submenu_item_embed'),
					'active'=> ($this->video_source == 'embed_code' || !$this->permissions['allow_uploading']) ? true : false,
				    'class' => 'embed_code'
				); 	
			}
			if ($this->permissions['allow_uploading'])
			{
				$menu_items[] = array (
					'href'	 =>	'file',
					'label'	 =>	SK_Language::section('components.video_upload')->text('submenu_item_upload').$msg_count,
					'active' => ($this->video_source == 'file') ? true : false,
				    'class' => 'file'
				);
			}
			
			$Layout->assign('video_upload_menu_items', $menu_items);
			$Layout->assign('vs', $this->video_source);
		}
		else
		{
			if ( app_ProfileVideo::getProfileVideoCount($profile_id) >= intval(SK_Config::section('video')->Section('other_settings')->get('upload_media_files_limit')))
				$perm_msg = SK_Language::section('components.video_upload')->text('video_limit_exceeded'); 			
			else {
				$perm_msg = $service->permission_message['message']; 
				$Layout->assign('permission_message', $perm_msg);
			}
		}
		
		$enable_categories = SK_Config::section('video')->Section('other_settings')->get('enable_categories');
        $Layout->assign('enable_categories', $enable_categories);
		
		return parent::render($Layout);
	}
	
	public function handleForm(SK_Form $form)
	{
		$form->getField('profile_id')->setValue(SK_HttpUser::profile_id());	
	}

	public static function clearCompile($tpl_file = null, $compile_id = null) 
	{
        $cmp = new self;
        return $cmp->clear_compiled_tpl();
    }
	

}
