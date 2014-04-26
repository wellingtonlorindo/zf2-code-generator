<?php

return array(
	'controllers' => array(
		'invokables' => array(
			'Zf2CodeGenerator\Controller\Index' => 'Zf2CodeGenerator\Controller\IndexController',
			),
		),

	'doctrine' => array(
		'driver' => array(
			__NAMESPACE__ . '_driver' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
				),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
					)
				)
			)
		),
	'router' => array(
		'routes' => array(
			'zf2-code-generator' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/generator[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
						),
					'defaults' => array(
						'controller' => 'Zf2CodeGenerator\Controller\Index',
						'action'     => 'index',
						),
					),
				),
			),
		),

	'view_manager' => array(
		'template_path_stack' => array(
			'zf2-code-generator' => __DIR__ . '/../view',
			),
		)
	);