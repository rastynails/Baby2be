<?php

class app_TempFileManager
{
    public static function removeTmpFile( $uniqid )
	{
        $uniqid = trim($uniqid);
        $path = self::getTmpFilePath( $uniqid );

        if( !$path )
        {
            return false;
        }

        if( !file_exists($path) || ( $isFile = is_file($path) ) )
        {
        	if ($isFile)
        	{
        		@unlink($path);
        	}
            $query = SK_MySQL::placeholder( "DELETE FROM  `".TBL_TMP_USERFILE."` WHERE `uniqid` = '?' LIMIT 1 ", $uniqid );
            SK_MySQL::query($query);
            return SK_MySQL::affected_rows() ? true : false;
        }

        return false;
	}

    public static function getTmpFileInfo( $uniqid )
	{
        $uniqid = trim($uniqid);
		if( !$uniqid )
            return array();

        $query = SK_MySQL::placeholder( "SELECT * FROM  `".TBL_TMP_USERFILE."` WHERE `uniqid` = '?'" , $uniqid );
        $res = SK_MySQL::query($query);

        return $res->fetch_assoc();
	}

    public static function getTmpFilePath( $uniqid )
	{
        $tmp_file = self::getTmpFileInfo( $uniqid );
        if( !$tmp_file )
            return false;

        $file_type = strtolower(substr($tmp_file['filename'], strrpos($tmp_file['filename'], '.')+1));
        return DIR_TMP_USERFILES.$tmp_file['uniqid'].'.'.$file_type;
	}

    public static function removeExpiredTmpFiles()
    {
        $query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_TMP_USERFILE."` WHERE `uploadstamp`< '?' LIMIT ? " , ( time() - TMP_USERFILES_REMOVE_TIMEOUT ), TMP_USERFILES_DELETE_LIMIT );
        $res = SK_MySQL::query($query);

        while ($tmp_file = $res->fetch_assoc() )
        {
            self::removeTmpFile( $tmp_file['uniqid'] );
        }
    }
}


