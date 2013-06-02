<?php
class component_Polls extends SK_Component {
	private $poll = array();
	private $polls = array();
	
	function __construct( array $params ){
		parent::__construct('polls');
		if( isset($_GET['poll']) && intval($_GET['poll']) ){
			$pollId = $_GET['poll'];
			$poll = app_Poll::get($pollId);
			$poll['question'] = SK_Language::section("polls")->text("poll_{$pollId}_question");
			$poll['answers'] = app_Poll::getAnswers($pollId);
			$poll['votes_total'] = app_Poll::getAllVoteCount($pollId);
			
			foreach ( $poll['answers'] as $key => $ans ){
				$poll['answers'][$key]['answer'] = SK_Language::section('polls')->text("poll_{$pollId}_answer_{$ans['id']}");
				$poll['answers'][$key]['votes'] = app_Poll::getVoteCount($pollId, $ans['id']);
				$percent = !intval($poll['votes_total']) ? 0 : ($poll['answers'][$key]['votes']/$poll['votes_total']) *100;
				$poll['answers'][$key]['percent'] = is_integer($percent) ? $percent : sprintf('%.1f', $percent);  
			}
			
			$this->poll = $poll;
			$this->poll['my_choice'] = app_Poll::getProfileAnswer($pollId, SK_HttpUser::profile_id());
			if($this->poll['my_choice'] !== false)
			{
				return $this->polls = array();
			}
		}
		
		$polls = (!is_null($_GET['id']) && intval($_GET['id'])>0 )? array(app_Poll::get($_GET['id'])) : app_Poll::getList(0, app_Poll::getCount());
		
		foreach ( $polls as $_key=>$_poll ){
			$pollId = $polls[$_key]['id'];
			$polls[$_key]['question'] = SK_Language::section("polls")->text("poll_{$pollId}_question");
			$polls[$_key]['answers'] = app_Poll::getAnswers($pollId);
			$polls[$_key]['votes_total'] = app_Poll::getAllVoteCount($pollId);
			$polls[$_key]['my_choice'] = app_Poll::getProfileAnswer($pollId, SK_HttpUser::profile_id());
			foreach ( $polls[$_key]['answers'] as $key => $ans ){
				$polls[$_key]['answers'][$key]['answer'] = @SK_Language::section('polls')->text("poll_{$pollId}_answer_{$ans['id']}");
				$polls[$_key]['answers'][$key]['votes'] = app_Poll::getVoteCount($pollId, $ans['id']);
				
				$percent = (!intval($polls[$_key]['votes_total']))? 0: ($polls[$_key]['answers'][$key]['votes']/$polls[$_key]['votes_total']) *100;

				$polls[$_key]['answers'][$key]['percent'] = is_integer($percent) ? $percent : sprintf('%.1f', $percent);
			}
		}

		$this->polls = $polls; 
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render ( SK_Layout $Layout )  {
		$Layout->assign('poll', $this->poll);
		$Layout->assign('polls', $this->polls);
		
		return parent::render($Layout);
	}

}
?>