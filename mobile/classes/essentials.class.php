<?php
class Essentials
 {
     private $salt			=	"";
	 private $dbHost		=	"";
	 private $dbUser		=	"";
	 private $dbPass		=	"";
	 private $dbName		=	"";
	private $url		=	"";
	
     public function getHashSalt()
	  {
	  		$salt		= SK_PASSWORD_SALT;	
	   		return $salt;	
	  }
		
	public function geturl()
	  {     
			$url		= SITE_URL;
	   		return $url;
	  }
	 public function getDbHost()
	  {     
			$dbHost		= DB_HOST;
	   		return $dbHost;
	  }
	 public function getDbUser()
	  {     
	        $dbUser 	= DB_USER;
	   		return $dbUser;	
	  } 
     public function getDbPass()
	  {     
	        $dbPass		= DB_PASS;
	   		return $dbPass;	
	  }
     public function getDbName()
	  {		
			$dbName		= DB_NAME;
	   		return $dbName;	
	  }	
	 public function tblPrefix()
	 {
	 		$tblPrefix	= DB_TBL_PREFIX;
	   		return $tblPrefix;	
	 }
	 public function getpasslen()
	 {
	 		return PASSLEN;	
	 }
	 public function getSiteEmail()
	 {
	 		return SITEMAIL;
	 }
	 public function GetAboutUs()
	 {mysql_query('SET CHARACTER SET utf8');
	 	$essence			=	new Essentials();
		$langkey			=	$essence->tblPrefix().'lang_key';
		$langvalue			=	$essence->tblPrefix().'lang_value';
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$sql				=	"SELECT `value` from $langvalue where `lang_key_id` = (SELECT `lang_key_id` from $langkey where `key`= 'about_us' and `lang_section_id` = 8)";
		if($db1->Query($sql))
		{
			if($db1->RowCount())
			{
				$row	=	$db1->Row();
				$value=$row->value;
				$value=PREG_REPLACE('#<br\s*?/?>#i', "", $value);
                        $value=$this->converter($value);
				
				$response	=	array("About_us"=>$value);
				echo json_encode($response);
			}
			else
			{
				$response	=	array("About_us"=>"NULL");
				echo json_encode($response);
			}
		}
	 }
	  public function GetPrivacyPolicy()
	{mysql_query('SET CHARACTER SET utf8');
		$essence = new Essentials();
		$langkey = $essence->tblPrefix().'lang_key';
		$langvalue = $essence->tblPrefix().'lang_value';
		$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$sql = "SELECT `value` from $langvalue where `lang_key_id` = (SELECT `lang_key_id` from $langkey where `key`= 'privacy_policy' and `lang_section_id` = 8)";
		if($db1->Query($sql))
		{
			if($db1->RowCount())
			{
				$row = $db1->Row();
				$value=$row->value;
				$value=PREG_REPLACE('#<br\s*?/?>#i', "", $value);
                        $value=$this->converter($value);
				
				$response = array("Privacy"=>$value);
				echo json_encode($response);
			}
			else
			{
				$response = array("Privacy"=>"NULL");
				echo json_encode($response);
			}
		}
	}
	public function GetTerms()
	{mysql_query('SET CHARACTER SET utf8');
		$essence = new Essentials();
		$langkey = $essence->tblPrefix().'lang_key';
		$langvalue = $essence->tblPrefix().'lang_value';
		$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$sql = "SELECT `value` from $langvalue where `lang_key_id` = (SELECT `lang_key_id` from $langkey where `key`= 'term_of_use' and `lang_section_id` = 8) limit 1";
			if($db1->Query($sql))
			{
				if($db1->RowCount())
				{
					$row = $db1->Row();
					$value=$row->value;
				$value=PREG_REPLACE('#<br\s*?/?>#i', "", $value);
                        $value=$this->converter($value);
				
				$response = array("Terms"=>$value);
					echo json_encode($response);
				}
				else
				{
					$response = array("Terms"=>"NULL");
					echo json_encode($response);
				}
			}
	}
	public function GetFull()
	{mysql_query('SET CHARACTER SET utf8');
		$essence = new Essentials();
		$langkey = $essence->tblPrefix().'lang_key';
		$langvalue = $essence->tblPrefix().'lang_value';
		$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$sql = "SELECT `value` from $langvalue where `lang_key_id` = (SELECT `lang_key_id` from $langkey where `key`= 'faq' and `lang_section_id` = 8)";
			if($db1->Query($sql))
			{
				if($db1->RowCount())
				{
					$row = $db1->Row();
					$value=$row->value;
				$value=PREG_REPLACE('#<br\s*?/?>#i', "", $value);
                        $value=$this->converter($value);
				
				$response = array("FullVersion"=>$value);
					echo json_encode($response);
				}
				else
				{
					$response = array("FullVersion"=>"NULL");
					echo json_encode($response);
				}
			}
	}
	
 public function converter($html)
{ 
    
    $tags = array (
    0 => '~<h[123][^>]+>~si',
    1 => '~<h[456][^>]+>~si',
    2 => '~<table[^>]+>~si',
    3 => '~<tr[^>]+>~si',
    4 => '~<li[^>]+>~si',
    5 => '~<br[^>]+>~si',
    6 => '~<p[^>]+>~si',
    7 => '~<div[^>]+>~si',
    );
    $html = preg_replace($tags,"\n",$html);
    $html = preg_replace('~</t(d|h)>\s*<t(d|h)[^>]+>~si',' - ',$html);
    $html = preg_replace('~<[^>]+>~s','',$html);
    // reducing spaces
    $html = preg_replace('~ +~s',' ',$html);
    $html = preg_replace('~^\s+~m','',$html);
    $html = preg_replace('~\s+$~m','',$html);
    // reducing newlines
    $html = preg_replace('~\n+~s',"\n",$html);
    $html = preg_replace('/\r\n|\n\r|\r|\n/',"\n",$html);
    $html = str_replace("'", '\n', $html);
    return $html;
}

  }
?>
