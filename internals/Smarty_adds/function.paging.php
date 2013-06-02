<?php

function smarty_function_paging(  array $params, SK_Layout $Layout )
{
	
	$total = (int)$params['total'];
	$in_page = (int)$params['on_page'];
	$pages_count = (int)$params['pages'] ? (int)$params['pages'] : 5;
	$var = isset($params['var']) ? (string)$params['var'] : 'page';
	
	$url = empty($params['url']) ? null : $params['url']; 
	
	$request_uri = sk_make_url($url);
	
	if (isset($params["exclude"])) {
		$excludes = explode(',', $params["exclude"]);
		
		if (count($excludes)) {
			$_exl = array();
			foreach ($excludes as $item) {
				$_exl[$item] = null;
			}
			$excludes = array_combine($excludes, $_exl);
			
			$request_uri = sk_make_url($url, $excludes);
		}
	}
	
	if( !$total || !$in_page || !$pages_count) return '';
	
	if(($total_pages = ceil($total / $in_page))<=1)
		return '';
	
	$current = @SK_HttpRequest::$GET[$var];
	$current = intval($current) ? $current : 1;
		
	$offset = ceil($pages_count / 2)-1;
	$offset_inc = ($total_pages - $offset)-$current;
	$offset+= ($offset_inc <= 0) ? abs($offset_inc)+(($pages_count%2)?0:1) : 0;
		
	$page = ($current - $offset)>1 ? ($current - $offset) : 1 ;
		
	$paging = '';
	
	for ($counter=1; $counter <= $pages_count && $page <= $total_pages; $counter++)
	{
		$active = ($page == $current) ? 'active' : '';
		$url = sk_make_url($request_uri,array($var=>$page));
		$paging.= "<a class='paging_num {$active}' href='{$url}'>{$page}</a>";
		$page++;
	}
	
	
	
	switch ($current)
	{
		case 1:
			$paging.="<a class='paging_next' href='".sk_make_url($request_uri,array( $var=>$current+1))."'><span>".SK_Language::section('navigation.paging')->text('next_page')."</span></a>";
			$paging.="<a class='paging_last' href='".sk_make_url($request_uri,array( $var=>$total_pages))."'><span>".SK_Language::section('navigation.paging')->text('last_page')."</span></a>";
			break;
			
		case $total_pages:
			$paging= "<a class='paging_prev' href='".sk_make_url($request_uri,array( $var=>$current-1))."'><span>".SK_Language::section('navigation.paging')->text('prev_page')."</span></a>".$paging;
			$paging= "<a class='paging_first' href='".sk_make_url($request_uri,array( $var=>1))."'><span>".SK_Language::section('navigation.paging')->text('first_page')."</span></a>".$paging;
			break;
			
		default:
			$paging= "<a class='paging_prev' href='".sk_make_url($request_uri,array( $var=>$current-1))."'><span>".SK_Language::section('navigation.paging')->text('prev_page')."</span></a>".$paging;
			$paging= "<a class='paging_first' href='".sk_make_url($request_uri,array( $var=>1))."'><span>".SK_Language::section('navigation.paging')->text('first_page')."</span></a>".$paging;
			
			$paging.="<a class='paging_next' href='".sk_make_url($request_uri,array( $var=>$current+1))."'><span>".SK_Language::section('navigation.paging')->text('next_page')."</span></a>";
			$paging.="<a class='paging_last' href='".sk_make_url($request_uri,array( $var=>$total_pages))."'><span>".SK_Language::section('navigation.paging')->text('last_page')."</span></a>";
			break;
	}
	
	$out = '<div class="paging">';
	$out.= '<span class="paging_label">'.SK_Language::section('navigation.paging')->text('pages').': </span>';
	$out.=$paging.'</div>';
	
	return $out;
	
}

