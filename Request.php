<?php
/**
 * @author Ivan Tikhonov <tihh@yandex.ru>
 */

namespace Engine;

class Request {

    public function __construct() {
		$this->_schema = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    	$this->_uri = $_SERVER['REQUEST_URI'];
    	$this->_url = $this->_schema . $_SERVER["SERVER_NAME"] . $this->_uri;
		$this->_baseUrl = $this->_schema . $_SERVER["SERVER_NAME"];
        $this->_data = array_merge($_GET, $_POST);
    }

    /**
     * @var array
     */
    private $_data;

	/**
	 * @var string
	 */
    private $_uri;
	/**
	 * @var string
	 */
    private $_schema;

	/**
	 * @var string
	 */
    private $_url;

	/**
	 * @var string
	 */
    private $_baseUrl;

	/**
	 * @param string $key
	 * @param $defaultValue
	 * @return string
	 */
    public function get($key = null, $defaultValue = null) {
    	if ($key === null) {
    		return $this->_data;
		}
        if (!array_key_exists($key, $this->_data)) {
            return $defaultValue;
        }
        return $this->_data[$key];
    }

	/**
	 *
	 */
    public function getBody() {
    	return file_get_contents('php://input');
	}

	/**
	 * @return string
	 */
	public function getUri() {
		return $this->_uri;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->_url;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_baseUrl;
	}
}