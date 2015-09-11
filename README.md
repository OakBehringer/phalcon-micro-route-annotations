# phalcon-micro-route-annotations

Phalcon Micro applications are awesome, but they can very quickly grow out of control. Adding collections helps, allowing you to encapsulate logic into classes, and then define routes based on those classes, but you then need to constantly keep your bootstrap file up to date: Any time you want to add new functionality to your micro app, you must create and mount a collection, and/or update routes for methods that collection, all in your bootstrap file. Gross. 

Wouldn't it be great if all that could be handled dynamically, without touching your bootstrap file? Simply create a new controller and blammo, it works. And wouldn't it be great if routes for that controller were defined in the same file, thus encapsulating all the controller's application and route logic in the same place? 

This class allows you to use annotation based routing for your micro applications. The setup is very simple: Add a directory for  controller classes, and add some simple route annotations to each controller. Annotation syntax follows the phalcon [annotations router](https://docs.phalconphp.com/en/latest/reference/routing.html#annotations-router).

Notes: 

* Controllers can, but do not need to extend the Phalcon MVC controller class.
* Classes/controllers without annotations are skipped. 
* Methods in the parsed classes/controllers without annotations are skipped. 

## Show me some code!

The following will mount routes annotated on controllers in the `controllers` directory:

```
// Create a basic Micro App
$app = new Phalcon\Mvc\Micro();

// Create the micro route annotation object, passing the micro app into the constructor
$microAnno = new \MicroRouteAnnotations($app);

// Add a directory of "controllers" that are namespaced
$microAnno->addControllerNamespace('SomeNamespace', 'controllers');

// Add the rotues to our micro app
$microAnno->mount();

// Just like always, handle your micro app
$app->handle();
```

An example of what a controller with annotations may look like:

```
<?php

/**
 * @RoutePrefix('/motos')
 */
class Motorcycles {

	/**
	 * @Get('/get-them-all')
	 */
	public function getAll() {
		echo json_encode(array('Harley Davidson', 'Honda', 'Moto Guzzi', 'Suzuki', 'Triumph', 'Yamaha', 'Zero', ));
	}
}
```

You would be able to call Motorcycles::getAll() by makinig a get request to /motos/get-them-all

## Simple, Default Rest Routes

You may also add the class annotation @RouteDefault('Rest') to automatically add routes for the following actions:

**indexAction()**
Automatically creates a route synonomous to @Get('/')

**getAction($id)**
Automatically creates a route synonomous to @Get('/{id}')

**putAction($id)**
Automatically creates a route synonomous to @Put('/{id}')

**postAction()**
Automatically creates a route synonomous to @Post('/')

**deleteAction($id)**
Automatically creates a route synonomous to @Delete('/{id}')
