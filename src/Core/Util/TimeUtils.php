<?php

namespace Core\Util;

use \DateTime;
use \DateInterval;

class TimeUtils
{
	/**
	* @see https://www.php.net/manual/en/class.datetime.php
	*/
	public static function getAge(string $start_date, string $end_date) : DateInterval
	{
		$start = new DateTime($start_date);
		$end = new DateTime($end_date);

		$difference = $start->diff($end);

		return $difference;
	}

	/**
	* @see https://www.php.net/manual/en/dateinterval.format.php
	* @return string
	*/
	public static function formatInterval(DateInterval $interval, string $format) : string
	{
		return $interval->format($format);
	}
}