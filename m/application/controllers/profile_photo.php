<?php

class Profile_Photo_Controller extends Controller {
	
	function __construct()
	{
		parent::__construct();
		
		if ( !SKM_User::is_authenticated() ) 
		{
			Session::instance()->set("requested_url", "/".url::current());
			SKM_Template::addMessage(SKM_Language::text('%nav_doc_item.headers.sign_in'), 'notice'); 
			url::redirect('sign_in');
		}
	}
	
	public function view( $profile, $photo_id ) 
	{
		if (!strlen($profile) || !$photo_id )
		{
			url::redirect('not_found');
		}
		else
		{
			$view = new View('default');
			$view->header = SKM_Template::header();
				
			$profile_id = app_Profile::getProfileIdByUsername($profile);
			
			if (!$profile_id) {
				url::redirect('not_found');
			}
			else 
			{
				$owner_id = app_ProfilePhoto::photoOwnerId($photo_id);
				
				if ( $owner_id != SKM_User::profile_id() ) 
				{
					// track photo view
					$service = new SK_Service('view_photo', SKM_User::profile_id());
					if ( $service->checkPermissions() != SK_Service::SERVICE_FULL ) {
						$not_avaliable = true;
						SKM_Template::addTplVar("permission", array('avaliable' => !$not_avaliable, 'msg' => $service->permission_message["message"]));
					} else {
						if ( app_ProfilePhoto::trackPhotoView( $photo_id, $profile_id)) {
							$service->trackServiceUse();
						}
						SKM_Template::addTplVar("permission", array('avaliable' => true));
					}
				}
				else
					SKM_Template::addTplVar("permission", array('avaliable' => true));
		
				$photo_info = app_ProfilePhoto::getPhoto($photo_id);
				
				$photo = array (
					'status'			=>	$photo_info->status,
					'description'		=>	$photo_info->description,
					'html_description'	=>	nl2br(SKM_Language::htmlspecialchars($photo_info->description)),
					'publishing_status'	=>	$photo_info->publishing_status,
					'title'				=>	SKM_Language::htmlspecialchars($photo_info->title),
					'added'				=>	$photo_info->added_stamp
				);

				$photo["owner_name"] = app_Profile::username($owner_id);
				$photo["owner_url"] = url::base() . 'profile/' . $profile;
				
				$photo["views"] = app_ProfilePhoto::getViewCount($photo_id);
					
				$photo["locked"] = ($photo["publishing_status"] == "friends_only" 
					&& !app_FriendNetwork::isProfileFriend($owner_id, SKM_User::profile_id()));
		
				if ($photo["locked"]) {
					$photo["locked"] = !($owner_id == SKM_User::profile_id());
				}
			
				$photo["src"] = app_ProfilePhoto::getUrl($photo_id, app_ProfilePhoto::PHOTOTYPE_VIEW );
				$photo["fullsize_src"] = app_ProfilePhoto::getUrl($photo_id, app_ProfilePhoto::PHOTOTYPE_FULL_SIZE );
		
				if ($photo["locked"]) {
					switch ($photo["publishing_status"]) {
						case "password_protected":
							$photo["fullsize_src"] = app_ProfilePhoto::password_protected_url();
							$photo["src"] = app_ProfilePhoto::password_protected_url();
							break;
							
						case "friends_only":
							$photo["fullsize_src"] = app_ProfilePhoto::friend_only_url();
							$photo["src"] = app_ProfilePhoto::friend_only_url();
							break;
					}
				}
				
				// menu
				$m = new SKM_Menu('main', 'profile', SKM_Language::text('%nav_menu_item.photo') );
				$view->menu = $m->get_view();
		
				SKM_Template::addTplVar('photo', $photo);
				SKM_Template::addTplVar('title', SKM_Language::text('%mobile.user_photos', array('username' => $profile)));

				$pr_model = new Profile_Model($profile_id);
				$prev = $pr_model->get_prev_photo($profile_id, $photo_id);
				if ($prev)
					SKM_Template::addTplVar('prev', $prev);
					
				$next = $pr_model->get_next_photo($profile_id, $photo_id);
				if ($next)
					SKM_Template::addTplVar('next', $next);
				
				// proifle view
				$photo = new View('photo', SKM_Template::getTplVars());
				$view->content = $photo;
				$view->footer = SKM_Template::footer();
				
				$view->render(TRUE);
			}
		}
	}
	
	public function view_all( $profile ) 
	{
		if ( !strlen($profile) ) {
			url::redirect('not_found');
		}
		else
		{
			$profile_id = app_Profile::getProfileIdByUsername($profile);
			
			if ( !$profile_id ) {
				url::redirect('not_found');
			}
			else 
			{									
				$view = new View('default');
				$view->header = SKM_Template::header(); 
				
				// menu
				$m = new SKM_Menu('main', 'profile', SKM_Language::text('%nav_menu_item.photo') );
				$view->menu = $m->get_view();
				
				$pr_model = new Profile_Model($profile_id);
				$page = 1; 
				$photos = $pr_model->get_photos($profile_id, $page);
								
				if ($photos['total'])
				{
					SKM_Template::addTplVar('photo_url', url::base() . 'profile/'.$profile.'/photo/');
					SKM_Template::addTplVar('photo_sect', SKM_Language::text('%mobile.user_photos', array('username' => $profile)));
					SKM_Template::addTplVar('photos', $photos['list']);
				}
				// proifle view
				$photo = new View('photos', SKM_Template::getTplVars());
				$view->content = $photo;
				$view->footer = SKM_Template::footer();
				
				$view->render(TRUE);
			}
		}
	}
}
