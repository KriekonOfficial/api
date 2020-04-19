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
		foreach($params as $column => $value)
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

		$set = '';
		$set_count = 0;
		$count_set_params = count($set_params);

		$prepared = [];
		foreach($set_params as $column => $value)
		{
			$set .= $column . ' = ?';
			if (++$set_count < $count_set_params)
			{
				$set .= ', ';
			}
			$prepared[] = $value;
		}

		$where = '';
		$where_count = 0;
		$count_where_params = count($where_params);
		foreach ($where_params as $column => $value)
		{
			$where .= $column . ' = ?';
			if (++$where_count < $count_where_params)
			{
				$where .= ' AND ';
			}
			$prepared[] = $value;
		}

		$sql = 'UPDATE ' . $table . ' SET ' . $set . ' WHERE ' . $where;
		$out_prepared = $prepared;
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

		$where = '';
		$where_count = 0;
		$count_where_params = count($where_params);
		foreach ($where_params as $column => $value)
		{
			$where .= $column . ' = ?';
			if (++$where_count < $count_where_params)
			{
				$where .= ' AND ';
			}
			$out_prepared[] = $value;
		}

		return 'DELETE FROM ' . $table . ' WHERE ' . $where;
	}
}
