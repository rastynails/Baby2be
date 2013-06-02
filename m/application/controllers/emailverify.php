<?php defined('SYSPATH') OR die('No direct access allowed.');

class Emailverify_Controller extends Controller
{
	function __construct()
	{
		parent::__construct();
	}

	/*public function emailverify( )
	{
        if ( !SKM_User::is_authenticated() )
        {
            url::redirect('sign_in');
        }
        
        $profile_id = SKM_User::profile_id();
        
        if ( app_Profile::email_verified($profile_id) )
        {
            url::redirect('home');
        }
        
        if ( isset($_POST['emailverify']) && strlen($_POST['email']) )
        {
            $res = $this->verificationForm($_POST['email']);
            
            switch ( $res )
            {
                case 1:
                    SKM_Template::addMessage(SKM_Language::text('%forms.email_verify.messages.success'));
                    url::redirect('home');
                    break;

                case -1:
                    SKM_Template::addMessage(SKM_Language::text("%components.email_verify.email_already_exists"), 'error');
                    break;
            }
            
            url::redirect('emailverify');
        }

        $view = new View('default');

        $view->header = SKM_Template::header();
        
        // menu
        $m = new SKM_Menu('main', 'home', SKM_Language::text('%nav_doc_item.headers.email_verify'));
        $view->menu = $m->get_view();
        
        $email = app_Profile::getFieldValues(SKM_User::profile_id(), 'email');
        SKM_Template::addTplVar('email', $email);
        
        $view->content = new View('emailverify', SKM_Template::getTplVars());
        
        $view->footer = SKM_Template::footer();
        $view->render(TRUE);
	}*/
	
    public function emailverify( $code = null )
    {
        if ( !SKM_User::is_authenticated() )
        {
            url::redirect('sign_in');
        }
        
        $profile_id = SKM_User::profile_id();
        
        if ( app_Profile::email_verified($profile_id) )
        {
            url::redirect('home');
        }
        
        if ( isset($_POST['emailverify']) && strlen($_POST['email']) )
        {
            $res = $this->verificationForm($_POST['email']);
            
            switch ( $res )
            {
                case 1:
                    SKM_Template::addMessage(SKM_Language::text('%forms.email_verify.messages.success'));
                    url::redirect('home');
                    break;

                case -1:
                    SKM_Template::addMessage(SKM_Language::text("%components.email_verify.email_already_exists"), 'error');
                    break;
            }
            
            url::redirect('emailverify');
        }

        $view = new View('default');

        $view->header = SKM_Template::header();
        
        // menu
        $m = new SKM_Menu('main', 'home', SKM_Language::text('%nav_doc_item.headers.email_verify'));
        $view->menu = $m->get_view();

        if ( $code != null )
        {
            $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE_EMAIL_VERIFY_CODE."` 
                WHERE `code`='?' AND `expiration_date` > ?", trim($code), time());
            
            $profile_id = SK_MySQL::query($query)->fetch_cell();
    
            if ( $profile_id == SKM_User::profile_id() )
            {
                // delete code
                $query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_EMAIL_VERIFY_CODE."` 
                    WHERE `profile_id`=?", $profile_id);
                
                SK_MySQL::query($query);
                
                app_Profile::setFieldValue($profile_id, 'email_verified', 'yes');
                
                $info = app_Profile::getFieldValues($profile_id, array('username', 'password'));
                $auth = SKM_User::authenticate($info['username'], $info['password'], false);
    
                url::redirect('home');
            }
        }
        
        $email = app_Profile::getFieldValues(SKM_User::profile_id(), 'email');
        SKM_Template::addTplVar('email', $email);
        
        $view->content = new View('emailverify', SKM_Template::getTplVars());
        
        $view->footer = SKM_Template::footer();
        $view->render(TRUE);
    }
    
    private function verificationForm( $email )
    {
        $profile_id = SKM_User::profile_id();
        
        $email = htmlspecialchars(trim($email));
        $profile_email = app_Profile::getFieldValues($profile_id, 'email');

        if ( $email != $profile_email )
        {
            $result = SK_MySQL::query(
                SK_MySQL::placeholder("SELECT COUNT(*) FROM `".TBL_PROFILE."` WHERE `email`='?'", $email)
            )->fetch_cell();
            
            if ( $result ) 
            {
                return -1;
            }    
        }
        
        try
        {
            $model = new Profile_Model($profile_id);
            
            if ( $model->request_email_verification($email) > 0 ) 
            {
                return 1;
            }
        }
        catch ( SK_EmailException $e )
        {
            return 0;
        }
    }
}
