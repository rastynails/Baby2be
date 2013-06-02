<?php defined('SYSPATH') OR die('No direct access allowed.');

class Im_Controller extends Controller
{	
	private $content;
	
	private $session;

	function __construct()
	{
		parent::__construct();

		$this->session = Session::instance(); 
	}
	
	public function iframe( $opponent, $session_id )
	{
		$stylesheet =  html::stylesheet('application/media/css/im.css', 'screen');
		SKM_Template::addTplVar('css_include', $stylesheet);
			
		if ( !SKM_User::is_authenticated() ) 
		{
			SKM_Template::addTplVar('session_expired', SKM_Language::text('%mobile.session_expired'));
		}
		else 
		{
			// getting new messages
			$new_messages = IM_Model::getNewMessages( $session_id, null );
			
			$opponent_id = app_Profile::getProfileIdByUsername($opponent);
			
			if ( strstr( Kohana::user_agent(), "Safari") || strstr( Kohana::user_agent(), "Opera Mobi") )
			{
				SKM_Template::addTplVar('js_refresh', true);
			}
	
			try {
				$session = IM_Model::getSession($opponent_id);
				$session->ping();
			}
			catch ( SK_ServiceUseException $e ) {
				echo $e->getHtmlMessage();
			}
					
			if ( count($new_messages) ) 
			{
				SKM_Template::addTplVar('messages', $new_messages);
			}
				
			SKM_Template::addTplVar('is_online', app_Profile::isOnline($opponent_id));
			SKM_Template::addTplVar('offline', SKM_Language::text('mobile.offline', array('opponent' => $opponent )));
						
			SKM_Template::addTplVar('profile_id', SKM_User::profile_id());
			
		}
		
		$view = new View('imiframe', SKM_Template::getTplVars());
		$view->render(TRUE);
	}
	
	public function index( $opponent )
	{
		if (!SKM_User::is_authenticated()) {
			$this->session->set("requested_url","/".url::current()); 
			url::redirect('sign_in');
		}
		
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main', 'home', SKM_Language::text('nav_doc_item.private_chat'));
		$view->menu = $m->get_view();
		
		$opponent_id = app_Profile::getProfileIdByUsername($opponent);
		$opener_id = SKM_User::profile_id();
		
		$error = false;
		
		if ( !$opponent_id ) 
		{
			url::redirect('not_found');
		}
	    else 
	    {
			// getting session
			try {
				$session = IM_Model::getSession($opponent_id);
			}
			catch ( SK_ServiceUseException $e ) {
				echo $e->getHtmlMessage();
			}
						
			$sess_id = $session->im_session_id();
			
			if (isset($_POST['msg']) && strlen(trim($_POST['msg']))) 
			{
				IM_Model::addMessages(
					array(array('text' => $_POST['msg'], 'color' => '000000')),
					$sess_id,
					$opener_id,
					$opponent_id
				);
			}
			
			$iframe_src = url::base() . 'imiframe/' . $opponent . '/' . $sess_id;
	    
			SKM_Template::addTplVar('iframe_src', $iframe_src);
			SKM_Template::addTplVar('opponent', $opponent);
			SKM_Template::addTplVar('opponent_id', $opponent_id);		
		    SKM_Template::addTplVar('profile_url', url::base() . 'profile/');
		    
    	    $service = new SK_Service('instant_messenger', SKM_User::profile_id());
            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                SKM_Template::addTplVar('service_msg', $service->permission_message['alert']);
            }
	    }
	    
		$im = new View('im', SKM_Template::getTplVars());

		$view->content = $im;
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
}
