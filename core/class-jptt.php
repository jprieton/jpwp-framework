<?php

defined('ABSPATH') OR exit('No direct script access allowed');

/**
 * @since 0.0.1
 * @author jprieton
 */
class jpwp {

	/**
	 * @var \jpwp\core\Error
	 */
	public $error;

	/**
	 * @var \jpwp\core\Input
	 */
	public $input;

	public function __construct() {
		require_once 'class-error.php';
		require_once 'class-input.php';

		$this->error = new \jptt\core\Error();
		$this->input = new \jptt\core\Input();
	}

}
