<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 19, 2008
 * 
 * TO BE DELETED
 * 
 */


class httpdoc_ProfileNote extends SK_HttpDocument
{
    private $event_id;
	private $opponent_id;
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		parent::__construct( 'profile_note' );

        $this->event_id = $params['event_id'];
		$this->opponent_id = $params['opponent_id'];
	}
	
	public function render( SK_Layout $Layout )
	{	
		$profilenote = new component_ProfileNote( array( 'event_id'=>$this->event_id, 'opponent_id' => $this->opponent_id) );
		$Layout->assign('profilenote', $profilenote );
		
		return parent::render($Layout);
	}

}