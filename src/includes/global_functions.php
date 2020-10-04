<?php
// Global functions

function camelCase(string $string, string $delimiter = '_', bool $capitalizeFirstCharacter = true) : string
{
	$str = str_replace($delimiter, '', ucwords($string, $delimiter));

	if (!$capitalizeFirstCharacter)
	{
		$str = lcfirst($str);
	}

	return $str;
}

function humanReadable(string $string, string $delimiter = '_') : string
{
	return ucwords(str_replace($delimiter, ' ', $string));
}

function htmlEncode(string $string) : string
{
	return htmlspecialchars($string, ENT_QUOTES);
}
