<?php

namespace SomeNamespace;

class Index {

	/**
	 * @Get('/index');
	 */
	public function indexAction() {
		echo 'Welcome! Take a look at the controllers to see what is going on... or click here to see a route in action: ' .
			'<a href="./fart/toot/chloe/4">Click Me</a>';
	}

}