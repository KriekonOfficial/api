<?php

namespace Core\Response;

class Dump
{
	public static function var($var) : void
	{
		header('Content-Type: text/html');

		echo "<pre>";
		var_dump($var);
		echo "</pre>";
		exit();
	}
}