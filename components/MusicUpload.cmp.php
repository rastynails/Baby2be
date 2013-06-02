<?php

class component_MusicUpload extends SK_Component
{
	/**
	 * Source of the music
	 * 
	 * possible values:
	 * <ul>
	 * <li>file</li>
	 * </ul>
	 *
	 * @var string
	 */
	private $music_source;
	
	private $permissions = array();
	
	public function __construct( array $params = null )
	{
		parent::__construct('music_upload');
		
		$this->permissions['allow_uploading'] = SK_Config::section('music')->allow_upload_music_files;
                $this->permissions['allow_embed'] = SK_Config::section('music')->allow_upload_ambed_music_files;
		
		if (!$this->permissions['allow_embed'] && !$this->permissions['allow_uploading'])
			$this->annul();
			
		$sources = array('file', 'embed_code');
		$this->music_source = in_array(SK_HttpRequest::$GET['ms'], $sources) ? SK_HttpRequest::$GET['ms'] : 'file';
		
		if (!$this->permissions['allow_embed'])
			$this->music_source = 'file';
			
		if (!$this->permissions['allow_uploading'])
			$this->music_source = 'embed_code';
     	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('MusicUpload');
		
		$this->frontend_handler->construct();
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$service = new SK_Service('upload_music', $profile_id);
		$service_state = $service->checkPermissions();
           	$perm_msg = null;
		
		if ($service_state == SK_Service::SERVICE_FULL)
		{
			if ($this->permissions['allow_embed'])
			{
				$menu_items[] = array (
					'href'	=>	'embed_code',
					'label'	=>	SK_Language::section('components.music_upload')->text('submenu_item_embed'),
					'active'=> ($this->music_source == 'embed_code' || !$this->permissions['allow_uploading']) ? true : false,
				    'class' => 'embed_code'
				); 	
			}
                        if ($this->permissions['allow_uploading'])
			{
				$menu_items[] = array (
					'href'	 =>	'file',
					'label'	 =>	SK_Language::section('components.music_upload')->text('submenu_item_upload').$msg_count,
					'active' => ($this->music_source == 'file') ? true : false,
				    'class' => 'file'
				);
			}

			$Layout->assign('music_upload_menu_items', $menu_items);
			$Layout->assign('ms', $this->music_source);
		}
		else
		{
			if ( app_ProfileMusic::getProfileMusicCount($profile_id) >= intval(SK_Config::section('music')->get('upload_music_files_limit')))
				$perm_msg = SK_Language::section('components.music_upload')->text('music_limit_exceeded');
			else {
				$perm_msg = $service->permission_message['message']; 
				$Layout->assign('permission_message', $perm_msg);
			}
		}
		
		return parent::render($Layout);
	}
	
	public function handleForm(SK_Form $form)
	{
		$form->getField('profile_id')->setValue(SK_HttpUser::profile_id());	
	}

    public static function clearCompile( $tpl_file = null, $compile_id = null ) 
    {
        $cmp = new self;
        return $cmp->clear_compiled_tpl();
    }

}
