<?php

class Utils 
{
	/**
	 * Generates cryptographically secure pseudo-random token of a given legnth
	 * 
	 * @param  int $token_length Desired length of final token
	 * @return string            Token made from random characters from the char pool
	 */
	public static function generateSecureToken($token_length) {
		$char_pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$pool_length = strlen($char_pool);
		$token = '';

		for ($counter = 0; $counter < $token_length; $counter +=1) {
			$token .= $char_pool[random_int(0, $pool_length)];
		}

		return $token;
	}

	public static function CSPRNG($bytes=32){
		// use openssl to generate the randomness, but check if it stands by it
		// for use in cryptogrphic applications by checking the $strong bool
		$rand = openssl_random_pseudo_bytes($bytes, $strong);
		if ($strong) {
		    return $rand;
		}

		// try to fall back to random_bytes which is avaible from PHP 7.0
		try {
			return random_bytes($bytes);
		} catch(\Exception $e) {
			return false;
		}
	}

	// allows quick change of db stanard input date format incase DB engine changes
	public static function database_datetime($unix_timestamp = NULL)
	{
		if (is_null($unix_timestamp)) {
			$unix_timestamp = time();
		}

		return date('Y-m-d H:i:s', $unix_timestamp);
	}

	/**
	* Redirects the user to a new page, with optional delay timer.
	*
	* @param string $URL - the URL the user should be redirected to
	* @param int $Delay - the number of seconds to delay the redirect 
	*
	* @return Void
	*/
	public static function redirect($URL, $Delay=0) 
	{
		if(headers_sent())
		{
    			if($Delay == 0)
			{
				die(print("<script type=\"text/javascript\">window.location.href='{$URL}';</script>"));
			}else{
				die(print("<script type=\"text/javascript\">setTimeout( \"window.location.href = '{$URL}'\", {$Delay}*1000);</script>"));
			}
		}else{
    			if($Delay == 0)
			{
				die(header('Location: '.$URL));
			}else{
				die(header('Refresh: '.(int)$Delay.'; url='.$URL));
			}
    		}
	}

	/**
	* Get the remote address of a user
	*
	* @param NULL
	*
	* @return string - the remote address of the user (IP)
	*/
	public static function get_ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}

	/*
	 * days_in_month($month, $year)
	 * Returns the number of days in a given month and year, taking into account leap years.
	 *
	 * $month: numeric month (integers 1-12)
	 * $year: numeric year (any integer)
	 *
	 * Prec: $month is an integer between 1 and 12, inclusive, and $year is an integer.
	 * Post: none
	 */
	// corrected by ben at sparkyb dot net
	public static function days_in_month($month, $year)
	{
		// calculate number of days in a month
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}

	public static function round_up_to_any($n,$x=1)
	{
		return (ceil($n)%$x === 0) ? ceil($n) : round(($n+$x/2)/$x)*$x;
	} 

	public static function is_url($URL)
	{
		return filter_var($URL, FILTER_VALIDATE_URL);
	}

	public static function is_email($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public static function is_int($Int)
	{
		return filter_var($Int, FILTER_VALIDATE_INT);
	}

	public static function xss_secure($Input)
	{
		return htmlentities($Input, 'utf-8');
	}

	public static function range_check($Int, $Min, $Max)
	{
		return filter_var(
		    $Int, 
		    FILTER_VALIDATE_INT, 
		    array(
		        'options' => array(
		            'min_range' => $Min, 
		            'max_range' => $Max
		        )
		    )
		);
	}

	/**
	 * Takes in a raw string and a proposed format to check for validity
	 * 
	 * @param  string  $date_string Date as string to be checked for errors
	 * @param  string  $date_format Expected date format of $date_string
	 * @return boolean             	returns true if the date is valid in accordance to the supplied format
	 */
	public static function is_date(string $date_string, $date_format = 'Y-m-d')
	{
		$date = DateTime::createFromFormat($date_format, $date_string);
		$date_errors = DateTime::getLastErrors();
		if($date_errors['warning_count'] + $date_errors['error_count'] > 0)
		{
		    return false;
		}

		return true;
	}
}