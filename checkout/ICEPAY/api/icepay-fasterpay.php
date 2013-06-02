<?php
/**
* ICEPAY Library for PHP
* (c) 2009 ICEPAY
*
* This file contains the ICEPAY Library for PHP.
*
* @author ICEPAY.eu (support@icepay.eu)
* @version 1.0.5
*/
class ICEPAY_Fasterpay extends ICEPAY
{
	public function Pay( $amount, $description )
	{
		$this->issuer			= '';
		$this->assignCountry	( "GB" );
		$this->assignLanguage	( "EN" );
		$this->assignCurrency	( "GBP" );
		$this->assignAmount		( $amount );
		$this->description		= $description;
		$this->paymentMethod	= "FASTERPAY";

		return $this->postRequest( $this->basicMode(), $this->prepareParameters() );
	}
}

?>