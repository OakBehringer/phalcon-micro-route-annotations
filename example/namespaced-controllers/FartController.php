<?php

namespace SomeNamespace;

/**
 * This controller is namespaced. It does not extend the Phalcon MVC controller. It uses the default routes for rest,
 * so that you do not need to define the route for each default action. You can, however, combine this technique with
 * function based annotations - see the last action still  uses function annotations to define another route/action.
 *
 * @RoutePrefix('/fart')
 * @RouteDefault('Rest')
 */
class FartController {

	protected $fakeData = array(
		'Silent, but deadly',
		'Beefy',
		'Sewage',
		'Tangy',
		'Toxic',
	);

	/**
	 * Route for this method is a GET request to /
	 * RouteDefault + RoutePrefix = this method called via GET request to /fart
	 */
	public function indexAction() {
		echo json_encode($this->fakeData);
	}

	/**
	 * Route for this method is a GET request to /{id}
	 * RouteDefault + RoutePrefix = this method called via GET request to /fart/2
	 */
	public function getAction($id) {
		echo json_encode($this->fakeData[$id]);
	}

	/**
	 * Route for this method is a POST request to /
	 * RouteDefault + RoutePrefix = this method called via POST request to /fart
	 */
	public function postAction() {
		echo json_encode($_POST);
	}

	/**
	 * Route for this method is a PUT request to /{id}
	 * RouteDefault + RoutePrefix = this method called via PUT request to /fart/2
	 */
	public function putAction($id) {
		echo json_encode(array(
			'status' => $this->fakeData[$id] . ' Updated'
		));
	}

	/**
	 * Route for this method is a DELETE request to /{id}
	 * RouteDefault + RoutePrefix = this method called via DELETE request to /fart/2
	 */
	public function deleteAction($id) {
		echo json_encode(array(
			'status' => $this->fakeData[$id] . ' Deleted'
		));
	}

	/**
	 * Call this method via /fart/toot/chloe/4
	 *
	 * @Get('/toot/{person:[a-z]+}/{type:[0-9]+}')
	 */
	public function tootAction($person, $type) {
		echo $person . ' just tooted a ' . $this->fakeData[$type] . ' toot!';
	}

}