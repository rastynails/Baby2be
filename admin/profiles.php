<?php

$file_key = 'profiles';

$active_tab = 'statistic';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.rss.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_configs.php' );
//require_once( 'inc/class.admin_language.php' );
//require_once( 'inc/class.admin_membership.php' );
//require_once( 'inc/class.admin_profile.php' );

$frontend = new AdminFrontend();
adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);


require_once( 'inc.admin_menu.php' );


$_page['title'] = 'Profiles';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS . 'frontend.js' );

/**
 * Returns formated precents string
 * 
 * @param integer $total
 * @param integer $part
 * @return string
 */
function getPrecents( $total, $part )
{
	if ( !$total )
		return 0;
		
	return sprintf( "%01.2f", ( $part/$total*100 ) ).'%';
}

/* --- Total Profiles --- */
$total_profiles = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."`" );
$frontend->assign_by_ref( 'total_profiles', $total_profiles );

$moderators = SK_MySQL::query('SELECT COUNT(`profile_id`) FROM '.TBL_SITE_MODERATORS)->fetch_cell();
$frontend->assign('moderators', $moderators);


/* --- Review/Pending Approval --- */
$pending_approval = array
(
	'profiles'			=>	MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `reviewed`='n'" ),
	'photos'			=>	MySQL::fetchField( "SELECT COUNT(`photo_id`) FROM `".TBL_PROFILE_PHOTO."` WHERE `status`='approval'" ),
	'media'				=>	MySQL::fetchField( "SELECT COUNT( `video_id` ) FROM `".TBL_PROFILE_VIDEO."` WHERE `status`='approval'" ),
    'music'				=>	MySQL::fetchField( "SELECT COUNT( `music_id` ) FROM `".TBL_PROFILE_MUSIC."` WHERE `status`='approval'" ),
	'groups'			=>	MySQL::fetchField( "SELECT COUNT( `group_id` ) FROM `".TBL_GROUP."` WHERE `status`='approval'" ),
	'membership_claims'	=>	MySQL::fetchField( "SELECT COUNT(`".TBL_PROFILE."`.`profile_id`)
			FROM `".TBL_PROFILE."` LEFT JOIN `".TBL_MEMBERSHIP_CLAIM."` USING(`profile_id`) WHERE `claim_result`='claim'" ),
	'bg_images'			=>  MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` 
			WHERE `bg_image_status`='approval' AND ( 
			(`bg_image_mode`=0 AND `bg_image_url`!='null' AND `bg_image_url` IS NOT NULL ) 
				OR
			(`bg_image_mode`=1 AND `bg_image`!='null' AND `bg_image` IS NOT NULL )
			)" )
);

if (app_Features::isAvailable(42))
{
    $pending_approval['photo_auth'] = app_PhotoAuthenticate::findPendingResponceCount();
}

$frontend->assign_by_ref( 'pending_approval', $pending_approval );


/* --- Online --- */
$online_profiles_num = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE_ONLINE."`" );
$frontend->assign( 'online_profiles',
	array
	(
		'num'		=>	$online_profiles_num,
		'precent'	=>	getPrecents( $total_profiles, $online_profiles_num )
	)
);



/* --- Membersips --- */
$memberships=array();
$_result = MySQL::fetchResource( "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE."`" );
while( $_row = MySQL::resource2NumArray( $_result ) )
{
	$_profiles_num = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."`
											WHERE `membership_type_id`='{$_row[0]}'" );
	if( $_profiles_num )
	{
		$memberships[$_row[0]] = array
		(
			'label'			=>	SK_Language::section('membership.types')->text($_row[0]),
			'profiles_num'	=>	$_profiles_num,
			'precent'		=>	getPrecents( $total_profiles, $_profiles_num )
		);
	}
}

$_count_in_row = ( ( count( $memberships ) % 4 ) == 1 ) ? 3 : 4;
$membershipsArr = array_chunk( $memberships , $_count_in_row, true );
$frontend->assign_by_ref( 'memberships',  $membershipsArr);



// --- Profile Has Photos --- //
$_positive = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `has_photo`='y'" );
$_negative = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `has_photo`='n'" );
$frontend->assign( 'has_photos',
	array
	(
		'positive_num'		=>	$_positive,
		'positive_precent'	=>	getPrecents( $total_profiles, $_positive ),
		'negative_num'		=>	$_negative,
		'negative_precent'	=>	getPrecents( $total_profiles, $_negative )
	)
);



// --- Statuses --- //
$active_profiles_num = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `status`='active'" );
$on_hold_profiles_num = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `status`='on_hold'" );
$suspended_profiles_num = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `status`='suspended'" );

$frontend->assign( 'statuses',
	array
	(
		'active_num'		=>	$active_profiles_num,
		'active_precent'	=>	getPrecents( $total_profiles, $active_profiles_num ),
		'on_hold_num'		=>	$on_hold_profiles_num,
		'on_hold_precent'	=>	getPrecents( $total_profiles, $on_hold_profiles_num ),
		'suspended_num'		=>	$suspended_profiles_num,
		'suspended_precent'	=>	getPrecents( $total_profiles, $suspended_profiles_num ),
	)
);

// --- Featured profiles --- //

$_featured_yes = MySQL::fetchField( "SELECT COUNT(*) FROM `".TBL_PROFILE."` WHERE `featured`='y'" );
$_featured_no = MySQL::fetchField( "SELECT COUNT(*) FROM `".TBL_PROFILE."` WHERE `featured`='n'" );

$frontend->assign( 'featured', 
	array
	(
		'featured_yes_num' => $_featured_yes,
	 	'featured_yes_precent' => getPrecents( $total_profiles, $_featured_yes ),
	 	'featured_no_num' => $_featured_no,
	 	'featured_no_precent' => getPrecents( $total_profiles, $_featured_no ) 
	 ) );

// --- Has multimedia --- //
$_media_yes = MySQL::fetchField( "SELECT COUNT(*) FROM `".TBL_PROFILE."` WHERE `has_media`='y'" );
$_media_no = MySQL::fetchField( "SELECT COUNT(*) FROM `".TBL_PROFILE."` WHERE `has_media`='n'" );

$frontend->assign( 'has_media', 
	array
	(
		'media_yes_num' => $_media_yes,
	 	'media_yes_precent' => getPrecents( $total_profiles, $_media_yes ),
	 	'media_no_num' => $_media_no,
	 	'media_no_precent' => getPrecents( $total_profiles, $_media_no ),
	 ) );
	 
// --- Email Verified --- //
$_yes = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `email_verified`='yes'" );
$_no = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `email_verified`='no'" );
$_undefined = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `email_verified`='undefined'" );

$frontend->assign( 'email_verified',
	array
	(
		'yes_num'			=>	$_yes,
		'yes_precent'		=>	getPrecents( $total_profiles, $_yes ),
		'no_num'			=>	$_no,
		'no_precent'		=>	getPrecents( $total_profiles, $_no ),
		'undefined_num'		=>	$_undefined,
		'undefined_precent'	=>	getPrecents( $total_profiles, $_undefined ),
	)
);

// --- Has mailbox conversations --- //
$_yes = MySQL::fetchField( "SELECT COUNT(`pr`.`profile_id`) FROM `".TBL_PROFILE."` AS `pr`
	INNER JOIN ( SELECT DISTINCT `initiator_id` AS `ID` FROM `".TBL_MAILBOX_CONVERSATION."`
	UNION SELECT DISTINCT `interlocutor_id` AS `ID` FROM `".TBL_MAILBOX_CONVERSATION."`) AS `m`
	ON( `pr`.`profile_id` = `m`.`ID`)" );

$_no = $total_profiles - $_yes;

$frontend->assign( 'has_mail',
	array
	(
		'yes_num'			=>	$_yes,
		'yes_precent'		=>	getPrecents( $total_profiles, $_yes ),
		'no_num'			=>	$_no,
		'no_precent'		=>	getPrecents( $total_profiles, $_no ),
	)
);

// --- Sex Stats --- //
$_result = MySQL::fetchResource( "SELECT `value` FROM `".TBL_PROF_FIELD_VALUE."` WHERE `profile_field_id`='6'" );
while( $_row = mysql_fetch_array( $_result, MYSQL_NUM ) )
{
	$_profiles_num = MySQL::fetchField( "SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."`
												WHERE `sex`='{$_row[0]}'" );
	if( $_profiles_num )
		$sex_stats[$_row[0]] = array
		(
			'label'			=>	SK_Language::section('profile_fields.value')->text('sex_'.$_row[0]),
			'profiles_num'	=>	$_profiles_num,
			'precent'		=>	getPrecents( $total_profiles, $_profiles_num )
		);
}

$_count_in_row = ( ( count( $sex_stats ) % 4 ) == 1 ) ? 3 : 4;
if (is_array($sex_stats))
{
    $sexStatsArr = array_chunk( $sex_stats , $_count_in_row, true );
	$frontend->assign_by_ref( 'sex_stats',  $sexStatsArr);
}
$height = @file_get_contents(SKADATE_SPOTLIGHT_SIZE);
$frontend->assign('spot_height', $height );

//$frontend->assign_by_ref( 'sex_stats', $sex_stats );

// Gettings RSS feeds
//$frontend->assign( 'blog_feeds', getRSS( SKALFA_MAIN_FEED, 3 ) );

$frontend->display( 'profiles.html' );

