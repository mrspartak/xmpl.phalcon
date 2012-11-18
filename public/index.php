<?php

/**
 * Very simple MVC structure
 */

$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
	'../apps/controllers/',
	'../apps/models/',
	'../apps/My/'
));
$loader->register();


$di = new \Phalcon\DI();
$di->set('url', function() use ($config){
	$url = new \Phalcon\Mvc\Url();
	return $url;
});

$di->set('db', function() {

    $eventsManager = new Phalcon\Events\Manager();
    $logger = new Phalcon\Logger\Adapter\File("../apps/logs/debug.log");
    //Listen all the database events
    $eventsManager->attach('db', function($event, $connection) use ($logger) {
        if ($event->getType() == 'beforeQuery') {
            $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
        }
    });

    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => "localhost",
        "username" => "root",
        "password" => "",
        "dbname" => "poll"
    ));

    //Assign the eventsManager to the db adapter instance
    $connection->setEventsManager($eventsManager);
    return $connection;
	
});

$di->set('modelsManager', function(){
	return new Phalcon\Mvc\Model\Manager();
});
//Registering the Models-Metadata
$di->set('modelsMetadata', function(){
    return new \Phalcon\Mvc\Model\Metadata\Memory();
});

//Registering a router
$di->set('router', 'Phalcon\Mvc\Router');	

//Registering a dispatcher
$di->set('dispatcher', function() use ($di) {
	//$eventsManager = new \Phalcon\Events\Manager();
	//$eventsManager->attach('dispatch',  new Dispatcher($di));
	$dispatcher = new Phalcon\Mvc\Dispatcher();
	//$dispatcher->setEventsManager($eventsManager);
	return $dispatcher;
});


$di->set('flash', function() {
    $flash = new Phalcon\Flash\Direct(array(
        'error' => 'alert alert-error',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
	return $flash;
});

//Registering a Http\Response 
$di->set('response', 'Phalcon\Http\Response');

//Registering a Http\Request
$di->set('request', 'Phalcon\Http\Request');

$di->set('filter', function(){
    return new \Phalcon\Filter();
});

$di->set("cache", function(){
	
    $frontCache = new Phalcon\Cache\Frontend\Data(array(
    	"lifetime" => 2
	));

	$cache = new Phalcon\Cache\Backend\File($frontCache, array(
		"cacheDir" => "../apps/cache/"
	));
	return $cache;
	
});

$di->set('voltService', function($view, $di) {

    $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

    $volt->setOptions(array(
        "compiledPath" => "../apps/compiled-templates/",
        "compiledExtension" => ".compiled"
    ));

    return $volt;
});

//Registering the view component
$di->set('view', function() {
	
	$eventsManager = new \Phalcon\Events\Manager();
	$viewManager = new ViewManager();
	$eventsManager->attach('view', $viewManager);

    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir('../apps/views/');
	$view->registerEngines(array(
		".phtml" => 'voltService'
	));
	
	$view->setEventsManager($eventsManager);
	
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
