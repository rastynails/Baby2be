<?php

class app_Passwords
{
    public static function getKeyExpirationTime()
    {
        return 60 * 60 * 24 * 7;
    }
    
    public static function hashPassword( $password )
    {
        return sha1(SK_PASSWORD_SALT . $password);
    }
    
    public static function getKey( $ident )
    {
        $key = md5(uniqid(time()));
        $query = "INSERT INTO " . TBL_RESTORE_PASSWORD_KEY . " SET `key`='$key', identificator='$ident', timeStamp=" . time();
        
        SK_MySQL::query($query);
        
        return $key;
    }
    
    public static function sendPassword( $email )
    {
        $email = trim( $email );
        
        if ( !strlen( $email ) )
            return false;
            
        $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE."` 
            WHERE `email`='?'", $email);
        
        $profile_id = intval( SK_MySQL::query($query)->fetch_cell() );
        
        if ( !$profile_id )
            return false;
        
        // send notify mail
        $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                ->setRecipientProfileId($profile_id)
                ->setTpl('profile_forgot_pass_request')
                ->assignVar('url', SK_Navigation::href('reset_password', array(
                    'key' => self::getKey($profile_id)
                )));
                
        return app_Mail::send($msg);
    }
    
    public static function getIdentificator( $key )
    {
        $query = SK_MySQL::placeholder('SELECT identificator FROM ' . TBL_RESTORE_PASSWORD_KEY . " WHERE `key`='?'", $key);
        
        return SK_MySQL::query($query)->fetch_cell();
    } 
    
    public static function deleteExpiredKeys()
    {
        $query = "DELETE FROM " . TBL_RESTORE_PASSWORD_KEY . " WHERE timeStamp < " . ( time() - self::getKeyExpirationTime() );

        SK_MySQL::query($query);
    }
    
    public static function deleteByIdentificator($ident)
    {
        $query = "DELETE FROM " . TBL_RESTORE_PASSWORD_KEY . " WHERE identificator='$ident'";

        SK_MySQL::query($query);
    }
}