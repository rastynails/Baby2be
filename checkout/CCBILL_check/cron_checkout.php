<?php
$pp_FORMAT = array(
	'type','account_number','subaccount_number','subscription_id','timestamp','first_name','last_name','username','password',
	'address','city','state','country','postal_code','email','referrer','active','initial_amount','initial_period','recurring_amount',
	'recurring_period','rebills'
	);

if ( $__CRON_CONFIRMATION != 'yes' )
	return;

$merchant_fields = app_Finance::getPaymentProviderFields(5);

//get data from CCBILL DataLink
$_req = "https://datalink.ccbill.com/data/main.cgi?startTime=".get_CCBILLcheck_date_format(time()-86400)."&endTime=".get_CCBILLcheck_date_format(time())."&transactionTypes=NEW,REBILL,EXPIRE&clientAccnum={$merchant_fields['account_number']}&clientSubacc={$merchant_fields['subaccount']}&username={$merchant_fields['datalink_username']}&password={$merchant_fields['datalink_password']}";

$logString = "Request(Time: ".date( "Y-m-d H:i:s", time())." LINE: ".__LINE__."): $_req\n";

$ch = curl_init( $_req );
ob_start();
curl_exec( $ch );
$_string = ob_get_contents();
ob_end_clean();

$logString.= "Response(LINE: ".__LINE__."): $_string\n";

//check for error
if ( curl_errno($ch) )
{
	$logString.= "Curl error(LINE: ".__LINE__."): Error number=$ch; Description=".curl_error($ch)."\n";
	CCBILLcheckSaveInLogFile($logString, 'errors.log');
	return;
}
else
	$_rebill_arr = parseCCBILLcheckDatalinkResponse( $pp_FORMAT, $_string );

curl_close( $ch );

if ( $_rebill_arr )
	$logString.= "Rebill array(LINE: ".__LINE__."): ".print_r($_rebill_arr, true);

CCBILLcheckSaveInLogFile( $logString, 'transactions.log' );

// execute received data
foreach ( $_rebill_arr as $_rebill )
{
	app_Finance::LogPayment( $_rebill, __FILE__, __LINE__ );
	
	$client_accnum = $_rebill['account_number'];
	$client_subaccnum = $_rebill['subaccount_number'];
	
	switch ( strtoupper($_rebill['type']) )
	{
		case 'NEW':
			$amount = ( $_rebill['recurring_amount'] )? $_rebill['recurring_amount'] : $_rebill['initial_amount'];
			$custom = $_rebill['username'].$_rebill['password'];
			$trans_order = $_rebill['subscription_id'];
	
			// check merchant info
			if ( $merchant_fields['account_number'] != $client_accnum || $merchant_fields['subaccount'] != $client_subaccnum )
			{
				app_Finance::LogPayment( 'Finish. TIME: '.date("Y-m-d H:i:s", time()).";", __FILE__, __LINE__, $custom );
				continue;
			}
			
			if ( app_Finance::SetTransactionResult( $custom, 'approval', '', $trans_order ) )
			{
				app_Finance::LogPayment( 'Registered.', __FILE__, __LINE__ );
				$__CUSTOM = $custom;
				include( DIR_CHECKOUT . 'post_checkout.php' );
			}		
			break;
			
		case 'REBILL':
			$amount = $_rebill['last_name'];
			
			// get custom
			$query = SK_MySQL::placeholder("SELECT `hash` FROM `".TBL_FIN_SALE_INFO."` 
				WHERE `payment_provider_order_number` = '?'", $_rebill['subscription_id']);		
			$custom = SK_MySQL::query($query)->fetch_cell();
	
			$trans_order = $_rebill['first_name'];
			
			$res = app_Finance::SetTransactionResult( $custom, 'approval', '', $_rebill['subscription_id'], '', $trans_order );
			CCBILLcheckSaveInLogFile( "Custom = ".$custom." | Subscription ID = ".$_rebill['subscription_id']." | Transaction ID = ".$trans_order." | Transaction result: ".$res, 'transactions.log' );
			if ( $res )
			{
				app_Finance::LogPayment( 'Registered.', __FILE__, __LINE__ );
				$__CUSTOM = $custom;
				include( DIR_CHECKOUT . 'post_checkout.php' );
			}
			break;
			
        case 'EXPIRE':
            $subscrId = $_rebill['subscription_id'];
            
            $query = SK_MySQL::placeholder("SELECT `m`.`membership_id`, `s`.* 
                FROM `".TBL_FIN_SALE."` AS `s` 
                LEFT JOIN `".TBL_MEMBERSHIP."` AS `m` ON (`m`.`fin_sale_id`=`s`.`fin_sale_id`)
                WHERE `s`.`payment_provider_order_number` = '?'", $subscrId);     
            
            $membership = SK_MySQL::query($query)->fetch_assoc();
            
            if ( $membership['membership_id'] && $membership['profile_id'] )
            {
                app_Membership::expireProfileMembership($membership['profile_id'], $membership['membership_id']);
            }
            break;
	}
	
	app_Finance::LogPayment( 'Finish. TIME: '.date("Y-m-d H:i:s", time()).";", __FILE__, __LINE__, $custom );
}



function parseCCBILLcheckDatalinkResponse( $format, $string )
{
	$_return = array();
	
	$_array = explode( "\n", $string );
	$_r = 0;
	foreach ( $_array as $_string )
	{
		$_j = 0;
		for( $_i = 0; $_i < strlen( $_string ); $_i++ )
		{
			if ( $_string[$_i-2] == '"' && $_string[$_i-1] == ',' && $_string[$_i] == '"' )
				$_j++;

			if ( ($_string[$_i] == ',' && $_string[$_i+1] == '"') || ($_string[$_i-1] != '\\' && $_string[$_i] == '"') )
				continue;
			
			$_return[$_r][$format[$_j]].= $_string[$_i];
		}
		$_r++;
	}
	
	return $_return;
}

function get_CCBILLcheck_date_format( $time )
{
	return date( "Y", $time ).date( "m", $time ).date( "d", $time ).'010101';
}

function CCBILLcheckSaveInLogFile( $string, $filename )
{
	if ( file_exists(DIR_SITE_ROOT.'checkout/CCBILL_check/'.$filename) )
		$mode = (filesize(DIR_SITE_ROOT.'checkout/CCBILL_check/'.$filename)>500000)? "w" : "a";
	else
		$mode = "w";
	
	if ( !$fr = @fopen(DIR_SITE_ROOT.'checkout/CCBILL_check/'.$filename, $mode) )
		return false;
	
	@fwrite($fr, $string);
	
	fclose($fr);
	
	return true;
}