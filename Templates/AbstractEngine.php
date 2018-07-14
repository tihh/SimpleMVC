<?php

/**
 * @author Ivan Tikhonov <tihh@yandex.ru>
 */
namespace Engine\Templates;

abstract class AbstractEngine {
    /**
     * @param $name
     * @param $variables
     * @return $string
     */
    abstract public function render($name, array $variables = []);
}