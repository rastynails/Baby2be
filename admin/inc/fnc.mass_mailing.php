<?php

function composeProfileQueryString( $profile_criterion )
{
    
	if ( !is_array( $profile_criterion ) )
		return '';
		
	if ( is_array( $profile_criterion['sex'] ) )
		$_query_string = " AND `sex`&".array_sum( $profile_criterion['sex'] );

    if ( is_array( $profile_criterion['membership_type_id'] ) )
    {
        $_query_string .= " AND ( 0 ";
        foreach($profile_criterion['membership_type_id'] as $membership_type_id)
        {
            $_query_string .= " OR `membership_type_id`=".$membership_type_id;
        }
        $_query_string .= " ) ";
    }
    
    $f = SK_ProfileFields::get('birthdate');
    if ( $f->required_field ) {
    	if ( $profile_criterion['birthdate_start'] && $profile_criterion['birthdate_end'] )
        {
            $_query_string .= sql_placeholder( " AND `birthdate`<=DATE_SUB(DATE(NOW()),INTERVAL ? YEAR)
                AND  `birthdate`>=DATE_SUB(MAKEDATE(YEAR(NOW()),1) , INTERVAL ? YEAR) ",
                $profile_criterion['birthdate_start'], $profile_criterion['birthdate_end']
            );
        }
    }
    
	if ( strlen( $profile_criterion['status'] ) )
		$_query_string .= sql_placeholder( " AND `status`=?", $profile_criterion['status'] );
		
	if ( strlen( $profile_criterion['reviewed'] ) )
		$_query_string .= sql_placeholder( " AND `reviewed`=?", $profile_criterion['reviewed'] );
	
	if ( strlen( $profile_criterion['email_verified'] ) )
		$_query_string .= sql_placeholder( " AND `email_verified`=?", $profile_criterion['email_verified'] );
		
	if ( intval( $profile_criterion['activity_num'] ) )
		$_query_string .= sql_placeholder( " AND `activity_stamp`<?", ( time() - intval( $profile_criterion['activity_num']*86400 ) ) );
		
	if ( $profile_criterion['has_photo'] )
		$_query_string .= " AND `has_photo`='{$profile_criterion['has_photo']}'";
			
	if ( $profile_criterion['has_media'] )
		$_query_string .= " AND `has_media`='{$profile_criterion['has_media']}'";
			
	/*if( $profile_criterion['ignore_unsubscribe']!='on')
		$_query_string .= " AND `is_unsubscribed_mail` = 0";*/
		
    $_query_string = !empty($_query_string) ? " 1 " . $_query_string : " 1 ";
	
	return $_query_string;
}	

function getProfilesForSending()
{
	$_query = "SELECT `profile_id`, `username`, `email` FROM `".TBL_PROFILE."`
		WHERE ".composeProfileQueryString( $_POST );
        
	return MySQL::fetchArray( $_query );
}
