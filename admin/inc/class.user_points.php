<?php

class UserPoints
{
    /**
     * Returns user credits transactions by specified interval of time.
     *
     * @param string $date_from, format YYYY-mm-dd
     * @param string $date_to, format YYYY-mm-dd
     * @param integer $page
     * @param integer $on_page
     *
     * @return array
     */
    public static function getTransactionsByDate( $date_from, $date_to, $page, $on_page )
    {
        // make up "date from":
        if ( !strlen( $date_from ) || !preg_match ('/^[0-9]{4}-([0-9]{2})-[0-9]{2}$/', $date_from ) ) {
            $date_from_q = '';
        } else {
            $data_arr = explode( '-', $date_from );
            $date_from = mktime (0, 0, 0, $data_arr[1], $data_arr[2], $data_arr[0]);
    
            $date_from_q = " AND `ps`.`sale_timestamp`>'$date_from'";
        }
        
        $page = isset($page) ? $page : 1;
        $limit_q = " LIMIT ".( ( $page - 1 ) * $on_page ).",$on_page";
        
        // make up "date to":
        if ( !strlen( $date_to ) || !preg_match ('/^[0-9]{4}-([0-9]{2})-[0-9]{2}$/', $date_to ) ) {
            $date_to = time();
        } else {
            $data_arr = explode ( '-', $date_to );
            $date_to = mktime (0, 0, 0, $data_arr[1], $data_arr[2] + 1, $data_arr[0]);
        }

        $query = "SELECT `ps`.*, `ps`.`price` AS `income`, `pr`.`username`,`pr`.`profile_id`, `p`.*
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON(`p`.`package_id`=`ps`.`package_id`)
            LEFT JOIN `".TBL_FIN_PAYMENT_PROVIDERS."` AS `pp` ON(`ps`.`pp_id`=`pp`.`fin_payment_provider_id`)
            WHERE `ps`.`sale_timestamp`<'$date_to'$date_from_q AND `ps`.`verified`=1 
            ORDER BY `ps`.`sale_timestamp` DESC $limit_q";
        
        $res = SK_MySQL::query($query);
        
        $fin_info = array();
        while ($row = $res->fetch_assoc())
        {
            $row['income'] = floatval($row['income']);
            $fin_info['list'][] = $row;
        }
        
        $query = "SELECT SUM(`ps`.`price`)
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            WHERE `ps`.`sale_timestamp`<'$date_to'$date_from_q AND `ps`.`verified`=1";
        
        $fin_info['total'] = floatval(SK_MySQL::query($query)->fetch_cell());
        
        $query = "SELECT COUNT(`ps`.`sale_id`)
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON(`p`.`package_id`=`ps`.`package_id`)
            LEFT JOIN `".TBL_FIN_PAYMENT_PROVIDERS."` AS `pp` ON(`ps`.`pp_id`=`pp`.`fin_payment_provider_id`)
            WHERE `ps`.`sale_timestamp`<'$date_to'$date_from_q AND `ps`.`verified`=1";
        
        $fin_info['total_num'] = SK_MySQL::query($query)->fetch_cell();
        
        return $fin_info;
    }
    
    /**
     * Returns user credits transactions by profile.
     *
     * @param string $profile
     * @param integer $page
     * @param integer $on_page
     *
     * @return array
     */
    public static function getTransactionsByProfile( $profile, $page, $on_page )
    {
        // check if argument passed or not:
        if ( !strlen($profile) )
            return array();
        
        // check the argument is a email or username
        $profile_q = preg_match( '/@/', $profile ) ? 'email' : 'username';
                
        $page = isset($page) ? $page : 1;
        $limit_q = " LIMIT ".( ( $page - 1 ) * $on_page ).",$on_page";

        $query = "SELECT `ps`.*, `ps`.`price` AS `income`, `pr`.`username`,`pr`.`profile_id`, `p`.*
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON(`p`.`package_id`=`ps`.`package_id`)
            LEFT JOIN `".TBL_FIN_PAYMENT_PROVIDERS."` AS `pp` ON(`ps`.`pp_id`=`pp`.`fin_payment_provider_id`)
            WHERE `pr`.`$profile_q`='$profile' AND `ps`.`verified`=1 
            ORDER BY `ps`.`sale_timestamp` DESC $limit_q";
        
        $res = SK_MySQL::query($query);
        
        $fin_info = array();
        while ($row = $res->fetch_assoc())
        {
            $row['income'] = floatval($row['income']);
            $fin_info['list'][] = $row;
        }
        
        $query = "SELECT SUM(`ps`.`price`)
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            WHERE `pr`.`$profile_q`='$profile' AND `ps`.`verified`=1";
        
        $fin_info['total'] = floatval(SK_MySQL::query($query)->fetch_cell());
        
        $query = "SELECT COUNT(`ps`.`sale_id`)
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON(`p`.`package_id`=`ps`.`package_id`)
            LEFT JOIN `".TBL_FIN_PAYMENT_PROVIDERS."` AS `pp` ON(`ps`.`pp_id`=`pp`.`fin_payment_provider_id`)
            WHERE `pr`.`$profile_q`='$profile' AND `ps`.`verified`=1";

        $fin_info['total_num'] = SK_MySQL::query($query)->fetch_cell();
        
        return $fin_info;
    }
    
    /**
     * Returns SMS transactions by order.
     *
     * @param string $order
     *
     * @return array
     */
    public static function getTransactionsByOrder( $order )
    {
        if ( !strlen($order) )
            return array();

        $query = "SELECT `ps`.*, `ps`.`price` AS `income`, `pr`.`username`,`pr`.`profile_id`, `p`.*
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON(`p`.`package_id`=`ps`.`package_id`)
            LEFT JOIN `".TBL_FIN_PAYMENT_PROVIDERS."` AS `pp` ON(`ps`.`pp_id`=`pp`.`fin_payment_provider_id`)
            WHERE `ps`.`trans_id`='$order' AND `ps`.`verified`=1 
            ORDER BY `ps`.`sale_timestamp` DESC $limit_q";
        
        $res = SK_MySQL::query($query);
        
        $fin_info = array();
        $total = 0;
        while ($row = $res->fetch_assoc())
        {
            $row['income'] = floatval($row['income']);
            $total += $row['income'];
            $fin_info['list'][] = $row;
        }
        $fin_info['total'] = $total;
        
        $query = "SELECT COUNT(`ps`.`sale_id`)
            FROM `".TBL_USER_POINT_PACKAGE_SALE."` AS `ps`
            LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`ps`.`profile_id`=`pr`.`profile_id`)
            LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON(`p`.`package_id`=`ps`.`package_id`)
            LEFT JOIN `".TBL_FIN_PAYMENT_PROVIDERS."` AS `pp` ON(`ps`.`pp_id`=`pp`.`fin_payment_provider_id`)
            WHERE `ps`.`trans_id`='$order' AND `ps`.`verified`=1";

        $fin_info['total_num'] = SK_MySQL::query($query)->fetch_cell();
        
        return $fin_info;
    }
    
    public static function updateServices( $use_credits, $cost, $show )
    {
        if ( !is_array( $use_credits ) || !is_array( $cost ) )
            return false;

        $result = true;
        foreach ( $use_credits as $serv => $val )
        {
            if ( ($use_credits[$serv] == 1 || $use_credits[$serv] == 0) && (floatval($cost[$serv]) || (int)$cost[$serv] == 0 ) )
            {
                $query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP_SERVICE."`
                    SET `use_credits`=?, `credits`='?', `show`='?' 
                    WHERE `membership_service_key`='?'",
                    $use_credits[$serv], floatval($cost[$serv]), $show[$serv],  $serv);
        
                SK_MySQL::query($query);
                    
                $result = true;
            }
            else{
                $result = false;
            }
        }

        return $result;
    }
    
    public static function getActionsList()
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_USER_POINT_ACTION."`
            WHERE 1");
        
        $res = SK_MySQL::query($query);
        $actions = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $row['credits'] = floatval($row['credits']);
            $actions[] = $row;
        }
        
        return $actions;
    }
    
    public static function updateActions( $active, $credits_cost, $notify )
    {
        if ( !is_array( $active ) || !is_array( $credits_cost ) )
            return false;

        $result = true;
        foreach ( $active as $act_id => $is_active )
        {
            if ( ($active[$act_id] == 1 || $active[$act_id] == 0) && (floatval($credits_cost[$act_id]) || (int)$credits_cost[$act_id] == 0 ) )
            {
                $query = SK_MySQL::placeholder("UPDATE `".TBL_USER_POINT_ACTION."`
                    SET `active`=?, `credits`='?', `notify`=? WHERE `action_id`=?",
                    $is_active, floatval($credits_cost[$act_id]), $notify[$act_id], $act_id);
        
                SK_MySQL::query($query);
                    
                $result = true;
            }
            else{
                $result = false;
            }
        }

        return $result;
    }

}