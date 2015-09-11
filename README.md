# phalcon-micro-route-annotations

This class allows you to use annotation based routing for your micro applications. The setup is very simple: Add a directory for your controllers (which may or may not extend the Phalcon MVC Controller class), and add some route annotations to the controller class. Annotation syntax follows the phalcon [annotations router](https://docs.phalconphp.com/en/latest/reference/routing.html#annotations-router).

For example, the following will mount routes annotated on controllers in the namespaced-controllers directory:

```
// Create a basic Micro App
$app = new Phalcon\Mvc\Micro();

// Create the micro route annotation object, passing the micro app into the constructor
$microAnno = new \MicroRouteAnnotations($app);

// Add a directory of "controllers" that are namespaced
$microAnno->addControllerNamespace('SomeNamespace', 'namespaced-controllers');

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
