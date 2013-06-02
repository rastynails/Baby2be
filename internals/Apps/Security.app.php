<?php

class app_Security
{
    const WHITE_LIST = 0;
    const BLACK_LIST = 1;

    const SESSION_NAME = 'security';

    const ZIP_IP_LIST = 'listed_ip_90.zip';
    const TXT_IP_LIST = 'listed_ip_90.txt';
    const ZIP_EMAIL_LIST = 'listed_email_30.zip';
    const TXT_EMAIL_LIST = 'listed_email_30.txt';

    const DATABASE_DUMP_STOPFORUM = 'http://www.stopforumspam.com/downloads/';
    const DATABASE_DUMP_AMAZON    = 'http://ow.antispam.s3.amazonaws.com/';

    const COUNT_SPAM_ATTEMPT      = 'count_spam_attempt';
    const COUNT_IP_LIST           = 'count_ip_list';
    const COUNT_EMAIL_LIST        = 'count_email_list';
    const TIME_UPDATE_DATABASE    = 'time_update_database';
    const TIME_NEXT_UPDATE        = 'time_next_update';
    const TIME_RESET_SPAM_ATTEMPT = 'time_reset_spam_attempt';

    private static $classInstance;

    private $config;

    private function __construct()
    {
        $this->config = SK_Config::section( 'security' );
    }

    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function isBlackCountry()
    {
        if ( !$this->config->enable )
        {
            return false;
        }
        
        $query = SK_MySQL::placeholder( '
            SELECT `black`.`Country_str_ISO3166_2char_code` AS `code`
            FROM `' . TBL_SECURITY_COUNTRIES . '` AS `black`
                INNER JOIN `' . TBL_LOCATION_COUNTRY_IP . '` AS `country` ON(`black`.`Country_str_ISO3166_2char_code` = `country`.`Country_str_ISO3166_2char_code`)
            WHERE ? BETWEEN `startIpNum` AND `endIpNum`', ip2long($_SERVER['REMOTE_ADDR']) );
        
        return SK_MySQL::query( $query )->fetch_cell() ? true : false;
    }

    private function  isSpamIP()
    {
        if ( !$this->config->enable_block_ip )
        {
            return false;
        }

        $ip = SK_MySQL::query( SK_MySQL::placeholder('SELECT * FROM `' . TBL_SECURITY_IP_LIST . '` WHERE `ip` = "?"', $_SERVER['REMOTE_ADDR']) )->fetch_assoc();

        return ( empty($ip) || $ip['listType'] == self::WHITE_LIST ) ? false : true;
    }

    private function isSpamEmail( $email = '' )
    {
        if ( !$this->config->enable_block_ip || !filter_var($email, FILTER_VALIDATE_EMAIL) )
        {
            return false;
        }

        $_email = SK_MySQL::query( 'SELECT * FROM `' . TBL_SECURITY_EMAIL_LIST . '` WHERE `email` = "' . $email . '"' )->fetch_assoc();

        return !empty( $_email ) ? true : false;
    }
    
    public function check( $email = '' )
    {
        if ( SK_HttpRequest::isIpBlocked() || $this->isBlackCountry() || $this->isSpamIP() || $this->isSpamEmail($email) )
        {
            $this->incSpamAttempt();
            return true;
        }
    }

    private function incSpamAttempt()
    {
        $_SESSION[self::SESSION_NAME]['attempt'] = true;
        $this->config->set( self::COUNT_SPAM_ATTEMPT, $this->config->{self::COUNT_SPAM_ATTEMPT} + 1);
    }

    public function update()
    {
        if ( $this->config->enable_block_ip )
        {
            if ( $this->config->{self::TIME_RESET_SPAM_ATTEMPT} <= time() )
            {
                $this->config->set( self::TIME_RESET_SPAM_ATTEMPT, strtotime('+1 day 00:00:00') );
                $this->config->set( self::COUNT_SPAM_ATTEMPT, 0 );
            }

            if ( $this->config->{self::TIME_NEXT_UPDATE} > time() )
            {
                return;
            }

            $this->config->set( self::TIME_NEXT_UPDATE, strtotime('+1 day 00:00:00') + rand(1, 86400) );
            $this->config->set( self::TIME_UPDATE_DATABASE, time() );

            /* Update IP Black List */
            if ( $this->downloadDump() )
            {
                $this->deleteBlackListIP();
                $this->exctractFile();
                $this->import();
                $this->config->set(self::COUNT_IP_LIST, SK_MySQL::query('SELECT COUNT(*) FROM `' . TBL_SECURITY_IP_LIST . '` WHERE `listType` = ' . self::BLACK_LIST)->fetch_cell() );
                @unlink( DIR_TMP_USERFILES . self::ZIP_IP_LIST );
                @unlink( DIR_TMP_USERFILES . self::TXT_IP_LIST );
            }

            /* Update Email Black List */
            if ( $this->downloadDump(self::ZIP_EMAIL_LIST) )
            {
                $this->truncateEmailList();
                $this->exctractFile( self::ZIP_EMAIL_LIST );
                $this->import( self::TXT_EMAIL_LIST );
                $this->config->set(self::COUNT_EMAIL_LIST, SK_MySQL::query('SELECT COUNT(*) FROM `' . TBL_SECURITY_EMAIL_LIST . '`')->fetch_cell() );
                @unlink( DIR_TMP_USERFILES . self::ZIP_EMAIL_LIST );
                @unlink( DIR_TMP_USERFILES . self::TXT_EMAIL_LIST );
            }
        }
    }

    private function downloadDump( $fileName = self::ZIP_IP_LIST )
    {
        if ( @copy(self::DATABASE_DUMP_STOPFORUM . $fileName, DIR_TMP_USERFILES . $fileName) )
        {
            return true;
        }
        else
        {
            if ( @copy(self::DATABASE_DUMP_AMAZON . $fileName, DIR_TMP_USERFILES . $fileName) )
            {
                return true;
            }
            else
            {
                trigger_error( 'Can`t download ' . $fileName . ' file!' , E_ERROR );
                return false;
            }
        }
    }

    private function deleteBlackListIP()
    {
        SK_MySQL::query( 'TRUNCATE TABLE `' . TBL_SECURITY_IP_LIST . '`' );
    }

    private function truncateEmailList()
    {
        SK_MySQL::query( 'TRUNCATE TABLE `' . TBL_SECURITY_EMAIL_LIST . '`' );
    }

    private function exctractFile( $fileName = self::ZIP_IP_LIST )
    {
        $zip = new ZipArchive();
        $zip->open( DIR_TMP_USERFILES . $fileName );
        $zip->extractTo( DIR_TMP_USERFILES );
        $zip->close();
    }

    private function import( $base = self::TXT_IP_LIST )
    {
        switch ( $base )
        {
            case self::TXT_IP_LIST:
                $fileName = self::TXT_IP_LIST;
                $filter = FILTER_VALIDATE_IP;
                break;
            case self::TXT_EMAIL_LIST:
            default:
                $fileName = self::TXT_EMAIL_LIST;
                $filter = FILTER_VALIDATE_EMAIL;
                break;
        }
        
        $fileHandler = fopen( DIR_TMP_USERFILES . $fileName, 'r' );

        $data = array();

        while ( $buffer = fgets($fileHandler) )
        {
            $buffer = trim( $buffer, "\n\r" );

            if ( !filter_var($buffer, $filter) )
            {
                continue;
            }

            array_push( $data, '("' . $buffer .'")' );

            if ( count($data) % 500 == 0 )
            {
                $this->insertIntoBlackList( $base, $data );
                $data = array();
            }
        }

        $this->insertIntoBlackList( $base, $data );
    }

    private function insertIntoBlackList( $base, $data )
    {
        if ( empty($data) )
        {
            return;
        }

        switch ( $base )
        {
            case self::TXT_IP_LIST:
                SK_MySQL::query( 'INSERT IGNORE INTO `' . TBL_SECURITY_IP_LIST . '` (`ip`) VALUES' . implode(',', $data) );
                break;
            case self::TXT_EMAIL_LIST:
            default:
                SK_MySQL::query( 'INSERT IGNORE INTO `' . TBL_SECURITY_EMAIL_LIST . '` (`email`) VALUES' . implode(',', $data) );
                break;
        }
    }
}
