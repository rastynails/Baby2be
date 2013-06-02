<?php
class component_Poll extends SK_Component {
	private $testId;
	private $test;
	private $isMulti = false;
	
	function __construct($params = null){
		parent::__construct( 'poll' );	
		
		$profileId = SK_HttpUser::profile_id();
		
		if( !$profileId ){$this->annul();return;}
		$testToPass = (intval($params['pollId']))? $params['pollId'] : ( ($_POST['command'] == 'test_pass' && ( $keys = array_keys($_POST['test']) ))? $keys[0] : app_Poll::getSingleToVote($profileId) ) ;
		if ( !$testToPass ){$this->annul(); return;}
		

		$this->testId = $testToPass;
		
		
		if($_POST['command'] == 'test_pass'){
			$answerId = $_POST['test'][$this->testId];

			app_Poll::vote($profileId, $this->testId, $answerId);
            app_UserPoints::earnCreditsForAction($profileId, 'answer_poll');
			
			header('location: '.sk_make_url(SK_Navigation::getDocument('polls')->url, "poll={$this->testId}" )); exit();
		}
		
		$test = app_Poll::get($this->testId);
		$test['question'] = @SK_Language::section('polls')->text("poll_{$this->testId}_question");
		$this->test['info'] = $test;

		$anses = app_Poll::getAnswers($this->testId);
		foreach ($anses as $key=>$ans)
			$anses[$key]['answer'] = @SK_Language::section('polls')->text("poll_{$this->testId}_answer_{$ans['id']}");
		
		$this->test['answers'] = $anses;
		
			
		$rightAnswersCount = 0;
	}
	
/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render ( SK_Layout $Layout ) {
		$Layout->assign('test', $this->test);
		
		return parent::render( $Layout );
	}
}
?>