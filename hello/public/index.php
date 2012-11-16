<?php

/**
 * Very simple MVC structure
 */

$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
	'../apps/controllers/',
	'../apps/models/'
));
$loader->register();

$di = new \Phalcon\DI();

//Registering a router
$di->set('router', 'Phalcon\Mvc\Router');	

//Registering a dispatcher
$di->set('dispatcher', 'Phalcon\Mvc\Dispatcher');		

//Registering a Http\Response 
$di->set('response', 'Phalcon\Http\Response');

//Registering a Http\Request
$di->set('request', 'Phalcon\Http\Request');

//Registering the view component
$di->set('view', function() {

    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir('../app/views/');

	
    return $view;
});


try {
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	echo $application->handle()->getContent();
}
catch(Phalcon\Exception $e){
	echo $e->getMessage();
}
