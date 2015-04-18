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

	/**
	 * @var \jpwp\core\Loader
	 */
	public $load;

	/**
	 * @var \jpwp\models\Users
	 */
	public $users;

	public function __construct() {
		require_once 'class-error.php';
		require_once 'class-input.php';
		require_once 'class-loader.php';

		$this->error = new \jpwp\core\Error();
		$this->input = new \jpwp\core\Input();
		$this->load = new \jpwp\core\Loader();
	}

	public function config($config) {
		
		// Models
		if (!empty($config['models'])) {
			$models = (array) $config['models'];
			foreach ($models as $model) {
				$this->load->model($model);
			}
		}
		// Post types
		if (!empty($config['post_types'])) {
			$post_types = (array) $config['post_types'];
			foreach ($post_types as $post_type) {
				$this->load->post_type($post_type);
			}
		}
		// Taxonomies
		if (!empty($config['taxonomies'])) {
			$taxonomies = (array) $config['taxonomies'];
			foreach ($taxonomies as $taxonomy) {
				$this->load->taxonomy($taxonomy);
			}
		}
		// Modules
		if (!empty($config['modules'])) {
			$modules = (array) $config['modules'];
			foreach ($modules as $module) {
				$this->load->module($module);
			}
		}
		// Filters
		if (!empty($config['filters'])) {
			$filters = (array) $config['filters'];
			foreach ($filters as $filter) {
				$this->load->filter($filter);
			}
		}
		// Helpers
		if (!empty($config['helpers'])) {
			$helpers = (array) $config['helpers'];
			foreach ($helpers as $helper) {
				$this->load->helper($helper);
			}
		}
	}

}
