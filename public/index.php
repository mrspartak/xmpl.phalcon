<?php

//load ini config into variable
$config = new Phalcon\Config\Adapter\Ini( '../apps/config/config.ini' );

//set Loader and basic dirs, where Classes are.
$loader = new \Phalcon\Loader();
$loader->registerDirs(array(
	$config->application->controllersDir,
	$config->application->modelsDir,
	$config->application->myDir,
));
$loader->register();

//settind dependency injector
$di = new \Phalcon\DI();

//url component
$di->set('url', function() use ($config){
	$url = new \Phalcon\Mvc\Url();
	return $url;
});

//DB component
$di->set('db', function() use ($config) {
    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name
    ));
		
    return $connection;
});

//Register models Manager
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

//register filter
$di->set('filter', function(){
    return new \Phalcon\Filter();
});

//register service to run templates
$di->set('voltService', function($view, $di) use ($config) {
    $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);
    $volt->setOptions(array(
        "compiledPath" => $config->application->templCompDir,
        "compiledExtension" => ".compiled"
    ));
    return $volt;
});

//set cache engine
$di->set('cache', function(){
    //Cache data for 1 minute
    $frontCache = new Phalcon\Cache\Frontend\Data(array(
        "lifetime" => 60
    ));

    //Memcached connection settings
    // $cache = new Phalcon\Cache\Backend\File($frontCache, array(
    //   "cacheDir" => "../apps/cache/"
    //));

    // we would use APC cache
    $cache = new Phalcon\Cache\Backend\Apc($frontCache);
    return $cache;
});

//Registering the view component
$di->set('view', function() use ($config) {
    //register event manager for view
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

//init applicartion
try {
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	echo $application->handle()->getContent();
}
catch(Phalcon\Exception $e){
	echo $e->getMessage();
}
