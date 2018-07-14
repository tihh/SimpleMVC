<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 17.01.18
 * Time: 22:45
 */

namespace Engine;


class TableParser {
	/**
	 * @var \PDO[]
	 */
	private static $_connections = [];

	/**
	 * @param string $dbName
	 * @return \PDO
	 */
	private static function _getDb($dbName) {
		if (!array_key_exists($dbName, self::$_connections)) {
			$config = $GLOBALS['config']['db'][$dbName];
			self::$_connections[$dbName] = new \PDO("{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$dbName};charset={$config['charset']}", $config['user'], $config['password']);
		}
		return self::$_connections[$dbName];

	}
	public static function analyzeTable($tableName) {
		$db = self::_getDb('allbmw');
		$columns = $db->query('SHOW COLUMNS FROM models', \PDO::FETCH_ASSOC);
		foreach ($columns as $column) {
			var_dump($column);
		}
	}
}