<?php

class component_ProfileActions extends SK_Component
{
    private $profile_id;
    
    private $viewer_id;
    
    /**
     * Class constructor
     *
     */
    public function __construct( $params )
    {
        parent::__construct( 'profile_actions' );
        
        $this->viewer_id = SK_HttpUser::profile_id();
        
        if( empty( $params['profile_id'] ) || empty($this->viewer_id) || $params['profile_id'] == $this->viewer_id )
        {
            $this->annul();
            return;
        }
        
        $this->profile_id = (int)$params['profile_id'];
    }
    
    
    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     */
    public function render ( SK_Layout $Layout ) 
    {
        if ($this->viewer_id) {
            $Layout->assign( 'SendMessage', new component_SendMessage(array( 'sender_id' => $this->viewer_id, 'recipient_id' => $this->profile_id, 'type' => 'new') ) );
            $Layout->assign( 'sendProfile', new component_SendProfile( array( 'profile_id' => $this->profile_id ) ) );
        }

        $Layout->assign('viewer', $this->viewer_id);
        $Layout->assign('profile_id', $this->profile_id);
        
        return parent::render( $Layout );   
    }
    
}