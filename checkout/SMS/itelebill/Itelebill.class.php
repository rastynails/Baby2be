<?php

class Itelebill 
{
	public static function getIframeSrc ( $siteId, $customerId, $hashPass, $price, $custom, $service, $country = 'gb' )
	{
	    $rebillPrice = 0;
	    $rebillFreqI = 0;
	    $rebillFreq = 0;

	    $hashParams = "cur=gbp&price={$price}&rprice={$rebillPrice}&rfi={$rebillFreqI}&rf={$rebillFreq}&password={$hashPass}&country={$country}";
	    $hash = hash("sha256", $hashParams);

	    $iframeSrc = "http://payment.itelebill.com/payment?site={$siteId}&customer={$customerId}&pay_by_sms=1&html=iframe&smsp[cur]=gbp&smsp[price]={$price}&smsp[rprice]=0&smsp[rfi]=0&smsp[rf]=0&smsp[hash]={$hash}&config[custom]={$custom}&config[service]={$service}&smsp[country]={$country}";

	    return $iframeSrc;
	}
}
