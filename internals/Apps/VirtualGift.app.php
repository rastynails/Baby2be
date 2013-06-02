<?php

class app_VirtualGift
{

    public static $templateCache = array();

    public static function getGiftsList( $by_category = false )
    {        
        $query = "SELECT * FROM `".TBL_VIRTUAL_GIFT_TPL."` WHERE `status`='active'";
    
        $result = SK_MySQL::query($query);
        $tpl_list = array();
        
        while ( $tpl = $result->fetch_assoc() )
        {
            $tpl['picture'] = self::getGiftPictureSrc($tpl['tpl_id'], $tpl['picture_hash']);
            $tpl_list[] = $tpl;
        }
        
        if ( $by_category )
        {
            $categories = self::getCategoryList();
            $cat_list = array();
            
            foreach ( $categories as $cat )
            {
                $cat_list[$cat['id']]['title'] = $cat['title']; 
                $cat_list[$cat['id']]['tpls'] = array();
            }
            
            foreach ( $tpl_list as $tpl )
            {
                if ( $tpl['category_id'] )
                {
                    $cat_list[$tpl['category_id']]['tpls'][] = $tpl;
                }
            }
            
            return $cat_list;
        }
        
        return $tpl_list;
    }
    
    public static function categoriesAdded()
    {
        return (bool)SK_MySQL::query(
                "SELECT COUNT(`t`.`tpl_id`) FROM `".TBL_VIRTUAL_GIFT_TPL."` AS `t`
                INNER JOIN `".TBL_VIRTUAL_GIFT_CATEGORY."` AS `c` ON (`t`.`category_id`=`c`.`id`)
                WHERE `t`.`status` = 'active'"
            )->fetch_cell();
    }
    
    public static function getCategoryList()
    {
        $query = "SELECT * FROM `".TBL_VIRTUAL_GIFT_CATEGORY."` WHERE 1 ORDER BY `order` ASC";
        
        $res = SK_MySQL::query($query);
        
        $list = array();
        while ( $row = $res->fetch_assoc() )
        {
            $list[] = $row;
        }
        
        return $list;
    }
    
    public static function getGiftMinCost( )
    {
        $query = "SELECT MIN(`credits`) FROM `".TBL_VIRTUAL_GIFT_TPL."` WHERE `status`='active'";
        
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    public static function getProfileGifts( $profile_id, $status, $page = 1, $limit = 8 )
    {
        if ( !$profile_id )
        {
            return false;
        }
        
        $page = $page ? $page : 1;
        $limit_q = " LIMIT ".( ( $page - 1 ) * $limit ).",$limit";
        
        $status_q = $status == 'public' ? " AND `g`.`status`='public'" : '';
        
        $query = SK_MySQL::placeholder("SELECT `g`.*, `gt`.*, `p`.`username` FROM `".TBL_VIRTUAL_GIFT_TPL."` AS `gt`
            LEFT JOIN `".TBL_VIRTUAL_GIFT."` AS `g` ON (`g`.`tpl_id`=`gt`.`tpl_id`)
            LEFT JOIN `".TBL_PROFILE."` AS `p` ON (`g`.`sender_id`=`p`.`profile_id`)
            WHERE `g`.`recipient_id`=? $status_q
            ORDER BY `gift_timestamp` DESC" . $limit_q, 
            $profile_id);
        
        $res = SK_MySQL::query($query);
        
        $gifts = array();
        while ( $row = $res->fetch_assoc() )
        {
            if ( $row['username'] == null )
            {
                $row['username'] = SK_Language::text('%label.deleted_member');
            }
            $row['picture'] = self::getGiftPictureSrc($row['tpl_id'], $row['picture_hash']);
            $gifts[] = $row;
        }
        
        return $gifts;
    }
    
    public static function countProfileGifts( $profile_id, $status )
    {
        if ( !$profile_id )
        {
            return false;
        }
        
        $status_q = $status == 'public' ? " AND `g`.`status`='public'" : '';
        
        $query = SK_MySQL::placeholder("SELECT COUNT(`g`.`gift_id`)
            FROM `".TBL_VIRTUAL_GIFT_TPL."` AS `gt`
            LEFT JOIN `".TBL_VIRTUAL_GIFT."` AS `g` ON (`g`.`tpl_id`=`gt`.`tpl_id`)
            WHERE `g`.`recipient_id`=? $status_q", $profile_id);
        
        $res = SK_MySQL::query($query);
        
        return $res->fetch_cell();
    }
    
    /**
     * Returns virtual gift template's info.
     *
     * @param integer $template_id
     * @return array
     */
    public static function getGiftTemplate( $template_id )
    {   
        if ( !is_numeric( $template_id ) )
            return false;

        if ( isset(self::$templateCache[$template_id]) )
        {
            return self::$templateCache[$template_id];
        }

        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_VIRTUAL_GIFT_TPL."` 
           WHERE `tpl_id`=?", $template_id);
    
        $tpl = SK_MySQL::query($query)->fetch_assoc();
        $tpl['picture'] = app_VirtualGift::getGiftPictureSrc($tpl['tpl_id'], $tpl['picture_hash']);
            
        return $tpl;
    }

    public static function getGiftTemplateList( $templateList )
    {
        if ( empty( $templateList ) )
        {
            return array();
        }

        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_VIRTUAL_GIFT_TPL."`
           WHERE `tpl_id` IN ( ?@ )", $templateList);

        $result = SK_MySQL::query($query);

        $tplList = array();

        while ( $tpl = $result->fetch_assoc() )
        {
            $tpl['picture'] = app_VirtualGift::getGiftPictureSrc($tpl['tpl_id'], $tpl['picture_hash']);
            $tplList[] = $tpl;

            self::$templateCache[$tpl['tpl_id']] = $tpl;
        }

        return $tplList;
    }
    
    public static function getGiftById( $gift_id )
    {
        if ( !$gift_id )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("SELECT `g`.*, `gt`.*, `p`.`username`, `p`.`profile_id`, `g`.`status` AS `privacy_status`  
            FROM `".TBL_VIRTUAL_GIFT_TPL."` AS `gt`
            LEFT JOIN `".TBL_VIRTUAL_GIFT."` AS `g` ON (`g`.`tpl_id`=`gt`.`tpl_id`)
            LEFT JOIN `".TBL_PROFILE."` AS `p` ON (`g`.`sender_id`=`p`.`profile_id`)
            WHERE `g`.`gift_id`=?", $gift_id);
        
        $gift = SK_MySQL::query($query)->fetch_assoc();
        $gift['picture'] = self::getGiftPictureSrc($gift['tpl_id'], $gift['picture_hash']);
        
        return $gift;         
    }
    
    public static function isGiftRecipient( $profile_id, $gift_id )
    {
        if ( !$profile_id || !$gift_id )
        {
            return false;
        }
        
        $gift = self::getGiftById($gift_id);
        
        return $gift['recipient_id'] == $profile_id;
    }
    
    public static function deleteGift( $gift_id )
    {
        if ( !$gift_id )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("DELETE FROM `".TBL_VIRTUAL_GIFT."`
            WHERE `gift_id`=?", $gift_id);
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    /**
     * Sends virtual gift to profile.
     *
     * @param integer $sender
     * @param integer $recipient
     * @param integer $template_id
     * @return boolean
     */
    public static function sendVirtualGift( $sender_id, $recipient_id, $template_id, $sign, $is_private )
    {
        $template_id = intval($template_id);
        $recipient_id = intval($recipient_id);
        $sender_id = intval($sender_id);
        $sign = trim($sign);
        
        $status = $is_private ? 'private' : 'public'; 
        
        if ( !$recipient_id || !$sender_id || !$template_id )
            return false;

        $tpl = self::getGiftTemplate($template_id);
        
        if ( $tpl )
        {
            $gift_id = self::addGift($sender_id, $recipient_id, $template_id, $sign, $status);
            
            if ( $gift_id )
            {
                $sender = app_Profile::username($sender_id);
                $subject = SK_Language::text('%components.send_virtual_gift.subject', array('sender' => $sender));
                $message = SK_Language::text(
                    '%components.send_virtual_gift.message',
                    array(
                        'gift_image' => self::getGiftImagePattern($tpl['tpl_id']),
                        'gift_url' => SK_Navigation::href('gift', array('gid' => $gift_id))
                    )
                );
                
                $message = str_replace('{$gift_text}', $sign, $message);
                
                $msg_sent = app_MailBox::sendSystemMessage($recipient_id, $subject, $message);
                
                // notify
                self::sendEmailNotification($sender_id, $recipient_id, $gift_id);

                /*if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_NEWSFEED,
                            'entityType' => 'send_gift',
                            'entityId' => $gift_id,
                            'userId' => $sender_id,
                            'status' => 'active'
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }*/

                return $msg_sent;
            }
        }
        
        return $false;
    }
    
    public static function getGiftImagePattern( $tpl_id )
    {
        return '[gift]' . $tpl_id . '[/gift]';
    }
    
    public static function replaceGiftImagePattern( $str )
    {
        if ( !strlen($str) )
            return false;

        if ( preg_match("/\[gift\](\d+)\[\/gift\]/", $str, $match) )
        {
            if ( isset($match[0]) && isset($match[1]) )
            {
                $tpl = self::getGiftTemplate($match[1]);
                $html = '<img src="' . self::getGiftPictureSrc($match[1], $tpl['picture_hash']) . '" width="100px" /><br />';
                
                $str = str_replace($match[0], $html, $str);
            }
        }
        
        return trim($str);
    }
    
    private static function addGift( $sender_id, $recipient_id, $tpl_id, $sign, $status )
    {
        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_VIRTUAL_GIFT."`
            (`tpl_id`, `sender_id`, `recipient_id`, `gift_timestamp`, `sign_text`, `status`)
            VALUES (?, ?, ?, ?, '?', '?')", 
            $tpl_id, $sender_id, $recipient_id, time(), $sign, $status);
            
        SK_MySQL::query($query);
        
        return SK_MySQL::insert_id();
    }
    
    public static function getGiftPictureSrc( $tpl_id, $hash )
    {
        if ( !$tpl_id || !$hash )
            return null;
            
        return URL_USERFILES . 'gift_' . $tpl_id . '_' . $hash . '.jpg';
    }
    
    public static function getGiftPicturePath( $tpl_id, $hash )
    {
        if ( !$tpl_id || !$hash )
            return null;
            
        return DIR_USERFILES . 'gift_' . $tpl_id . '_' . $hash . '.jpg';
    }
    
    
    public static function sendEmailNotification( $sender_id, $recipient_id, $gift_id )
    {
        $recipient_id = intval($recipient_id);
        $sender_id = intval($sender_id);
        
        if ( !$recipient_id || !$sender_id || !$gift_id )
        {
            return false;
        }
        
        $recipient_email = app_Profile::getFieldValues($recipient_id, 'email');
        
        if( !strlen($recipient_email) )
        {
            return false;
        }

        if ( app_ProfilePreferences::get('gifts', 'notify_gifts', $recipient_id) )
        {
            $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                ->setRecipientProfileId($recipient_id)
                ->setTpl('notify_about_new_gift')
                ->assignVarRange(array(
                    'recipient_name' => app_Profile::getFieldValues($recipient_id, 'username'), 
                    'sender_name' => app_Profile::getFieldValues($sender_id, 'username'), 
                    'gift_url' => SK_Navigation::href('gift', array('gid' => $gift_id))
                ));
            
            return app_Mail::send($msg);
        }
    }
}