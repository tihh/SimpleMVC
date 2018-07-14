<?php
/**
 * @author Ivan Tikhonov <tihh@yandex.ru>
 */

namespace Engine\Templates;

use Engine\Application;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;

class TwigEngine extends AbstractEngine{
    private $_twig;
    public function __construct() {
        $loader = new Twig_Loader_Filesystem($GLOBALS['config']['templates']['path']);
        $this->_twig = new Twig_Environment($loader, $GLOBALS['config']['twig']['options']);
        if ($GLOBALS['config']['twig']['options']['debug']) {
            $this->_twig->addExtension(new Twig_Extension_Debug());
        }
        //TODO: ?
		$this->_twig->addGlobal('app', Application::$instance);
    }

	/**
	 * @param string $name
	 * @param array $variables
	 * @return string
	 */
    public function render($name, array $variables = []) {
        return $this->_twig->render($name, $variables);
    }
}