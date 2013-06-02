<?php

class component_MemberConsole extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('member_console');
	}
	
	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend) 
	{
		$profile_id = SK_HttpUser::profile_id();
		if (!$profile_id) {
			return false;
		}
		
		$handler = new SK_ComponentFrontendHandler('MemberConsole');
		$handler->construct();
		$this->frontend_handler = $handler;
		
		return parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id();
		
		$profile = array(
					'url' 		=> SK_Navigation::href("view_profile", array('profile_id' => $profile_id)),
					'id'		=> $profile_id,
					'username'	=> app_Profile::username(),
					'hasThumb' => app_ProfilePhoto::hasThumbnail($profile_id),
					'photo_upload_url' => SK_Navigation::href("profile_photo_upload"),
					'mailbox_url' => SK_Navigation::href("mailbox"),
					);
		
			
		$Layout->assign("console_menu", $this->console_menu());	
		$Layout->assign('console_menu_opened', app_ProfilePreferences::get('hidden', 'member_console_menu_state'));
			
		$membership = app_Membership::profileCurrentMembershipInfo($profile_id);
		
		
		$_membership = !($membership["type"] == "subscription" && $membership["limit"] == "unlimited") 
			? SK_Language::section("membership.types")->text($membership["membership_type_id"]) 
			: false;
				
		$Layout->assign("membership", $_membership);
		$Layout->assign("new_items_menu", $this->new_items_menu());			
		$Layout->assign("profile", $profile);
		return parent::render($Layout);
	}
	
     public static function ajax_DeleteThumb($params = null,SK_ComponentFrontendHandler $handler) {
		try 
		{
            app_ProfilePhoto::deleteThumbnail();
		}
		catch (SK_ProfilePhotoException $e) 
		{
			$handler->message(SK_Language::section("components.upload_photo.upload.message")->text("make_thumb_error"));
			$handler->changeThumb();
		}

		$handler->message(SK_Language::section("components.upload_photo.upload.message")->text("thumb_delete_success"));
		$handler->changeThumb(app_ProfilePhoto::getThumbUrl(SK_HttpUser::profile_id()));
        $handler->hideThumbDeleteBtn();
	}
		
	private function new_items_menu()
	{
		$new_items_menu = array();
		
		$lang = SK_Language::section("components.member_console.new_items_menu");
		
		
		if (app_Features::isAvailable( 3 )) {
			$new_items_menu[] = array(
								'label'	=> $lang->text("photo"),
								'url'	=> SK_Navigation::href("profile_photo_upload"),
								"class"	=>	"photo"
								);
		}

		if (app_Features::isAvailable( 23 )) {	
		$new_items_menu[] = array(
							'label'	=> $lang->text("blog"),
							'url'	=>SK_Navigation::href('manage_blog',array('tab'=>'add')),
							"class"	=>	"blog"
							);
		}

		if (app_Features::isAvailable( 22 )) {	
			$new_items_menu[] = array(
								'label'	=> $lang->text("forum"),
								'url'	=> SK_Navigation::href("forum_new_topic"),
								"class"	=>	"forum"
								);
		}
		
		if (app_Features::isAvailable( 4 )) {					
			$new_items_menu[] = array(
								'label'	=> $lang->text("video"),
								'url'	=> SK_Navigation::href("my_video"),
								"class"	=>	"video"
								);
		}
		
		if (app_Features::isAvailable( 6 )) {
			$new_items_menu[] = array(
								'label'	=> $lang->text("event"),
								'url'	=> SK_Navigation::href("events"),
								"class"	=>	"event"
								);
		}

		
		if (app_Features::isAvailable( 33 )) {
			if (SK_Config::section('profile_registration')->invite_access == 'all') {
				$new_items_menu[] = array(
									'label'	=> $lang->text("invite_friend"),
									'url'	=> SK_Navigation::href("invite_friends"),
									"class"	=>	"invite_friend"
									);
			}
		}
		
		return $new_items_menu;
	}
	
    private function console_menu()
    {
        $console_menu = array();

        $doc_key = SK_HttpRequest::getDocument()->document_key;

        $lang = SK_Language::section("components.member_console.menu");

        $console_menu[] = array(
            'label'	=> $lang->text("my_profile"),
            'href'	=> SK_Navigation::href("profile_view"),
            'active'=> $doc_key == "profile_view",
            'class' => "icon_profile_view"
        );

        $console_menu[] = array(
            'label'	=> $lang->text("edit_profile"),
            'href'	=> SK_Navigation::href("profile_edit"),
            'active'=> $doc_key == "profile_edit",
            'class' => "icon_profile_edit"
        );

        if ( app_Features::isAvailable(3) ) {
            $console_menu[] = array(
                'label'	=> $lang->text("my_photo"),
                'href'	=> SK_Navigation::href("profile_photo_upload"),
                'active'=> $doc_key == "profile_photo_upload",
                'class' => "icon_photo_upload"
            );
        }
        
        if ( app_Features::isAvailable(40) ) {
            $console_menu[] = array(
                'label'	=> $lang->text("my_music"),
                'href'	=> SK_Navigation::href("my_music"),
                'active'=> $doc_key == "my_music",
                'class' => "icon_my_music"
            );
        }
			
        if ( app_Features::isAvailable(4) ) {
            $console_menu[] = array(
                'label'	=> $lang->text("my_video"),
                'href'	=> SK_Navigation::href("my_video"),
                'active'=> $doc_key == "my_video",
                'class' => "icon_my_video"
            );
        }

        if ( app_Features::isAvailable(23) ) {
            $console_menu[] = array(
                'label'	=> $lang->text("my_blog"),
                'href'	=> SK_Navigation::href("manage_blog"),
                'active'=> $doc_key == "manage_blog",
                'class' => "icon_manage_blog"
            );
        }
		
        if ( SK_Config::section('chuppo')->enable_chuppo_recorder && app_Features::isAvailable(21) ) {
            $console_menu[] = array(
                'label'	=> $lang->text("my_webcam"),
                'href'	=> SK_Navigation::href("recorder"),
                'active'=> $doc_key == "recorder",
                'class' => "icon_recorder"
            );
        }
        if ( SK_Config::section('chuppo')->enable_chuppo_recorder && app_Features::isAvailable(21) ) {
            $console_menu[] = array(
                'label'	=> $lang->text("profile_unregister"),
                'href'	=> SK_Navigation::href("profile_unregister"),
                'active'=> $doc_key == "profile_unregister",
                'class' => "profile_unregister"
            );
        }

        return $console_menu;
    }
	
	public static function ajax_saveMenuState($params = null, SK_ComponentFrontendHandler $handler) 
	{
		try {
			app_ProfilePreferences::set("hidden", "member_console_menu_state", $params->opened);
		} catch (SK_ProfilePreferencesException $e) {}
	}
}
