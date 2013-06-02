<?php
require_once( DIR_SERVICE_AUTOCROP.'lib/nusoap.php');
require_once( DIR_SERVICE_AUTOCROP.'class.rop.php');

class  ImageCropperProxy
{
	var $ErrorCodes = array('FileNotFound' => 'File not found',
							'ProxyError' => 'Proxy error',
							'InternalServiceError' => 'Internal service error');
	var $_webServiceUrl;
	var $_login;
	var $_password;
	var $_fileData;
	var $_webMethod;// can be 1 or 2
	var $_thumbWidth;
	var $_thumbHeight;
	var $_lastError = false;
	var $_lastErrorMsg = '';
	
	function ImageCropperProxy($webServiceUrl, $login, $password)
	{
		$this->_webServiceUrl = $webServiceUrl;
		$this->_login = $login;
		$this->_password = md5($password);
	}
	
	function GetLastError()
	{
		return  $this->_lastError;
	}

	function GetLastErrorMessage()
	{
		return  $this->_lastErrorMsg;
	}
	
	function GetWebMethod()
	{
		return  $this->_webMethod;
	}
	
	/**
	 * Create thumbnails from image
	 * on error return false; you can get error code by calling method GetLastError()
	 * on succes return array of thumbnails' images
	 *
	 * @param string $filename
	 *
	 * @return boolean|array of image urls
	 *
	 */

	function CropImage($filename, $thumbWidth, $thumbHeight )
	{
		$this->_thumbWidth = $thumbWidth;
		$this->_thumbHeight = $thumbHeight;
		
		$gdVersion = ROP::GetGdVersion();
		if($gdVersion == 2 || $gdVersion == 1)
			$this->_webMethod = 1; // web service return coords
		else 
			$this->_webMethod = 2; // web service return urls
		//$this->_webMethod = 2;
		if(!file_exists($filename))
		{
			$this->_lastError = $this->ErrorCodes['FileNotFound'];
			return false;
		}
		
		$this->_fileData = base64_encode(file_get_contents($filename));
		
		$client = new nusoap_client( $this->_webServiceUrl.'?WSDL', 'wsdl');
		$client->soap_defencoding = 'UTF-8';
		$client->setDefaultRpcParams(true);
		$soap_proxy = $client->getProxy();

		$params['login'] = $this->_login;
		$params['password'] = $this->_password;
		$params['fileData'] = $this->_fileData;
		$params['webMethod'] = $this->_webMethod;
		$params['thumbWidth'] = $this->_thumbWidth;
		$params['thumbHeight'] = $this->_thumbHeight;

		$result = $soap_proxy->CropImage($params);

		$err = $soap_proxy->getError();
		if ($err)
		{
			$this->_lastError = $this->ErrorCodes['ProxyError'];
			$this->_lastErrorMsg = $err.' '.$result['CropImageResult'];
		    return false;
		}
		
        $data = explode('#', $result['CropImageResult']);//strRes.Split('#');
        if ( count($data) != 2 || $data[0] == "FAILED")
        {
            $this->_lastError = $this->ErrorCodes['InternalServiceError'];
            $this->_lastErrorMsg = $result['CropImageResult'];
            return false;
        }
		
        $rests = explode(';', $data[1]);
        
        $q = 1;
        $thumbs = array();
        if ( $rests[0] )
        foreach ($rests as $strRect)
        {
        	switch ($this->_webMethod)
        	{
        		case 1:
		        	$rect = explode(':', $strRect);
		        	
		        	$thumbs[$q] = ROP::MakeThumbnail($filename, $rect[0], $rect[1], $rect[2], $rect[3], $thumbWidth, $thumbHeight);
        			break;
    			case 2:
    				$thumbs[$q] = file_get_contents($strRect);
    				break;
        	}
        	$q++;
        }
        
		$this->_lastError = false;
		$this->_lastErrorMsg = "";
		return  $thumbs;
	}	
	
	function GetCropStat( $start_stamp, $end_stamp )
	{
		$client = new nusoap_client( $this->_webServiceUrl.'?WSDL', 'wsdl');
		$client->soap_defencoding = 'UTF-8';
		$client->setDefaultRpcParams( true );
		$soap_proxy = $client->getProxy();

		$params['login'] = $this->_login;
		$params['password'] = $this->_password;
		$params['fromTimestamp'] = $start_stamp;
		$params['toTimestamp'] = $end_stamp;
		
		$result = $soap_proxy->GetCropStat($params);
		
		$_tmp_result = explode( '#', $result['GetCropStatResult'] );
		
		return ( $_tmp_result[0] == 'OK' ) ? $_tmp_result[1] : false;
	}
	
	function IsValidUser()
	{
		$client = new nusoap_client( $this->_webServiceUrl.'?WSDL', 'wsdl');
		$client->soap_defencoding = 'UTF-8';
		$client->setDefaultRpcParams( true );
		$soap_proxy = $client->getProxy();
		
		$params['login'] = $this->_login;
		$params['password'] = $this->_password;
		
		$result = $result = $soap_proxy->GetCropStat($params);
		
		$_tmp_result = explode( '#', $result['GetCropStatResult'] );
		
		return ( $_tmp_result[0] == 'OK' ) ? true : false;
	}
}
?>