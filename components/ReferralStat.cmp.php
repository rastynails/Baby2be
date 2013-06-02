<?php

class component_ReferralStat extends SK_Component
{
	private $profile_id;
		
	public function __construct( array $params = null )
	{
		parent::__construct('referral_stat');
		
		$this->profile_id = SK_HttpUser::profile_id();
	}

	private static function page() {
		return isset(SK_HttpRequest::$GET["page"]) ? SK_HttpRequest::$GET["page"] : 1;
	}
	
	private static function month() {
		return isset(SK_HttpRequest::$GET["month"]) ? SK_HttpRequest::$GET["month"] : 0;
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('has_referrals', (bool)count(app_Referral::getPersonalReferralsNum($this->profile_id)));
		
		
		$on_page = 12;
		
		$page = self::page();
		$month = self::month();
		$bounds = array();
		if( intval($month) && ($month <= 12)  )
		{
			if($month == 1)
				$bounds[0] = 12;
			else 
				$bounds[0] = $month -1;
			$bounds[1] = $month;
		}
		$start_time = null;
		$end_time = null;
		if ($bounds) {
			if($bounds[0] == 12)
				$start_time = mktime(0, 0, 0, ($bounds[0]+1), 1, (date('Y')-1) );
			else
				$start_time = mktime(0, 0, 0, ($bounds[0]+1), 1, (date('Y')) );
				
			$end_time = mktime(0, 0, 0, ($bounds[1]+1), 1, date('Y'));
			$month = $bounds[1];
		}
		
		$paging = array(
			'total'		=> app_Referral::getPersonalReferralsNum($this->profile_id, $start_time, $end_time),
			'on_page'	=> $on_page,
			'pages'	=> 10
		);
		
		$months = array();
		$months[0]["label"] = SK_Language::text("components.referral_stat.all_time");
		$months[0]["url"] = sk_make_url(null, array('month'=>null));
		$months[0]["selected"] = ($month == 0);
		for ($i = 1 ; $i <= 12; $i++) {
			$months[$i]["label"] = SK_Language::section("i18n.date")->text("month_short_" . $i);
			$months[$i]["url"] = sk_make_url(null, "month=" . $i);
			$months[$i]["selected"] = ($i == $month);
		}
		
		$Layout->assign('months', $months);
		$Layout->assign('paging', $paging);
		
		$start = ($page - 1) * $on_page;
		$count = $on_page;		
		
		
		$referal_info = app_Referral::getPersonalReferralInfo($this->profile_id, $start, $count, $start_time, $end_time);
		
		
				
		$Layout->assign('list', $referal_info);
		return parent::render($Layout);
	}
	
}
