<?php

/**
 * Code Generator Module 
 *
 * @link https://github.com/wellingtonlorindo/zf2-code-generator.git
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zf2CodeGenerator;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}