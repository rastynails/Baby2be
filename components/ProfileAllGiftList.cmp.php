<?php

class component_ProfileAllGiftList extends SK_Component
{
    private $profile_id;
    
    private $owner_mode;
    
	public function __construct( array $params = null )
	{
        if ( !$params['profile_id'] )
        {
            $this->annul();
        }
        else
        {
            $this->profile_id = $params['profile_id'];
            $this->owner_mode = $this->profile_id == SK_HttpUser::profile_id();
        }
	    
		parent::__construct('profile_all_gift_list');
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

		return parent::prepare($Layout, $Frontend);
	}	
		
	public function render( SK_Layout $Layout )
	{
	    $Layout->assign('username', app_Profile::username($this->profile_id));
	    
	    $page = (int) $_GET['page'] ? (int) $_GET['page'] : 1;
	    $limit = 10;
	    
	    if ( $this->owner_mode )
	    {
            $gifts = app_VirtualGift::getProfileGifts($this->profile_id, 'all', $page, $limit);
            $gift_count = app_VirtualGift::countProfileGifts($this->profile_id, 'all');
	    }
	    else
	    {
            $gifts = app_VirtualGift::getProfileGifts($this->profile_id, 'public', $page, $limit);
            $gift_count = app_VirtualGift::countProfileGifts($this->profile_id, 'public');
	    }
	    
	    $Layout->assign('gifts', $gifts);
	    
	    $Layout->assign('profile_id', $this->profile_id);
	    
	    $Layout->assign('owner_mode', $this->owner_mode);
	    
	    $Layout->assign('gift_count', $gift_count);
	    
        $Layout->assign('paging',array(
            'total'     => $gift_count,
            'on_page'   => $limit,
            'pages'     => 5
        ));
	    
		return parent::render($Layout);
	}
}
