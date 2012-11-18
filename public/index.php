<?php

/**
 * Very simple MVC structure
 */
$config = new Phalcon\Config\Adapter\Ini( '../apps/config/config.ini' );

$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
	$config->application->controllersDir,
	$config->application->modelsDir,
	$config->application->myDir,
));
$loader->register();

$di = new \Phalcon\DI();
$di->set('url', function() use ($config){
	$url = new \Phalcon\Mvc\Url();
	return $url;
});

$di->set('db', function() use ($config) {

    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name
    ));
		
    //Assign the eventsManager to the db adapter instance
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
	$dispatcher = new Phalcon\Mvc\Dispatcher();
	return $dispatcher;
});


//Registering a Http\Response 
$di->set('response', 'Phalcon\Http\Response');

//Registering a Http\Request
$di->set('request', 'Phalcon\Http\Request');


$di->set('filter', function(){
    return new \Phalcon\Filter();
});

$di->set("cache", function() use ($config) {
	
    $frontCache = new Phalcon\Cache\Frontend\Data(array(
    	"lifetime" => 2
	));

	$cache = new Phalcon\Cache\Backend\File($frontCache, array(
		"cacheDir" => $config->application->cacheDir
	));
	return $cache;
	
});

$di->set('voltService', function($view, $di) use ($config) {

    $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

    $volt->setOptions(array(
        "compiledPath" => $config->application->templCompDir,
        "compiledExtension" => ".compiled"
    ));

    return $volt;
});


$di->set('cache', function(){

    //Cache data for one day by default
    $frontCache = new Phalcon\Cache\Frontend\Data(array(
        "lifetime" => 60
    ));

    //Memcached connection settings
   // $cache = new Phalcon\Cache\Backend\File($frontCache, array(
    //   "cacheDir" => "../apps/cache/"
    //));
	$cache = new Phalcon\Cache\Backend\Apc($frontCache);

    return $cache;
});

//Registering the view component
$di->set('view', function() use ($config) {
	
	$eventsManager = new \Phalcon\Events\Manager();
	$viewManager = new ViewManager();
	$eventsManager->attach('view', $viewManager);

    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir( $config->application->viewsDir );
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
