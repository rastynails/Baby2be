<?php

class component_SendVirtualGift extends SK_Component 
{
	private $sender_id;
	
	private $recipient_id;
	
	private $gifts_list;
	
	private $hidden = false;
	
	private $by_category = false;
	
	public function __construct( array $params = null ) 
	{
	    if ( !app_Features::isAvailable(8) )
	    {
            $this->annul();
	    }
	    
	    if ( isset($params['hidden']) && $params['hidden'] )
	    {
            $this->hidden = true;
	    }
	    
		if ( !isset($params['recipient']) && !$this->hidden ) 
		{
			$this->annul();
		}
		else
		{
			$this->recipient_id = $params['recipient'];
		}
			
		$this->sender_id = SK_HttpUser::profile_id();
        
		if ( !$this->sender_id )
		{
            $this->annul();
		}
		
		// Get list of available gifts
        $this->by_category = app_VirtualGift::categoriesAdded();
		$this->gifts_list = app_VirtualGift::getGiftsList($this->by_category);
		
		parent::__construct('send_virtual_gift');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($this->sender_id, 'status');
		
		$error_msg = null;
		
		if ( $status == 'suspended' )
		{ 
			$error_msg = SK_Language::section('forms.send_message.error_msg')->text('sender_status_suspended');
		}
		
		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked($this->recipient_id, $this->sender_id) )
		{ 
			$error_msg = SK_Language::section('forms.send_message.error_msg')->text('profile_blocked');
		}
				
		$handler = new SK_ComponentFrontendHandler('SendVirtualGift');
		$handler->construct($this->sender_id, $this->recipient_id);			
		$this->frontend_handler = $handler;
        
		if ( $this->by_category )
		{
    		$Frontend->include_js_file(URL_STATIC . 'jquery.cookie.js');
            $Frontend->include_js_file(URL_STATIC . 'jquery.treeview.js');
            
            $Frontend->header_js(
                '$(function() {
                        $("#tree").treeview({
                            collapsed: true,
                            animated: "medium",
                            control:"#sidetreecontrol",
                            prerendered: true,
                            persist: "location"
                        });
                    })  
                ');
		}
		
		parent::prepare($Layout, $Frontend);
	}
	
	
	public function render( SK_Layout $Layout )
	{
        // check service permission
        $give_gift_service = new SK_Service('give_gifts', $this->sender_id);
        $error_msg = null;
        
        $minCost = app_VirtualGift::getGiftMinCost();
        $give_gift_service->setCreditsCost($minCost);
        
        if ( $give_gift_service->checkPermissions() == SK_Service::SERVICE_FULL )
        {
            $Layout->assign('show_cost', $give_gift_service->useCredits());
        
            $Layout->assign('credits_balance', app_UserPoints::getProfilePointsBalance($this->sender_id));
        
            $Layout->assign_by_ref('gifts_list', $this->gifts_list);
            
            $Layout->assign('by_category', $this->by_category);
        }
        else
        {            
            $Layout->assign('permission_msg', $give_gift_service->permission_message['message']);
        }
		
		return parent::render($Layout);
	}
	
	public function handleForm( SK_Form $form) 
	{
		$form->getField('recipient_id')->setValue($this->recipient_id);
		$form->getField('sender_id')->setValue($this->sender_id);
		
		$form->frontend_handler->bind('success', 'function(){
			window.location.reload(true);
		}');
	}
	
}
