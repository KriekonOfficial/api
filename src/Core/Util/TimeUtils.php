<?php

namespace Core\Util;

use \DateTime;
use \DateInterval;

class TimeUtils
{
	public const ONE_MIN = 60;
	public const ONE_HOUR = self::ONE_MIN * 60;
	public const ONE_DAY = self::ONE_HOUR * 24;
	public const ONE_WEEK = self::ONE_DAY * 7;
	public const ONE_MONTH = self::ONE_WEEK * 4.345;
	public const ONE_YEAR = self::ONE_MONTH * 12;

	public const DATEFORMAT_STANDARD = 'Y-m-d H:i:s';
	public const DATEFORMAT_COMPACT = 'Y-m-d';
	public const DATE_ZERO = '0000-00-00 00:00:00';
	public const DATE_HALF_ZERO = '0000-00-00';

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