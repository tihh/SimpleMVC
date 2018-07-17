<?php
/**
 * @author Ivan Tikhonov <tihh@yandex.ru>
 */

namespace Engine;

use Engine\Templates\TwigEngine;
use Repository;

class Controller {

    protected $_defaultAction = 'index';
    private $_templateEngine;
    private $_request;
    public function __construct() {
        $this->_templateEngine = new TwigEngine();
        $this->_request = new Request();
    }

	/**
	 * @param string $templateName
	 * @param array $variables
	 * @return string
	 */
    public function render($templateName, $variables = []) {
        return $this->_templateEngine->render($templateName, $variables);
    }

	/**
	 * @param string $url
	 * @param int $status
	 */
    public function redirect($url, $status = 302) {
    	header("Location: $url");
    	exit;
	}
    protected function _notFound() {
		http_response_code(404);
		echo  '404';
		exit;
    }
}
