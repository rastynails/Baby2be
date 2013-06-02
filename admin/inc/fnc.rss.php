<?php

/** 
 * This function is used to recieve RSS Feed from $url, max count of recieved messages is $rss_max
 * also we can recieve RSS FEED from local directory $category_mane 
 * @param  string $url
 * @param integer $rss_max
 * 
 * @return string
 * 
* */
function getRSS( $url, $rss_max )
{
	require_once('class.rss.php');

	$reader = new RSSReader( $url );
	
	$_output = array();
	
	if (is_array($reader->data)) {
		foreach( $reader->data['item']['title'] as $_key => $_value ) {
			$_output[$_key]['title'] = $reader->data['item']['title'][$_key];
			$_output[$_key]['link']	= $reader->data['item']['guid'][$_key];
			$pubdate= $reader->data['item']['pubdate'][$_key];
			$reg='/\d\d:\d\d:\d\d/';
			// cuting date to format D:M:Y
			$out=preg_split($reg,$pubdate,-1,PREG_SPLIT_NO_EMPTY);
			$_output[$_key]['pubdate']=$out[0];	
			if ( $rss_max-1 == $_key )
				break;
		}
	}
	return $_output;
}
?>