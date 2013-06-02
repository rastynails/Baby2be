<?php

/**
 * Internationalization API class.
 */
class SK_I18n
{
	public static function datetime( $timestamp = null )
	{
            
	}

	public static function date( $timestamp = null )
	{

	}

	public static function time( $timestamp = null )
	{

	}

	public static function period( $stamp1, $stamp2 = null )
	{
		if ( isset($stamp2) && $stamp2 > $stamp1 ){
			$time_interval = $stamp2 - $stamp1;
		}
		elseif ( !isset($stamp2) ){
			$time_interval = $stamp1;
		}
		else
			return '';

		if ( ( $time_interval / 86400 ) >= 1 )
		{
			$count = floor( $time_interval / 86400 );
			$item = 'd';
		}
		elseif ( ( $time_interval / 3600 ) >= 1 )
		{
			$count = floor( $time_interval / 3600 );
			$item = 'h';
		}
		elseif ( ( $time_interval / 60 ) >= 1 )
		{
			$count = floor( $time_interval / 60 );
			$item = 'min';
		}
		else
		{
			$count = 1;
			$item = 'm';
		}

		$lang_key = ($count > 1) ? 'i18n.date.period_'.$item : 'i18n.date.period_one_'.$item;

		return $count.' '.SK_Language::text($lang_key);
	}

	/**
	 * Formats and returns spec date string
	 *
	 * @param integer $time_stamp
	 * @param boolean $only_date
	 * @return string
	 */
	//TODO GMT and Langs and Config Format
	public static function getSpecFormattedDate( $time_stamp, $only_date = false, $simple_date = false )
	{
		if( empty( $time_stamp ) )
			return '_INVALID_TS_';

		$section = SK_Language::section( 'i18n.date' );

        $military_time = SK_Config::section('site.official')->get('military_time');

		$param_ts = $time_stamp; //TODO GMT format
		$current_ts = time(); //TODO GMT format

                $seconds_past = $current_ts - $param_ts;

		if( !$simple_date && $seconds_past >= 0 )
		{
			if( date('j', $param_ts) === date('j', $current_ts) && date('n', $param_ts) === date('n', $current_ts) && date('y', $param_ts) === date('y', $current_ts) )
			{
				if( $only_date )
					return $section->text( 'today' );

				switch (true)
				{
					case $seconds_past < 60:
						return $section->text( 'active_ago_within_minute' );

					case $seconds_past < 120:
						return $section->text( 'active_ago_one_minute' );

					case $seconds_past < 3600:
						return floor( $seconds_past / 60 ). $section->text( 'active_ago_minutes' );

					case $seconds_past < 7200:
						return $section->text( 'active_ago_one_hour' );

					default:
						return floor( $seconds_past / 3600 ). $section->text( 'active_ago_hours' );
				}
			}
			else if( ( date('j', $current_ts) - date('j', $param_ts) ) === 1 && date('n', $param_ts) === date('n', $current_ts) && date('y', $param_ts) === date('y', $current_ts) )
			{
				if( $only_date )
					return $section->text( 'yesterday' );

				return $section->text( 'yesterday' ).' '. ( $military_time ? strftime("%H:%M", $param_ts) : strftime("%I:%M%P", $param_ts) );
			}
		}

		$month = $section->text( 'month_short_'.date( 'n', $param_ts ) );
		$dev = $section->text( 'dev_at' );

	   $out = array(
            'm' => $month,
            'd' => date( 'j', $param_ts ),
            'y' => date( 'Y', $param_ts )
        );

        $dateFormatString = SK_Config::section('site.official')->date_format;

        $dateFormatString = !empty($dateFormatString) ? $dateFormatString : 'd-m-y';

        $dateFormat = explode('-', $dateFormatString);

        $date = array();
        foreach ( $dateFormat as $i )
        {
            array_push($date, $out[$i]);
        }

		if( $only_date )
			return implode(' ', $date);



		return implode(' ', $date) . ' '.$dev.' '.
			( $military_time ?
				date( 'H', $param_ts ).':'.date( 'i', $param_ts ) :
				date( 'h', $param_ts ).':'.date( 'i', $param_ts ).date( 'a', $param_ts )

			);

//
//		if( $only_date )
//			return strftime("%b %d, `%y", $param_ts);
//
//		return strftime("%b %d, `%y at %H:%M", $param_ts);
	}


	/**
	 * Returns local time stamp
	 *
	 * @return integer
	 */
	public static function mktimeLocal()
	{
		return time();
	}

	/**
	 * Replaces string code and returns string
	 * @param string $string
	 * @return string
	 */
	public static function getHandleSmile( $string )
	{
		return app_TextService::stHandleSmiles( $string );
	}

}
