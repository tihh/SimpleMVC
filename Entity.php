<?php
/**
 * @author Ivan Tikhonov <tihh@yandex.ru>
 */
namespace Engine;


abstract class Entity {
	/**
	 * @return string
	 */
	public function schema() : string {
		return $GLOBALS['config']['default_schema'];
	}

	/**
	 * @return string
	 */
	public function table() : string {
		$class = explode('\\', get_class($this));
		return strtolower(implode("_", preg_split('/(?=[A-Z])/', lcfirst(array_pop($class)))));
	}
	/**
	 * @var int
	 */
	public $id;
}