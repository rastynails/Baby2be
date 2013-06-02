<?php

class DaoPay 
{

	public static function handlePayment($get, $service_key, $app_code)
	{
		$appcode = trim($get['appcode']);
		$prodcode = trim($get["prodcode"]);
		$pin = trim($get["pin"]);


		if(!strlen($appcode) || !strlen($prodcode) || !strlen($pin)) 
			return -1;

		if ($app_code == $appcode) {
			$handle = fopen("http://daopay.com/svc/pincheck?appcode=".$appcode."&prodcode=".$prodcode."&pin=".$pin, "r");
			if ($handle) {
				$reply = fgets($handle);
				if (substr($reply, 0, 2) == "ok")
					 return 1;
				else 
					return -2;
			}
			else 
				return -3;
		}
		return -1;
	}
}