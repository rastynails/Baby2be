<?php

class component_ProfileGiftList extends SK_Component
{
    private $profile_id;
    
    private $owner_mode;
    
	public function __construct( array $params = null )
	{
        if ( !$params['profile_id'] )
        {
            $this->profile_id = SK_HttpUser::profile_id();
        }
        else
        {
            $this->profile_id = $params['profile_id'];
            $this->owner_mode = $this->profile_id == SK_HttpUser::profile_id();
        }
	    
		parent::__construct('profile_gift_list');
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
						
		$profile_id = SK_HttpUser::profile_id();

		return parent::prepare($Layout, $Frontend);
	}	
		
	public function render( SK_Layout $Layout )
	{
	    $Layout->assign('username', app_Profile::username($this->profile_id));
	    
	    if ( $this->owner_mode )
	    {
            $gifts = app_VirtualGift::getProfileGifts($this->profile_id, 'all');
            $gift_count = app_VirtualGift::countProfileGifts($this->profile_id, 'all');
	    }
	    else
	    {
            $gifts = app_VirtualGift::getProfileGifts($this->profile_id, 'public');
            $gift_count = app_VirtualGift::countProfileGifts($this->profile_id, 'public');
	    }

	    $Layout->assign('gifts', $gifts);
	    
	    $Layout->assign('profile_id', $this->profile_id);
	    
	    $Layout->assign('owner_mode', $this->owner_mode);
	    
	    $Layout->assign('gift_count', $gift_count);
	    
		return parent::render($Layout);
	}
}
