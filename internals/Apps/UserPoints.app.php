<?php

class app_UserPoints
{
    
    /**
     * Add new package
     * 
     * @param $price
     * @param $points
     * @param $desc
     * @return int
     */
    public static function addPackage( $price, $points, $desc ) 
    {
        if ( !floatval($price) || !$points || !count($desc) )
            return false;

        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_PACKAGE."`
            (`price`, `points`) VALUES ('?',?)", $price, $points);
        
        SK_MySQL::query($query);
        
        $package_id = SK_MySQL::insert_id();

        $providers = app_Finance::getSyncPaymentProviders();

        foreach ( $providers as $prov )
        {
            $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_PROVIDER_PACKAGE."`
                (`pp_id`, `package_id`, `product_id`) VALUES (?, ?, '')", $prov, $package_id);
            
            SK_MySQL::query($query);
        }
        
        SK_LanguageEdit::setKey('user_points.packages', 'package_'.$package_id, $desc);
        
        return $package_id;
    }
    
    /**
     * Delete packages
     * 
     * @param $packages
     * @return boolean
     */
    public static function deletePackages( array $packages ) 
    {
        $deleted = 0;
        
        $providers = app_Finance::getSyncPaymentProviders();

        $prov_arr = '';
        foreach ( $providers as $prov )
        {
            $prov_arr .= $prov . ', '; 
        }
        $prov_arr = substr($prov_arr, 0, -2);
        
        foreach ( $packages as $pack )
        {
            $query = SK_MySQL::placeholder("DELETE FROM `".TBL_USER_POINT_PACKAGE."`
                WHERE `package_id`=?", $pack);
            
            SK_MySQL::query($query);
            
            if ( SK_MySQL::affected_rows() )
            {
                $deleted++;
                
                $query = SK_MySQL::placeholder("DELETE FROM `".TBL_USER_POINT_PROVIDER_PACKAGE."` 
                WHERE `pp_id` IN ({$prov_arr}) AND `package_id`=?", $pack);

                SK_MySQL::query($query);
            }
            
            SK_LanguageEdit::deleteKey('user_points.packages', 'package_'.$pack);
        }
        
        return $deleted > 0 ? true : false;
    }
    
    /**
     * Update packages
     * 
     * @param $packages
     * @return boolean
     */
    public static function updatePackages( array $packages ) 
    {
        $updated = 0;

        $providers = app_Finance::getSyncPaymentProviders();
        
        foreach ( $packages as $pack )
        {
            $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_PACKAGE."`
                SET `price`='?', `points`=? 
                WHERE `package_id`=?", $pack['price'], $pack['points'], $pack['package_id']);
            
            SK_MySQL::query($query);
            
            foreach ( $providers as $prov )
            {
                $query = SK_MySQL::placeholder("SELECT `package_id` FROM `".TBL_USER_POINT_PROVIDER_PACKAGE."` 
                    WHERE `pp_id` = ? AND `package_id` = ?", $prov, $pack['package_id']);
                
                if ( !SK_MySQL::query($query)->fetch_cell() )
                {
                    $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_PROVIDER_PACKAGE."`
                        (`pp_id`, `package_id`, `product_id`) VALUES (?, ?, '')", $prov, $pack['package_id']);
                    
                    SK_MySQL::query($query);
                }
            }
            
            if ( SK_MySQL::affected_rows() )
                $updated++; 
        }
        return $updated > 0 ? true : false;
    }
    
    /**
     * Updates payment provider product Ids for packages
     * 
     * @param $provider_id
     * @param $packages
     * @return boolean
     */
    function updateProviderPackages( $provider_id, $packages )
    {
        if ( !is_array($packages) )
            return false;

        foreach ( $packages as $package_id => $product_id ) 
        {
            $query = SK_MySQL::placeholder("REPLACE INTO `".TBL_USER_POINT_PROVIDER_PACKAGE."` SET
                `pp_id` =?, `package_id`=?, `product_id`='?'",
                $provider_id, $package_id, $product_id);
            
            SK_MySQL::query($query);
        }
        
        return true;
    }
    
    /**
     * Returns all created packages
     * 
     * @return array
     */
    public static function getPackages()
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_USER_POINT_PACKAGE."` 
            WHERE 1 ORDER BY `price` ASC");
        
        $packages = array();
        
        $res = SK_MySQL::query($query);
        
        while ( $row = $res->fetch_assoc() )
        {
            $row['price'] = floatval($row['price']); 
            $packages[] = $row;
        }
        
        return $packages;
    }
    
    /**
     * Get Package by package ID
     * 
     * @param int $package_id
     * @return array
     */
    public static function getPackageById( $package_id )
    {
        if ( !$package_id )
            return false;
            
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_USER_POINT_PACKAGE."`
            WHERE `package_id`=?", $package_id);
        
        return SK_MySQL::query($query)->fetch_assoc();
    }
    
    /**
     * Get package sale by sale unique hash
     * 
     * @param string $hash
     * @return array
     */
    public static function getUserPointPackageSaleByHash( $hash )
    {
        if ( !strlen($hash) )
            return false;
            
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_USER_POINT_PACKAGE_SALE."` 
            WHERE `hash`='?'", trim($hash));
        
        $sale_info = SK_MySQL::query($query)->fetch_assoc();
        
        return $sale_info;
    }
    
    /**
     * Add record about unverified sale to sales table 
     * 
     * @param int $profile_id
     * @param int $package_id
     * @param int $pp_id
     * @param string $hash
     * @return boolean | integer
     */
    public static function preparePackageSale( $profile_id, $package_id, $pp_id, $hash )
    {
        if ( !$profile_id || !$package_id || !$pp_id || ! strlen($hash) )
        {
            return false;
        }

        $package = self::getPackageById($package_id);
        
        if ( $package )
        {
            $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_PACKAGE_SALE."`
                (`profile_id`, `package_id`, `pp_id`, `hash`, `price`, `points`, `verified`, `sale_timestamp`, `trans_id`)
                VALUES (?, ?, ?, '?', '?', ?, 0, ?, '')", 
                $profile_id, $package_id, $pp_id, $hash, $package['price'], $package['points'], time());
                
            SK_MySQL::query($query);

            return SK_MySQL::insert_id();
        }
        
        return false;
    }
    
    /**
     * Register & verify package sale
     * 
     * @param string $hash
     * @param string $trans_id
     * @param int $amount
     * @return boolean
     */
    public static function registerPackageSale( $hash, $trans_id, $amount = '' )
    {
        if ( !strlen($hash) )
        {
            return -1;
        }
            
        $sale = self::getUserPointPackageSaleByHash($hash);
        
        if ( $sale['verified'] == 1 )
        {
            return -2;
        }
        
        if ( $amount && $sale['price'] != $amount )
        {
            return -3; 
        }
        
        $updated = self::updateProfilePointsBalance($sale['profile_id'], $sale['points'], 'sale');
       
        if ( $updated )
        {
            // set sale verified
            $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_PACKAGE_SALE."` 
                SET `verified`=1, `trans_id`='?' WHERE `hash`='?'", $trans_id, $hash);
            
            SK_MySQL::query($query);
            
            if ( SK_MySQL::affected_rows() )
            {
                $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                    ->setRecipientProfileId($sale['profile_id'])
                    ->setTpl('purchase_credits')
                    ->assignVar('credits', $sale['points']);
            
                app_Mail::send($msg);

                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Returns provider product id for package
     * 
     * @param int $provider_id
     * @param int $package_id
     * @return array
     */
    public static function getProviderProductIdForUserPointPackage( $provider_id, $package_id )
    {
        if ( !$provider_id || !$package_id )
            return false;
        
        $query = SK_MySQL::placeholder("SELECT `product_id` FROM `".TBL_USER_POINT_PROVIDER_PACKAGE."`
            WHERE `pp_id`=? AND `package_id`=?", $provider_id, $package_id);
        
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    /**
     * Get profile balance of points
     * 
     * @param int $profile_id
     * @return int
     */
    public static function getProfilePointsBalance( $profile_id )
    {
        if ( !$profile_id )
            return false;
            
        $query = SK_MySQL::placeholder("SELECT `balance` FROM `".TBL_USER_POINT_BALANCE."`
            WHERE `profile_id`=?", $profile_id);
        
        return floatval(SK_MySQL::query($query)->fetch_cell());
    }
    
    /**
     * Check if profile has enough credits on balance
     * 
     * @param int $profile_id
     * @param float $cost
     * @return boolean
     */
    public static function checkBalance( $profile_id, $cost )
    {
        $pr_points = self::getProfilePointsBalance($profile_id);
        
        return ( $pr_points < $cost ) ? false : true;
    }
    
    /**
     * Give profile credits.
     * 
     * @param int $profile_id
     * @param float $amount
     * @return boolean
     */
    public static function giveCreditsByAdmin( $profile_id, $amount, $comment )
    {
        if ( !$profile_id )
        {
            return false;
        }
        
        $amount = floatval($amount);
        
        $userBalance = self::getProfilePointsBalance($profile_id);
        
        if ( $userBalance + $amount >= 999999 )
        {
            return false;
        }
        
        if ( self::updateProfilePointsBalance($profile_id, $amount, 'inc') )
        {
            $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                ->setRecipientProfileId($profile_id)
                ->setTpl('admin_give_credits')
                ->assignVar('amount', $amount)
                ->assignVar('comment', strlen($comment) ? $comment : '');

            app_Mail::send($msg);
            
            self::logAction($profile_id, 'given_by_admin', $amount, 0, '+');
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get error message informing about unsufficient amount of credits
     * 
     * @param string $service
     * @param string $msg_type
     * @return string
     */
    public static function getErrorMessage( $service, $msg_type = 'message' )
    {
        $upgrade_page = SK_Navigation::href('payment_selection');
        $points_page = SK_Navigation::href('points_purchase');
        
        // html
        $str = SK_Language::text('%membership.service_permission_insufficient_funds_on_balance');
        $html = str_replace(
            array('{$service}', '<url1>', '</url1>', '<url2>', '</url2>'),
            array($service, '<a href="'.$points_page.'" target="_blank">', '</a>', '<a href="'.$upgrade_page.'" target="_blank">', '</a>'),
            $str
        );

        // plain text 
        $text = SK_Language::text(
            '%membership.service_permission_insufficient_funds_on_balance_alert',
            array('service' => $service)
        );
        
        switch ( $msg_type )
        {
            case 'alert':
                return $text;
           
            case 'all':
                return array('message' => $html, 'alert' => $text);

            case 'message':
            default:
                return $html;    
        }        
    }


    /**
     * Decreases | increases profile balance of points
     * 
     * @param int $profile_id
     * @param int $amount
     * @param boolean $decrease
     * @return boolean
     */
    public static function updateProfilePointsBalance( $profile_id, $amount, $action = 'dec' )
    {
        if ( !$profile_id || floatval($amount) == 0 )
            return false;
        
        switch ( $action )
        {
            case 'inc':
                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_USER_POINT_BALANCE."`
                    WHERE `profile_id`=?", $profile_id);
                
                if ( SK_MySQL::query($query)->fetch_cell() )
                {
                    $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_BALANCE."` SET `balance`=`balance`+'?'
                        WHERE `profile_id`=?", $amount, $profile_id);
                }
                else
                {
                    $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_BALANCE."` 
                        (`profile_id`, `balance`) VALUES (?, '?')", $profile_id, $amount);
                }
                
                SK_MySQL::query($query);

                if ( SK_MySQL::affected_rows() || SK_MySQL::insert_id() )
                    return true;

                break;
                
            case 'dec':
                
                $current = self::getProfilePointsBalance($profile_id);
                
                if ( !$current || $current < $amount )
                    return false;
                 
                $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_BALANCE."` SET `balance`=`balance`-'?'
                    WHERE `profile_id`=?", $amount, $profile_id);
                
                SK_MySQL::query($query);
                
                if ( SK_MySQL::affected_rows() )
                    return true;
                        
                break;
                
            case 'sale':

                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_USER_POINT_BALANCE."`
                    WHERE `profile_id`=?", $profile_id);
                
                if ( SK_MySQL::query($query)->fetch_cell() )
                {
                    $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_BALANCE."` SET `balance`=`balance`+'?'
                        WHERE `profile_id`=?", $amount, $profile_id);
                }
                else
                {
                    $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_BALANCE."` 
                        (`profile_id`, `balance`) VALUES (?, '?')", $profile_id, $amount);
                }
                
                SK_MySQL::query($query);

                if ( SK_MySQL::affected_rows() || SK_MySQL::insert_id() )
                    return true;
                
                break;
        }
        
        return false;
    }
    
    /**
     * Delete expired unverified package sales
     *
     */
    public static function deleteExpiredPackageSales()
    {
        $query = "DELETE FROM `".TBL_USER_POINT_PACKAGE_SALE."` 
            WHERE `sale_timestamp` < " . (time() - 60 * 60 * 24 * 30) . " AND `verified`=0";
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    /**
     * Delete expired credits history
     *
     */
    public static function deleteExpiredCreditsHistory( )
    {
        $query = "DELETE FROM `".TBL_USER_POINT_ACTION_LOG."` 
            WHERE `log_timestamp` < " . (time() - 60 * 60 * 24 * 30 * 3);
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    /**
     * Store data about package sale for logging purposes
     * 
     * @param $hash
     * @param $log
     * @param $file
     * @param $line
     * @return boolean
     */
    public static function logSale( $hash, $log, $file = '', $line = '' )
    {
        $string = '';
        
        if ( is_array($log) )
        {
            foreach ( $log as $key => $value )
                $string .= "$key => $value\n";
        }
        elseif ( is_bool($log) )
        {
            $string = $log ? 'true' : 'false';
        }
        else
        {
            $string = $log;
        }
        
        $log_string = "File-$file; line-$line.\n$string\n\n";
        
        $query = SK_MySQL::placeholder("SELECT `sale_log` FROM `".TBL_USER_POINT_PACKAGE_SALE."`
            WHERE `hash`='?'", $hash);
        
        $old = SK_MySQL::query($query)->fetch_cell();

        if ( strlen($old) )
        {
            $log_string = $old . "\n" . $log_string;
        }

        $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_PACKAGE_SALE."`
            SET `sale_log`='?' WHERE `hash`='?'", $log_string, $hash);

        SK_MySQL::query($query);

        if ( SK_MySQL::affected_rows() )
            return true;
        
        return false;
    }
    
    /**
     * Checks if service allows using credits
     * 
     * @param string $service
     * @return boolean
     */
    public static function useCreditsForService( $service )
    {
        if ( !strlen($service) )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("SELECT `use_credits` FROM `".TBL_MEMBERSHIP_SERVICE."`
            WHERE `membership_service_key` = '?'", $service);
        
        $use = SK_MySQL::query($query)->fetch_cell();
        
        return $use == 1 ? true : false; 
    }
    
    /**
     * Checks if action is active and allows earning credits
     * 
     * @param unknown_type $action
     * @return unknown_type
     */
    public static function giveCreditsForAction( $action )
    {
        if ( !strlen($action) )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("SELECT `active` FROM `".TBL_USER_POINT_ACTION."`
            WHERE `action_name` = '?'", $action);
        
        $give = SK_MySQL::query($query)->fetch_cell();
        
        return $give == 1 ? true : false; 
    }    
    
    /**
     * Returns amount of credits given for an action
     * 
     * @param string $action
     * @return float
     */
    public static function getActionAmount( $action )
    {
        if ( !strlen($action) )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("SELECT `credits` FROM `".TBL_USER_POINT_ACTION."`
            WHERE `action_name`='?' LIMIT 1", $action);
        
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    /**
     * Returns service cost (in credits)
     * 
     * @param string $service
     * @return float
     */
    public static function getServiceCost( $service )
    {
        if ( !strlen($service) )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("SELECT `credits` FROM `".TBL_MEMBERSHIP_SERVICE."`
            WHERE `membership_service_key`='?' LIMIT 1", $service);
        
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    /**
     * Gives profile credits for specified action 
     * 
     * @param int $profile_id
     * @param string $action
     * @param float $amount
     * @return boolean
     */
    public static function earnCreditsForAction( $profile_id, $action, $amount = null )
    {
        if ( !$profile_id || !strlen($action) )
        {
            return false;
        }
        
        if ( !app_Features::isAvailable(44) || !self::giveCreditsForAction($action) )
        {
            return;
        }
        
        if ( $amount == null )
        {
            $amount = self::getActionAmount($action); 
        }
        
        if ( self::updateProfilePointsBalance($profile_id, $amount, 'inc') )
        {
            self::logAction($profile_id, $action, $amount, 0, '+');
            
            $query = SK_MySQL::placeholder("SELECT `notify` FROM `".TBL_USER_POINT_ACTION."` 
                WHERE `action_name`='?'", $action);
            $notify = SK_MySQL::query($query)->fetch_cell();
            
            if ( $notify )
            {
                $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                    ->setRecipientProfileId($profile_id)
                    ->setTpl('credits_earn_notification')
                    ->assignVarRange(array(
                        'action_name' => SK_Language::text('%user_points.actions.'.$action),
                        'amount' => floatval($amount)
                    ));
        
                app_Mail::send($msg);
            }
        }
        
        return;
    }

    /**
     * Gives profile credits for actions, tracks action
     * 
     * @param int $profile_id
     * @param string $action_name
     * @param boolean $check_exists
     * @param int $days
     * @return boolean
     */
    public static function earnCreditsForActionAlternative( $profile_id, $action_name, $check_exists = false, $days = 1 )
    {
        if ( !$profile_id || !app_Features::isAvailable(44) || !self::giveCreditsForAction($action_name) )
        {
            return false;
        }
            
        $query = SK_MySQL::placeholder("SELECT `at`.`stamp` FROM `".TBL_USER_POINT_ACTION_TRACK."` AS `at`
            LEFT JOIN `".TBL_USER_POINT_ACTION."` AS `a` ON (`a`.`action_id`=`at`.`action_id`)
            WHERE `at`.`profile_id`=? AND `a`.`action_name`='?'", $profile_id, $action_name);
        
        $stamp = SK_MySQL::query($query)->fetch_cell();

        if ( ($check_exists && !$stamp) || (time() - $stamp > 60 * 60 * 24 * (int)$days) )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_USER_POINT_ACTION."` 
                WHERE `action_name`='?'", $action_name);
            $action = SK_MySQL::query($query)->fetch_assoc();
            
            if ( !$action['action_id'] )
            {
                return false;
            }
                    
            $query = SK_MySQL::placeholder("REPLACE INTO `".TBL_USER_POINT_ACTION_TRACK."` 
                SET `profile_id`=?, `action_id`=?, `stamp`=?", $profile_id, $action['action_id'], time());
            
            SK_MySQL::query($query);
            
            $amount = self::getActionAmount($action_name); 
        
            if ( self::updateProfilePointsBalance($profile_id, $amount, 'inc') )
            {
                self::logAction($profile_id, $action_name, $amount, 0, '+');
                
                if ( $action['notify'] )
                {
                    $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                        ->setRecipientProfileId($profile_id)
                        ->setTpl('credits_earn_notification')
                        ->assignVarRange(array(
                            'action_name' => SK_Language::text('%user_points.actions.'.$action_name),
                            'amount' => floatval($amount)
                        ));
            
                    app_Mail::send($msg);
                }
            }
        }
    }
        
    
    /**
     * Log action for history
     * 
     * @param int $profile_id
     * @param string $action
     * @param float $amount
     * @param int $service
     * @param string $sign
     * @param int $opponent_id
     * @return boolean
     */
    public static function logAction( $profile_id, $action, $amount, $service = null, $sign = '-', $opponent_id = null )
    {
        if ( !$profile_id )
        {
            return false;
        }
        
        $service = (int)$service;
        
        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_USER_POINT_ACTION_LOG."`
            (`profile_id`, `action`, `service`, `opponent_id`, `sign`, `amount`, `log_timestamp`)
            VALUES (?, '?', ?, ?, '?', '?', ?)", 
            $profile_id, $action, $service, $opponent_id, $sign, $amount, time());
            
        SK_MySQL::query($query);
        
        return true;
    }
    
    /**
     * Get profile's action history
     * 
     * @param int $profile_id
     * @param int $page
     * @return array
     */
    public static function getActionHistory( $profile_id, $page = 1 )
    {
        if ( !$profile_id )
        {
            return false;
        }
        
        $on_page = 20;
        
        $limit = " LIMIT " . ($page - 1) * $on_page . ", " . $on_page;
        
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_USER_POINT_ACTION_LOG."`
            WHERE `profile_id`=? 
            ORDER BY `log_timestamp` DESC $limit", $profile_id);
        
        $res = SK_MySQL::query($query);
        $log = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $row['amount'] = floatval($row['amount']);
            $log[] = $row; 
        }
        
        $query = SK_MySQL::placeholder("SELECT COUNT(`log_id`) FROM `".TBL_USER_POINT_ACTION_LOG."`
            WHERE `profile_id`=?", $profile_id);
        
        $total = SK_MySQL::query($query)->fetch_cell();
        
        return array('list' => $log, 'total' => $total);
    }
    
    public static function getServicesList( )
    {
        $query = "SELECT `s`.*, `f`.`active` FROM `".TBL_MEMBERSHIP_SERVICE."` AS `s`
            INNER JOIN `".TBL_FEATURE."` AS `f` USING( `feature_id` ) 
            WHERE `f`.`active`='yes' AND `s`.`use_credits`=1 AND `s`.`show`='yes' 
            ORDER BY `s`.`order`";
        
        $res = SK_MySQL::query($query);
        $service_list = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $row['credits'] = floatval($row['credits']);
            $service_list[] = $row;
        }
        
        return $service_list; 
    }

}
