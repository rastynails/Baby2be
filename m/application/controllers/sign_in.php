<?php defined('SYSPATH') OR die('No direct access allowed.');

class Sign_In_Controller extends Controller {
	
	private $session;
		
	function __construct()
	{
		parent::__construct();
		
		$this->session = Session::instance();
		$form = $_POST;
		
		if (SKM_User::is_authenticated())
			url::redirect('home');
		elseif (isset($form['login']) && isset($form['password'])){
			$login = trim($form['login']);
			$pass = trim($form['password']);  
			try {
				$auth = SKM_User::authenticate($login, $pass);
			}
			catch (SKM_UserException $e){
				$field = null;
				switch ($e->getCode()){
					case 1:
						$field = 'login';
						break;
					case 2:
						$field = 'password';
						break;
				}
				
				SKM_Template::addMessage($e->getMessage(), 'error');
				$auth = false;
			}

			if ( $auth ) {
				SKM_Template::addMessage(SKM_Language::text("%mobile.login_complete"));
				
				$url = @$this->session->get_once("requested_url");
				if (isset($url))
					url::redirect($url);
				else 
					url::redirect('home');
			}
			
			url::redirect('sign_in');
		}
	}
	
	public function index()
	{	
		$view = new View('default');
		
		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'home', SKM_Language::text('%nav_doc_item.sign_in') );
		$view->menu = $m->get_view();
				
		$view->content = new View('sign_in');
		
		$view->footer = SKM_Template::footer();
		
		$view->render(TRUE);			
	}
		
}