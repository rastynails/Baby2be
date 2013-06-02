<?php

class component_MusicEdit extends SK_Component
{
	private $music_id;
	
	private $hash;
	
	public function __construct( array $params = null )
	{
		if (isset($params['music_id']))
			$this->music_id = $params['music_id'];
		else 
			SK_HttpRequest::showFalsePage();
		
		// check owner
		if (app_ProfileMusic::getMusicOwnerById($this->music_id) != SK_HttpUser::profile_id())
			SK_HttpRequest::showFalsePage();
		
		$this->hash = app_ProfileMusic::getMusicHash($this->music_id);
		
		parent::__construct('music_edit');
	}
	
	public function render( SK_Layout $Layout )
	{
		//$tags_enabled = app_Features::isAvailable(17);
		
		//if ($tags_enabled)
		//	$Layout->assign( 'tags_cmp', new component_TagEdit( array( 'entity_id' => $this->music_id, 'feature' => 'music' ) ) );
		//else
		//	$Layout->assign( 'tags_cmp', false );
		
		$Layout->assign('hash', $this->hash);
		
		return parent::render($Layout);
	}
	
	
	public function handleForm(SK_Form $form)
	{
		$music = app_ProfileMusic::getMusicInfo(SK_HttpUser::profile_id(), $this->hash);
		
		$form->getField('hash')->setValue($this->hash);
		$form->getField('title')->setValue($music['title']);
		$form->getField('description')->setValue($music['description']);
		$form->getField('privacy_status')->setValue($music['privacy_status']);
        $form->getField('profile_id')->setValue($music['profile_id']);

	}
	
}
