<?php

class component_GiftView extends SK_Component
{
    private $gift;
    
	public function __construct( array $params = null )
	{
        if ( !$params['gid'] )
        {
            SK_HttpRequest::showFalsePage();
        }
        else
        {
            $this->gift = app_VirtualGift::getGiftById($params['gid']);
 
            if ( !$this->gift['gift_id'] || ($this->gift['privacy_status'] == 'private' && $this->gift['recipient_id'] != SK_HttpUser::profile_id()) )
            {
                SK_HttpRequest::showFalsePage();
            }
        }
	    
		parent::__construct('gift_view');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        if ( !app_Features::isAvailable(8) ) 
        {
            $this->annul();
            return false;
        }
                        
        $handler = new SK_ComponentFrontendHandler('GiftView');
        
        $params = array(
            'gift_id' => $this->gift['gift_id'],
            'confirm' => SK_Language::text('%components.gift_view.confirm_delete'),
            'location' => SK_Navigation::href('gifts', array('profile_id' => SK_HttpUser::profile_id()))
        );
        
        $handler->construct($params);         
        $this->frontend_handler = $handler;
                
        parent::prepare($Layout, $Frontend);
    }
	
		
	public function render( SK_Layout $Layout )
	{
	    SK_Language::defineGlobal('sender_username', !empty($this->gift['username']) ? $this->gift['username'] : SK_Language::text('%label.deleted_member'));
	    
	    $Layout->assign('gift', $this->gift);
	    
	    $Layout->assign('owner_mode', $this->gift['recipient_id'] == SK_HttpUser::profile_id());
	    
		return parent::render($Layout);
	}
	
    public static function ajax_DeleteGift( SK_HttpRequestParams $params = null, SK_ComponentFrontendHandler $handler) 
    {   
        $profile_id = SK_HttpUser::profile_id();
        
        if ( $params->has('gift_id') && $profile_id )
        {
            $gift_id = $params->gift_id;
            
            if ( app_VirtualGift::isGiftRecipient($profile_id, $gift_id) )
            {
                if ( app_VirtualGift::deleteGift($gift_id) )
                {
                    $handler->message(SK_Language::text("%components.gift_view.deleted"));
                    return array('result' => true);
                }
            }
            else 
            {
                $handler->error("You are not authorized to delete gift");
                return array('result' => false);
            }           
        }
        return array('result' => false);
    }
}
