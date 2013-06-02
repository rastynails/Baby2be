<?php

final class app_ActivityScheduler 
{
    public static function getStatements()
    {
        $query = "SELECT * FROM `" . TBL_SCHEDULER_STATEMENT . "`";
        $res = SK_MySQL::query($query);
        
        $stList = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $row['conditions'] = self::getStatementConditions($row['statement_id']);
            $row['profile_count'] = self::countStatementProfiles($row['statement_id'], $row['conditions']);
            $row['mail_template'] = self::getStatementTemplate($row['statement_id']);
            $stList[] = $row;
        }
        
        return $stList;
    }
    
    private static function getStatementConditions( $stId )
    {
        $query = SK_MySQL::placeholder("SELECT `t2`.`field_name`, `t1`.`field_value` 
            FROM `" . TBL_LINK_SCHEDULER_STATEMENT_FIELD . "` AS `t1`
            LEFT JOIN `" . TBL_SCHEDULER_FIELD . "` AS `t2` ON (`t1`.`field_id` = `t2`.`field_id`)
            WHERE `t1`.`statement_id` = ?", $stId);
    
        $res = SK_MySQL::query($query);
        
        $condList = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $condList[$row['field_name']] = $row['field_value'];
        }
        
        return $condList;
    }
    
    private static function getStatementProfiles( $stId, $cond, $start, $count )
    {
        $queryCond = self::generateStatementConditionQuery($cond);
        
        $query = SK_MySQL::placeholder("SELECT DISTINCT * FROM `" . TBL_PROFILE . "`
            WHERE " . $queryCond .
            " ORDER BY `profile_id` ASC
            LIMIT ?, ?", $start, $count);
        
        $res = SK_MySQL::query($query);
        $profiles = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $profiles[] = $row;
        }
        
        return $profiles;
    }
    
    private static function countStatementProfiles( $stId, $cond )
    {
        $queryCond = self::generateStatementConditionQuery($cond);
    
        $query = "SELECT COUNT(`profile_id`) FROM `" . TBL_PROFILE . "`
            WHERE " . $queryCond;
            
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    private static function getStatementTemplate( $stId )
    {
        $query = SK_MySQL::placeholder("SELECT `name` 
            FROM `" . TBL_LINK_SCHEDULER_STATEMENT_MAIL . "`
            WHERE `statement_id` = ?", $stId);
        
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    public static function createStatement( $data )
    {
        $name = trim($data['statement_name']);
        $type = $data['statement_type'];
        $mail_template = $data['mail_tpl'];
        
        if ( !$name || !$type )
        {
            return -1;
        }
           
        $activity_start_num = intval($data['activity_stamp_start']);
        $activity_end_num = intval($data['activity_stamp_end']);
        $join_start_num = intval($data['join_stamp_start']);
        $join_end_num = intval($data['join_stamp_end']);
            
        $insert_data['activity_stamp'] = "$activity_start_num:{$data['activity_stamp_start_unit']}-$activity_end_num:{$data['activity_stamp_end_unit']}";
        $insert_data['has_photo'] = $data['has_photo'];
        $insert_data['has_media'] = $data['has_media'];
        $insert_data['reviewed'] = $data['reviewed'];
        $insert_data['membership_type_id'] = $data['membership_type_id'];
        $insert_data['email_verified'] = $data['email_verified'];
        $insert_data['status'] = $data['status'];
        $insert_data['join_stamp'] = "$join_start_num:{$data['join_stamp_start_unit']}-$join_end_num:{$data['join_stamp_end_unit']}";
        $insert_data['sex'] = $data['sex'];
        $insert_data['ignore_unsubscribe'] = $data['ignore_unsubscribe'] == 'on'? 1 : 0;
        
        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_SCHEDULER_STATEMENT . "` (`name`, `type`) 
            VALUES ('?', '?')", $name, $type);
        
        $res = SK_MySQL::query($query);
        $statement_id = SK_MySQL::insert_id();
        
        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_LINK_SCHEDULER_STATEMENT_MAIL . "` (`statement_id`, `name`)
            VALUES (?, '?')", $statement_id, $mail_template);
        
        SK_MySQL::query($query);
        
        $inserted_fields = self::getFields();
        
        foreach ( $inserted_fields as $_field_id => $_field_name )
        {
            $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_LINK_SCHEDULER_STATEMENT_FIELD . "` 
                (`statement_id`, `field_id`, `field_value`)
                VALUES (?, ?, '?')", $statement_id, $_field_id, $insert_data[$_field_name]);
            
            SK_MySQL::query($query);
        }
        
        return $statement_id;
    }
    
    public static function updateStatement( $statement_id, $data )
    {
        $statement_id = intval($statement_id);
        $name = trim($data['statement_name']);
        $type = $data['statement_type'];
        $mail_template = $data['mail_tpl'];
        
        if ( !$name || !$type )
        {
            return -1;
        }
        
        $affected_row = 0;
            
        $activity_start_num = intval($data['activity_stamp_start']);
        $activity_end_num = intval($data['activity_stamp_end']);
        $join_start_num = intval($data['join_stamp_start']);
        $join_end_num = intval($data['join_stamp_end']);
            
        $insert_data['activity_stamp'] = "$activity_start_num:{$data['activity_stamp_start_unit']}-$activity_end_num:{$data['activity_stamp_end_unit']}";
        $insert_data['has_photo'] = $data['has_photo'];
        $insert_data['has_media'] = $data['has_media'];
        $insert_data['reviewed'] = $data['reviewed'];
        $insert_data['membership_type_id'] = $data['membership_type_id'];
        $insert_data['email_verified'] = $data['email_verified'];
        $insert_data['status'] = $data['status'];
        $insert_data['join_stamp'] = "$join_start_num:{$data['join_stamp_start_unit']}-$join_end_num:{$data['join_stamp_end_unit']}";
        $insert_data['sex'] = $data['sex'];
        $insert_data['ignore_unsubscribe'] = $data['ignore_unsubscribe'] == 'on'? 1 : 0;
        
        $query = SK_MySQL::placeholder("UPDATE `" . TBL_SCHEDULER_STATEMENT . "` 
            SET `name` = '?', `type` = '?' 
            WHERE `statement_id` = ?", $name, $type, $statement_id);
        
        SK_MySQL::query($query);
        $affected_row = SK_MySQL::affected_rows();
        
        $query = SK_MySQL::placeholder("UPDATE `" . TBL_LINK_SCHEDULER_STATEMENT_MAIL . "`
            SET `name` = '?'
            WHERE `statement_id` = ?", $mail_template, $statement_id);
        
        SK_MySQL::query($query);
        
        $inserted_fields = self::getFields();
        
        foreach ( $inserted_fields as $_field_id => $_field_name )
        {
            $query = SK_MySQL::placeholder("UPDATE `" . TBL_LINK_SCHEDULER_STATEMENT_FIELD . "` 
                SET `field_value` = '?'
                WHERE `statement_id` = ? AND `field_id` = ?", $insert_data[$_field_name], $statement_id, $_field_id);
            
            SK_MySQL::query($query);
            $affected_row += SK_MySQL::affected_rows();
        }
        
        return $affected_row;
    }
    
    public static function deleteStatement( $statement_id )
    {
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_SCHEDULER_STATEMENT . "` 
            WHERE `statement_id` = ?", $statement_id);
        
        SK_MySQL::query($query);
        
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_LINK_SCHEDULER_STATEMENT_FIELD . "`
            WHERE `statement_id` = ?", $statement_id);
        
        SK_MySQL::query($query);
        
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_LINK_SCHEDULER_STATEMENT_MAIL . "`
            WHERE `statement_id` = ?", $statement_id);
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    private static function getFields()
    {
        $query = "SELECT * FROM `" . TBL_SCHEDULER_FIELD . "`";
        
        $res = SK_MySQL::query($query);
        $fields = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $fields[$row['field_id']] = $row['field_name'];
        }
        
        return $fields;
    }
    
    public static function getMailTemplates()
    {
        $query = "SELECT * FROM `" . TBL_SCHEDULER_MAIL_TPL . "`";
        
        $res = SK_MySQL::query($query);
        $tpls = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $tpls[$row['name']] = $row['label'];
        }
        
        return $tpls;
    }
    
    public static function createMailTemplate( $label, $subject, $body_txt, $body_html )
    {
        $label = trim($label);
        $name = strtolower(preg_replace("/[^\w]/i", "_", $label));
        $subject = trim($subject);
        $body_txt = trim($body_txt);
        $body_html = trim($body_html);
        
        if ( !$label || !$name )
            return -1;
            
        if ( !$subject )
            return -2;
            
        if ( !$body_txt )
            return -3;
            
        if ( !$body_html )
            return -4;
            
        // check if the same exists
        $query = SK_MySQL::placeholder("SELECT `name` FROM `" . TBL_SCHEDULER_MAIL_TPL . "`
            WHERE `name` = '?'", $name);
        
        if ( SK_MySQL::query($query)->fetch_cell() )
            return -5;
            
        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_SCHEDULER_MAIL_TPL . "` (`name`, `label`)
            VALUES ('?', '?')", $name, $label);
        
        SK_MySQL::query($query);
        
        SK_LanguageEdit::setKey('mail_template', 'scheduler_'.$name.'_subject', array(SK_Config::Section('languages')->default_lang_id => $subject));
        SK_LanguageEdit::setKey('mail_template', 'scheduler_'.$name.'_body_txt', array(SK_Config::Section('languages')->default_lang_id => $body_txt));
        SK_LanguageEdit::setKey('mail_template', 'scheduler_'.$name.'_body_html', array(SK_Config::Section('languages')->default_lang_id => $body_html));   
        
        return 1;
    }
    
    public static function deleteMailTemplate( $mail_name )
    {   
        // check if template is used
        $query = SK_MySQL::placeholder("SELECT `statement_id` FROM `" . TBL_LINK_SCHEDULER_STATEMENT_MAIL . "`
            WHERE `name` = '?'", $mail_name);
        
        if ( SK_MySQL::query($query)->fetch_cell() )
            return false;
            
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_SCHEDULER_MAIL_TPL . "`
            WHERE `name` = '?'", $mail_name);
        
        SK_MySQL::query($query);
        
        SK_LanguageEdit::deleteKey('mail_template', 'scheduler_'.$mail_name.'_subject');
        SK_LanguageEdit::deleteKey('mail_template', 'scheduler_'.$mail_name.'_body_txt');
        SK_LanguageEdit::deleteKey('mail_template', 'scheduler_'.$mail_name.'_body_html');
            
        return true;
    }
    
    private static function generateStatementConditionQuery( $data )
    {
        $activityData = self::getStatementTimestamp($data['activity_stamp']);
        $joinData = self::getStatementTimestamp($data['join_stamp']);
    
        $activityStart = self::convertTimestamp($activityData['start_num'], $activityData['start_unit']);
        $activityEnd = self::convertTimestamp($activityData['end_num'], $activityData['end_unit']);
        $joinStart = self::convertTimestamp($joinData['start_num'], $joinData['start_unit']);
        $joinEnd = self::convertTimestamp($joinData['end_num'], $joinData['end_unit']);
    
        if ( $activityStart )
            $query .= SK_MySQL::placeholder("`activity_stamp` < ( UNIX_TIMESTAMP() - ?) AND ", $activityStart);
        
        if ( $activityEnd )
            $query .= SK_MySQL::placeholder("`activity_stamp` > ( UNIX_TIMESTAMP() - ?) AND ", $activityEnd);
            
        if ( $joinStart )
            $query .= SK_MySQL::placeholder("`join_stamp` < ( UNIX_TIMESTAMP() - ?) AND ", $joinStart);
            
        if ( $joinEnd )
            $query .= SK_MySQL::placeholder("`join_stamp` > ( UNIX_TIMESTAMP() - ?) AND ", $joinEnd);    

        if ( $data['has_photo'] )
            $query .= SK_MySQL::placeholder("`has_photo` = '?' AND ", $data['has_photo']);
        
        if ( $data['has_media'] )
            $query .= SK_MySQL::placeholder("`has_media` = '?' AND ", $data['has_media']);        
            
        if ( $data['reviewed'] )
            $query .= SK_MySQL::placeholder("`reviewed` = '?' AND ", $data['reviewed']);

        if ( $data['membership_type_id'] )
            $query .= SK_MySQL::placeholder("`membership_type_id` = ? AND", $data['membership_type_id']);

        if ( $data['email_verified'] )
            $query .= SK_MySQL::placeholder("`email_verified` = '?' AND", $data['email_verified']);

        if ( $data['status'] )
            $query .= SK_MySQL::placeholder("`status`= '?' AND ", $data['status']);
        
        if ( $data['sex'] )
            $query .= SK_MySQL::placeholder("`sex` = ? AND ", $data['sex']);
               
        if ( !$query )
            $query .= '1';
        else 
            $query = substr($query, 0, -4);
    
        return $query;
    }

    public static function getStatementTimestamp( $stamp )
    {
        $stampData = explode('-', $stamp);
        $stampStartData = explode(':', $stampData[0]);
        $stampEndData = explode(':', $stampData[1]);
        
        $return['start_num'] = $stampStartData[0];
        $return['start_unit'] = $stampStartData[1];
        $return['end_num'] = $stampEndData[0];
        $return['end_unit'] = $stampEndData[1];
        
        return $return;
    }
    
    private static function convertTimestamp( $number, $unit )
    {
        $number = intval($number);
    
        if ( !$number )
            return false;
        
        switch ( $unit )
        {
            case 'second': return $number;
            case 'minute': return $number * 60; 
            case 'hour': return $number * 3600;
            case 'day': return $number * 86400;
            case 'month': return $number * 2592000;
        }
    }
    
    public static function explodeTimestampConfig( $value )
    {
        $tmp = explode('-', $value);
        
        $data = array
        (
            'minute' => $tmp[0],
            'hour' => $tmp[1],
            'day_type' => $tmp[2],
            'day' => $tmp[3],
            'month' => $tmp[4]
        );
        
        return $data;
    }
    
    public static function implodeTimestampConfig( $data )
    {
        if ( $data['run_minute'] > 59 )
            return false;
        
        if ( $data['run_hour'] > 23 )
            return false;
            
        if ( $data['day_type'] == 'week' && $data['run_day'] > 7 )
            return false;
            
        if ( $data['day_type'] == 'month' && $data['run_day'] > 31 )
            return false;
            
        if ( $data['run_month'] > 12 )
            return false;
            
        return $data['run_minute'].'-'.$data['run_hour'].'-'.$data['day_type'].'-'.$data['run_day'].'-'.$data['run_month'];
    }
    
    public static function calculateNextRunTimestamp()
    {
        $runDate = self::explodeTimestampConfig(SK_Config::section('scheduler')->get('mail_scheduler_time'));
        $currentDate = getdate(time());
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentDate['mon'], $currentDate['year']);
        
        if ( $currentDate['hours'] > $runDate['hour'] || $currentDate['hours'] == $runDate['hour'] && $currentDate['minutes'] >= $runDate['minute'] )
        {
            $nextDay = true;
        }
        
        if ( $runDate['day_type'] == 'week' )
        {
            if ( $currentDate['wday'] == $runDate['day'] )
            {
                $day = $nextDay ? $currentDate['mday'] + 7 : $currentDate['mday'];
            }
            else if ( $currentDate['wday'] > $runDate['day'] )
            {
                $day = $currentDate['mday'] + 7 - ($currentDate['wday'] - $runDate['day']);
            }
            else 
            {
                $day = $currentDate['mday'] + $runDate['day'] - $currentDate['wday'];
            }
        }
        else 
        {
            if ( $currentDate['mday'] == $runDate['day'] && $nextDay || $currentDate['mday'] > $runDate['day'] )
            {
                $nextMonth = true;
            }
            $day = $runDate['day'];
        }
        
        if ( $day > $daysInMonth ) {
            $day = $day - $daysInMonth;
            $nextMonth = true;
        }
        
        if ( $runDate['month'] == '*' )
        {
            $month = $nextMonth ? $currentDate['mon'] + 1 : $currentDate['mon'];
        }
        else 
        {
            if ( $currentDate['mon'] > $runDate['month'] || $currentDate['mon'] == $runDate['month'] && $nextMonth )
            {
                $nextYear = true;
            }
            $month = $runDate['month'];
        }
        
        if ( $month > 12 ) {
            $month = 1;
            $nextYear = true;
        }
        
        $year = $nextYear ? $currentDate['year'] + 1 : $currentDate['year'];
        
        return mktime($runDate['hour'], $runDate['minute'], 30, $month, $day, $year);
    }
    
    public static function setNextRunTimestamp( $ts )
    {
        if ( !$ts || $ts <= time() ) { return; }
        
        SK_Config::section('scheduler')->set('next_run_time', $ts);

        return true;
    }
    
    public static function runScheduler()
    {
        $scheduledTime = SK_Config::section('scheduler')->get('next_run_time');
        
        if ( !$scheduledTime )
        {
            $next = self::calculateNextRunTimestamp();
            self::setNextRunTimestamp($next);
            
            return;
        }
        
        if ( $scheduledTime > time() ) { return; }
        
        $next = self::calculateNextRunTimestamp();
        self::setNextRunTimestamp($next);
        
        $newMatches = SK_Config::section('scheduler')->Section('match_list')->get('new_matches') ? true : false;
        $profilesCount = SK_Config::section('scheduler')->Section('match_list')->get('scheduler_mlist_profile_count');
        $random = SK_Config::section('scheduler')->Section('match_list')->get( 'scheduler_mlist_is_random');
        
        $statements = self::getStatements();
        
        foreach ( $statements as $stm )
        {
            if ( $stm['type'] == 'match' && !app_Features::isAvailable(51) )
            {
                continue;
            }
            
            $start = 0; $count = 10;
            while ( true )
            {
                $profiles = self::getStatementProfiles($stm['statement_id'], $stm['conditions'], $start, $count);
                
                if ( empty($profiles) ) { break; }
                
                foreach ( $profiles as $profile )
                {
                    $profileId = (int) $profile['profile_id'];
                    $unsubscribed = app_Unsubscribe::isProfileUnsubscribed($profileId);
                    if ( !$stm['conditions']['ignore_unsubscribe'] && $unsubscribed ) { continue; }
                    
                    if ( $stm['type'] == 'match' )
                    {                       
                        $matchList = app_ProfileList::getMatcheList($profileId, $profilesCount, $random, $newMatches);
                
                        if ( !$matchList ) { continue; }
                
                        $assignVars = array(
                            'profile_match_list_html' => self::generateMatchlistTemplate($matchList),
                            'profile_match_list_txt' => strip_tags(self::generateMatchlistTemplate($matchList, 'txt')),
                        );
                    }
                    
                    if ( !strlen($profile['email']) ) { continue; }
                    
                    try {
                        $msg = app_Mail::createMessage()
                            ->setTpl('scheduler_' . $stm['mail_template'])
                            ->setRecipientEmail($profile['email'])
                            ->setRecipientLangId($profile['language_id'])
                            ->setRecipientProfileId($profileId)
                            ->assignVar('profile_username', $profile['username'])
                            ->assignVar('profile_email', $profile['email'])
                            ->assignVarRange($assignVars)
                            ->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink($profileId));
                            
                        $sent = app_Mail::send($msg);
    
                    } catch ( SK_EmailException $e ) { }

                    if ( $stm['type'] == 'match' && $sent && $newMatches )
                    {
                        foreach ( $matchList as $matchProfile )
                        {
                            self::setMatchSent($profileId, $matchProfile['profile_id']);
                        }
                    }
                }
                
                $start += $count;
            }
        }
    }
    
    public static function generateMatchlistTemplate( $matchList, $templateType = 'html' )
    {
        if ( !$matchList ) { return ''; }
                
        $output = '';
        $cols = 3;
        $counter = $cols; 
        $output .= '<table cellpadding="5">';
        
        foreach ( $matchList as $key => $match )
        {
            $profile_info = app_Profile::getFieldValues(
                $match['profile_id'], array('username', 'country', 'state', 'city', 'custom_location', 'birthdate', 'sex')
            );
            $profile_info['country'] = trim($profile_info['country']) ? $profile_info['country'] : ' ';
            $profile_info['state'] = trim($profile_info['state']) ? ', ' . $profile_info['state'] : ' ';
            $profile_info['city'] = trim($profile_info['city']) ? ', ' . $profile_info['city'] : ' ';
            $profile_info['custom_location'] = (trim($profile_info['custom_location']) && !trim($profile_info['city']) && !trim($profile_info['city'])) ? ', ' . $profile_info['custom_location'] : '';
            $profile_info['age'] = app_Profile::getAge($profile_info['birthdate']);
            $profile_info['sex'] = SK_Language::section('profile_fields.value')->text('sex_' . $profile_info['sex']);
            $profile_info['profile_username'] = $match['username'];
            $profile_info['profile_url'] = SK_Navigation::href('profile', array('profile_id' => $match['profile_id']));
            $profile_info['profile_thumb'] = app_ProfilePhoto::getThumbUrl($match['profile_id']);
    
            $output .= ($counter >= $cols)?'<tr><td>':'<td>';
    
            $output .= SK_Language::section('mail_template')->text('scheduler_match_list_' . $templateType, $profile_info);
            
            if ( $counter >= $cols ) { $counter = 0; }
            
            $counter++;
            $output .= ($counter >= $cols) ? '</td></tr>' : '</td>';
        }
        
        if ( $counter && $cols < count($matchList) )
        {
            for ( $i = 0; $i < ($cols - $counter); $i++ )
            {
                $output .= '<td> </td>';
            }
            $output .= '</tr>';
        }
        $output .= '</table>';
        
        return $output;
    }
    
    private static function setMatchSent( $profileId, $matchProfileId )
    {
        $profileId = intval($profileId);
        $matchProfileId = intval($matchProfileId);
    
        $query = SK_MySQL::placeholder('INSERT INTO `' . TBL_PROFILE_SENT_MATCHES . '` 
            VALUES (?, ?)', $profileId, $matchProfileId);
        
        SK_MySQL::query($query);
        
        return true;
    }
}