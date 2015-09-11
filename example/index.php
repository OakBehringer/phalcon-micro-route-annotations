<?php

date_default_timezone_set('America/Chicago');
// set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ ));

// Typical for development:
error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

use Phalcon\Mvc\Micro;


// Create a basic Micro App
$app = new Micro();

// Create the micro route annotation object, passing the micro app into the constructor
require_once '../MicroRouteAnnotations.php';
$microAnno = new PhalconExt\MicroRouteAnnotations($app);
// Add a directory of "controllers" that are not namespaced
$microAnno->addControllerDirectory('non-namespaced-controllers');
// Add a directory of "controllers" that are namespaced by including the namespace
$microAnno->addControllerNamespace('SomeNamespace', 'namespaced-controllers');
// This will add the routes to the application
$microAnno->mount();



$app->notFound(function() use ($app) {
	$app->response->setStatusCode(404);
	$app->response->setContent('Whoops! Not found. Maybe you should start at /example/index to hit the index controller?');
	return $app->response;
});

// Just like always, handle your micro app
$app->handle();