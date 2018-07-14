<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.04.18
 * Time: 0:30
 */

namespace Engine;


class Repository {
	/**
	 * @var \PDO[]
	 */
	private static $_connections = [];

	/**
	 * @param string $dbName
	 * @return \PDO
	 */
	protected static function _getConnection(string $dbName): \PDO {
		if (!array_key_exists($dbName, self::$_connections)) {
			$config = $GLOBALS['config']['db'][$dbName];
			self::$_connections[$dbName] = new \PDO(
				"mysql:dbname={$dbName};host={$config['host']};port={$config['port']};charset={$config['charset']}",
				$config['user'],
				$config['password'],
				[
					\PDO::ATTR_PERSISTENT =>  array_key_exists('persistent', $config) ? $config['persistent'] : false
				]
			);
		}
		return self::$_connections[$dbName];
	}

	protected function storeEntity(Entity $entity) {
		$table = $entity->table();
		$schema = $entity->schema();
		$db = self::_getConnection($schema);
		$properties = get_object_vars($entity);
		$propertyNames = array_keys($properties);
		$propertiesString = '(`' . join("`, `", $propertyNames) . '`)';
		$valuesString = '(:' . join(", :", $propertyNames) . ')';
		$sql = "REPLACE INTO {$table}{$propertiesString} VALUES {$valuesString};";
		$stmt = $db->prepare($sql);
		foreach ($properties as $propertyName => $propertyValue) {
			$stmt->bindValue(':' . $propertyName , $propertyValue);
		}
	    $stmt->execute();
		if ($stmt->errorCode() != 0) {
			$error = $stmt->errorInfo();
			throw new \Exception(print_r($error, true) . "\n" . $sql);
		}
		$entity->id = $db->lastInsertId();
	}

	/**
	 * @param string $class
	 * @param int $id
	 * @param array $constructorParams
	 * @return Entity
	 * @throws \Exception
	 */
	protected function loadEntityById(string $class, int $id, array $constructorParams = []): ?Entity  {
		if (!is_int($id)) {
			throw new \Exception('id not int');
		}
		/**
		 * @var Entity $object
		 */
		$object = new $class(...$constructorParams);
		$table = $object->table();
		$schema = $object->schema();
		$db = self::_getConnection($schema);
		$sql = "SELECT * FROM  {$table} WHERE `id` = $id;";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		if ($stmt->errorCode() != 0) {
			$error = $stmt->errorInfo();
			throw new \Exception(print_r($error, true) . "\n" . $sql);
		}
		$result = $stmt->fetchObject(get_class($object), $constructorParams);
		if ($result === false) {
			return null;
		}
		return $result;
	}

	/**
	 * @param string $class
	 * @param array $filter
	 * @param array $constructorParams
	 * @return Entity[]
	 * @throws \Exception
	 */
	protected function loadEntitiesByFilter(string $class, array $filter = [], array $constructorParams = []): array  {
		/**
		 * @var Entity $object
		 */
		$object = new $class(...$constructorParams);
		$table = $object->table();
		$schema = $object->schema();
		$db = self::_getConnection($schema);
		$filter = $this->_constructFilter($filter);
		if ($filter) {
			$filter = 'WHERE 1=1' . $filter;
		}
		$sql = "SELECT * FROM  {$table} {$filter}";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		if ($stmt->errorCode() != 0) {
			$error = $stmt->errorInfo();
			throw new \Exception(print_r($error, true) . "\n" . $sql);
		}
		$stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($object), $constructorParams);
		return $stmt->fetchAll();
	}

	protected function deleteEntity(Entity $entity) {
		$table = $entity->table();
		$schema = $entity->schema();
		$db = self::_getConnection($schema);
		$sql = "DELETE FROM {$table} WHERE id={$entity->id}";
		$stmt = $db->prepare($sql);
		if ($stmt->errorCode() != 0) {
			$error = $stmt->errorInfo();
			throw new \Exception(print_r($error, true) . "\n" . $sql);
		}
		$stmt->execute();
	}
	protected function _constructFilter($filter) {
		$result = '';
		foreach ($filter as $filterPart) {
			$filterPart[2] = is_string($filterPart[2]) ? "'" . $filterPart[2]. "'" : $filterPart[2];
			if ($filterPart[0] === 'in') {
				$result .= ' AND `' . $filterPart[1] . '` IN(' . join(',', $filterPart[2]) . ')';
			}
			if ($filterPart[0] === '=') {
				$result .= ' AND `' . $filterPart[1] . '`=' . $filterPart[2];
			}
		}
		return $result;
	}
}