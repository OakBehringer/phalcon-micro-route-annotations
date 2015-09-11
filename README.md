# phalcon-micro-route-annotations

This class allows you to use annotation based routing for your micro applications. The setup is very simple: Add a directory for your controllers (which may or may not extend the Phalcon MVC Controller class), and add some route annotations to the controller class. Annotation syntax follows the phalcon [annotations router](https://docs.phalconphp.com/en/latest/reference/routing.html#annotations-router).

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
