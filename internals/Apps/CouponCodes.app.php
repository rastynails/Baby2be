<?php

class app_CouponCodes 
{
	public static function addCode( $code, $start_stamp, $expire_stamp, $membership_id, $percent )
    {
        if ( !strlen($code) || !strlen($start_stamp) || !$percent )
        {
            return false;
        }
        
        preg_match('/^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/', $start_stamp, $match);
        if ( !$match[0] )
        {
            $start_stamp = date('m/d/Y');
        }
        $date = explode('/', $start_stamp);
		$start_ts = mktime(0, 0, 0, $date[0], $date[1], $date[2]);

		preg_match('/^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/', $expire_stamp, $match);
        if ( !$match[0] )
        {
            $expire_stamp = date('m/d/Y', time() + 30*24*60*60);
        }
        $date = explode('/', $expire_stamp);
        $expire_ts = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        
        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_COUPON_CODES."` 
            (`code`, `start_stamp`, `expire_stamp`, `active`, `membership_type_id`, `percent`) 
            VALUES ('?', ?, ?, 1, ?, '?')", 
            $code, $start_ts, $expire_ts, $membership_id, floatval($percent));
        
        SK_MySQL::query($query);
        
        return SK_MySQL::insert_id();
    }
    
    public static function removeCode( $code_id )
    {
        if ( !$code_id )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("DELETE FROM `".TBL_COUPON_CODES."` 
            WHERE `id`=?", $code_id);

        SK_MySQL::query($query);
        
        return SK_MySQL::affected_rows();
    }

    public static function getList( )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_COUPON_CODES."` WHERE 1");
        
        $res = SK_MySQL::query($query);
        $list = array();
        
        while ( $row = $res->fetch_assoc() )
        {
        	if ( $row['expire_stamp'] < time() )
        	{
        		$row['expired'] = true;
        	}
			$row['start_stamp'] = date('m/d/Y', $row['start_stamp']);
        	$row['expire_stamp'] = date('m/d/Y', $row['expire_stamp']);
        	$row['percent'] = floatval($row['percent']);
        	
            $list[] = $row;
        }
        
        return $list;
    }

	public static function updateCodes( $codes, $start_ts, $expire_ts, $membership_ids, $percent )
    {
        $upd = 0;
        
        if ( count($codes) )
        {
            foreach ( $codes as $code_id => $code )
            {
                preg_match('/^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/', $start_ts[$code_id], $match);
                if ( !$match[0] )
                {
                    $start_ts[$code_id] = date('m/d/Y');
                }
                $date = explode('/', $start_ts[$code_id]);
                $start = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
				
                preg_match('/^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/', $expire_ts[$code_id], $match);
                if ( !$match[0] )
                {
                    $expire_ts[$code_id] = date('m/d/Y', time() + 30*24*60*60);
                }
                $date = explode('/', $expire_ts[$code_id]);
                $expire = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
		
                $per = floatval($percent[$code_id]);
                if ( $per > 100 ) { $per = 100; } 
                
                $active = $expire > time();
                
                $query = SK_MySQL::placeholder("UPDATE `".TBL_COUPON_CODES."`
                    SET `code` = '?', `percent` = '?', `start_stamp` = ?, `expire_stamp` = ?, `membership_type_id`=?, `active` = ?
                    WHERE `id` = ?", 
                    $code, $per, $start, $expire, $membership_ids[$code_id], $active, $code_id);
                
                SK_MySQL::query($query);
                
                if ( SK_MySQL::affected_rows() )
                {
                    $upd++;
                }
            }
            
            return $upd > 0;
        }
        
        return false;
    }
    
	public static function getCodeById( $code_id )
    {
        if ( !$code_id )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_COUPON_CODES."`
            WHERE `id`=?", $code_id);
        
        $code = SK_MySQL::query($query)->fetch_assoc();
        
        return $code;
    }
    
	public static function getCode( $code )
    {
        if ( !strlen($code) )
        {
            return false;
        }

        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_COUPON_CODES."`
            WHERE `code`='?' LIMIT 1", $code);
        
        return SK_MySQL::query($query)->fetch_assoc();
    }
        
    public static function codeIsValid( $code )
    {
        if ( !strlen($code) )
        {
            return false;
        }

        $query = SK_MySQL::placeholder("SELECT `id` FROM `".TBL_COUPON_CODES."`
            WHERE `code`='?' AND `expire_stamp` > ? AND `active` = 1", $code, time());
        
        return SK_MySQL::query($query)->fetch_cell() ? true : false;
    }
    
    public static function codeUsed( $profile_id, $code )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_COUPON_TRACK."`
            WHERE `code` = '?' AND `profile_id` = ?", $code, $profile_id);
        
        $res = SK_MySQL::query($query);
        
        return $res->num_rows() ? true : false;
    }
    
    public static function trackCoupon( $code, $profile_id, $membership_type_id )
    {
        if ( !strlen($code) || !$profile_id || !$membership_type_id )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_COUPON_TRACK."` 
            (`profile_id`, `code`, `membership_type_id`, `timestamp`)
            VALUES (?, '?', ?, ?)", 
            $profile_id, $code, $membership_type_id, time());
        
        SK_MySQL::query($query);
        
        return true;
    }
    
    public static function expireCoupons( )
    {
        $query = SK_MySQL::placeholder("UPDATE `".TBL_COUPON_CODES."` SET `active` = 0 
            WHERE `expire_stamp` < ?", time());
        
        SK_MySQL::query($query);
        
        return;
    }
    
    public static function useFullDiscountCoupon( $profile_id, $code, $mtype_id, $plan_id )
    {
        if ( !$profile_id || !strlen($code) || !$mtype_id || !$plan_id ) { return false; }
        
        $plan = app_Membership::getMembershipTypePlanInfo($plan_id);
        if ( !$plan ) { return false; }
        
        $sale_id = app_Finance::FillSale(
            $profile_id, 0, 'coupon', $plan['units'], $plan['period'], 0, $mtype_id
        );

        if ( !$sale_id ) { return false; }
                        
        $membership_id = app_Membership::BuySubscriptionTrialMembership(
            $profile_id, $mtype_id, $plan['period'], $plan['units'], $sale_id
        );
                        
        self::trackCoupon($code, $profile_id, $mtype_id);
        
        return true;
    }
}