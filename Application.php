<?php
/**
 * @author Ivan Tikhonov <tihh@yandex.ru>
 */
namespace Engine;

use SimpleRouter;
use Tracy\Debugger;

class Application {
    /**
     * @var SimpleRouter\Router
     */
    private $_router;
    public static $instance;
    public $controller;

	/**
	 * @param string $name
	 * @param array $params
	 * @return string
	 */
    public function getRoute($name, array $params = []) {
    	return $this->_router->generate($name, $params);
	}
    public function __construct() {
    	if ($GLOBALS['config']['env']['debug'] === true) {
			Debugger::$strictMode = true;
			Debugger::enable(Debugger::DEVELOPMENT);
		}
		self::$instance = $this;
    }

	public function run() {
        $this->_router = new SimpleRouter\Router();
        $this->_mapRoutes();
        $route = $this->_router->route();
        if ($route !== null) {
            return $this->_dispatch($route);
        }
        $this->_notFound($this->_router->getMethod(), $this->_router->getUrl());
    }

    /**
     * @param SimpleRouter\Route $route
     * @return mixed
     */
    private function _dispatch(SimpleRouter\Route $route) {
        $route->callable[1] = 'action' . ucfirst($route->callable[1]);
        $this->controller = $route->callable[0];
        return call_user_func_array($route->callable, $route->params);
    }

    /**
     * @param string $method
     * @param string $url
     */
    private function _notFound($method, $url) {
		http_response_code(404);
		header("HTTP/1.1 404 Not Found");
		header("Status: 404", true, 404);
		echo  '404';
        exit;
    }

    private function _mapRoutes() {
        require_once($GLOBALS['config']['router']['routes_file_path']);
    }
}