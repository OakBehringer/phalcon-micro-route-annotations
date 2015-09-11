<?php

use Phalcon\Mvc\Controller;

/**
 * @RoutePrefix('/books');
 */
class BookController extends Controller {

	protected $fakeData = array(
		'It',
		'Pet Cemetary',
		'The Shining'
	);

	/**
	 * @Get('/')
	 */
	public function getAll() {
		echo json_encode($this->fakeData);
	}

	/**
	 * @Get('/{id:[0-9]+}')
	 */
	public function getBook($id) {
		echo json_encode(array($this->fakeData[$id]));
	}

}