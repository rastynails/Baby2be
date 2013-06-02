<?php

if( !$file_key )
	fail_error( 'undefined <code>$file_key</code>' );

// define sidebar menu items
$sidebar_menu_items = array
(
	'affiliate'	=>	array
	(
		'href'	=>	URL_AFFILIATE.'affiliate_info.php',
		'label'	=>	'Affiliate'
	),
	
	'affiliate_stats' => array
	(
		'href' => URL_AFFILIATE.'index.php',
		'label' => 'Statistics'
	),
	'logout' => array
	(
		'href' => URL_AFFILIATE.'logout.php',
		'label' => 'Logout',
	),
	
);

foreach( $sidebar_menu_items as $key => $item )
{
	$sidebar_menu.= '<div class="';
	
	if( $file_key != $key )
		$sidebar_menu.= 'sidebar_menu_item';
	else
		$sidebar_menu.= 'sidebar_menu_active';
	
	$sidebar_menu.= '">
	<a href="'.$item['href'].'">'.$item['label'].'</a>
</div>';
}


// define tabs menu items
$tabs_menu_items = array
(
	'affiliate' => array
	(
		'home_tab' => array
		(
			'href' => URL_AFFILIATE.'affiliate_info.php',
			'label' => 'Home',
		),
	),
	'affiliate_stats' => array
	(
		'stats_tab' => array
		(
			'href' => URL_AFFILIATE.'index.php',
			'label' => 'Statistics',
		),
	),
);

if( $tabs_menu_items[$file_key] )
	foreach( $tabs_menu_items[$file_key] as $key => $item )
	{
		$tabs_menu.= '<div class="';
		
		if( $active_tab != $key )
			$tabs_menu.= 'tab';
		else
			$tabs_menu.= 'active_tab';
		
		$tabs_menu.= '">
		<a href="'.$item['href'].'">'.$item['label'].'</a>
	</div>';
	}

$tabs_menu.= '<div id="empty_tab"></div>';

$frontend->assign( 'sidebar_menu', $sidebar_menu );
$frontend->assign( 'tabs_menu', $tabs_menu );


?>