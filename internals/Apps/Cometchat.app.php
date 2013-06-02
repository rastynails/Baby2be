<?php

class app_Cometchat
{
    public static function isActive()
    {
        return app_Features::isAvailable(56);
    }

    public static function findOnlineFriends( $userId )
    {
        $query = SK_MySQL::placeholder(
            'SELECT DISTINCT f.friend_id FROM ' . TBL_PROFILE . ' p
            INNER JOIN ' . TBL_PROFILE_ONLINE . ' AS o ON p.profile_id=o.profile_id
            INNER JOIN ' . TBL_PROFILE_FRIEND_LIST . ' f ON p.profile_id=f.friend_id
            WHERE f.status="active" AND f.profile_id=?'
         , $userId);

         $out = array();
         $r = SK_MySQL::query($query);
         while ( $item = $r->fetch_cell())
         {
             $out[] = self::getUserDetails($item);
         }

         return $out;
    }

    public static function findOnlineUsers()
    {
        $query = SK_MySQL::placeholder(
            'SELECT p.profile_id FROM ' . TBL_PROFILE . ' p
            INNER JOIN ' . TBL_PROFILE_ONLINE . ' AS o ON p.profile_id=o.profile_id'
         , $userId);

         $out = array();
         $r = SK_MySQL::query($query);
         while ( $item = $r->fetch_cell())
         {
             $out[] = self::getUserDetails($item);
         }

         return $out;
    }

    public static function getUserDetails( $userId )
    {
        $query = SK_MySQL::placeholder("SELECT activity_stamp FROM " . TBL_PROFILE . " WHERE profile_id=?", $userId);

        return array(
            'userid' => $userId,
            'username' => app_Profile::username($userId),
            'lastactivity' => SK_MySQL::query($query)->fetch_cell(),
            'avatar' => $userId,
            'link' => $userId
        );
    }

    public static function embedUserSql( array $userInfo )
    {
        $embedFields = array();
        foreach ( $userInfo as $name => $value )
        {
            $embedFields[] = SK_MySQL::placeholder("'?' $name", $value);
        }

        return '( SELECT ' . implode(', ', $embedFields) . ' )';
    }

    public static function embedUserListSql( array $userList )
    {
        $embed = array();
        foreach ( $userList as $user )
        {
            $embed[] = self::embedUserSql($user);
        }

        return '( ' . implode(' UNION ALL ', $embed) . ' )';
    }
}