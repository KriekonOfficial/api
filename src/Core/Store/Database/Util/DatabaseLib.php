<?php

namespace Core\Store\Database\Util;

class DatabaseLib
{
	/**
	* @param $table - The table you wanna generate the insert sql for
	* @param $params - array('column_name' => $value)
	* @return string
	*/
	public static function generateInsertSQL(string $table, array $params) : string
	{
		$columns = '';
		$values = '';
		$count_params = count($params);
		$counter = 0;
		foreach ($params as $column => $value)
		{
			$columns .= $column;
			$values .= '?';

			if (++$counter < $count_params)
			{
				$columns .= ',';
				$values .= ',';
			}
		}

		$sql = 'INSERT INTO ' . $table . ' (' . $columns . ') VALUES (' . $values . ')';

		return $sql;
	}

	/**
	* @param $table - The table you wanna generate the update sql for
	* @param $set_params - array('column_name' => $value)
	* @param $where_params - array('column_name' => $value)
	* @param $out_prepared - All the values in number index array keys array($value, $value1, $value2)
	* @return string
	*/
	public static function generateUpdateSQL(string $table, array $set_params, array $where_params, array &$out_prepared) : string
	{
		$out_prepared = [];
		$set = self::generateParameters($set_params, $out_prepared, ',');
		$where = self::generateParameters($where_params, $out_prepared);

		$sql = 'UPDATE ' . $table . ' SET ' . $set . ' WHERE ' . $where;
		return $sql;
	}

	/**
	* @param $table - The table you wanna generate delete sql for
	* @param $where_params - array('column_name' => $value)
	* @param $out_prepared - All the values in number index array keys array($value, $value1, $value2)
	* @return string
	*/
	public static function generateDeleteSQL(string $table, array $where_params, array &$out_prepared) : string
	{
		$out_prepared = [];

		return 'DELETE FROM ' . $table . ' WHERE ' . self::generateParameters($where_params, $out_prepared);
	}

	private static function generateParameters(array $params, array &$out_prepared, string $delimiter = 'AND') : string
	{
		$parameters = '';
		$count = 0;
		$count_params = count($params);
		foreach ($params as $column => $value)
		{
			$parameters .= $column . ' = ?';
			if (++$count < $count_params)
			{
				$parameters .= " {$delimiter} ";
			}
			$out_prepared[] = $value;
		}

		return $parameters;
	}
}
