<?php

/**
 * @RoutePrefix('/motos')
 * @RouteDefault('Rest')
 */
class Motorcycles {

	/**
	 * @Get('/get-them-all')
	 */
	public function getAll() {
		echo json_encode(array(
			'Harley Davidson',
			'Honda',
			'Moto Guzzi',
			'Suzuki',
			'Triumph',
			'Yamaha',
			'Zero',
		));
	}

}