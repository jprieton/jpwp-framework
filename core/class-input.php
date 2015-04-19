<?php

namespace jpwp\core;

defined('ABSPATH') or die('No direct script access allowed');

/**
 * @since 0.0.1
 * @author jprieton
 */
class Input {

	/**
	 *
	 * @param string $index
	 * @param mixed $fallback
	 * @param int $filter
	 * @param array $options
	 * @return mixed
	 * @since 0.0.1
	 * @author jprieton
	 */
	public function post($index, $filter = FILTER_DEFAULT, $options = array()) {
		$value = filter_input(INPUT_POST, $index, $filter, $options);
		$sanitized_value = sanitize_text_field($value);
		return $sanitized_value;
	}

	/**
	 *
	 * @param string $index
	 * @param mixed $fallback
	 * @param int $filter
	 * @param array $options
	 * @return mixed
	 * @since 0.0.1
	 * @author jprieton
	 */
	public function get($index, $filter = FILTER_DEFAULT, $options = array()) {
		$value = filter_input(INPUT_GET, $index, $filter, $options);
		$sanitized_value = sanitize_text_field($value);
		return $sanitized_value;
	}

	/**
	 *
	 * @param string $key
	 * @param string $method
	 * @return string
	 * @since 0.0.1
	 * @author jprieton
	 */
	public function get_wpnonce($key = '_wpnonce', $method = 'post') {
		if (in_array($method, array('post', 'get', 'post_get', 'get_post'))) {
			return $this->{$method}($key, FILTER_SANITIZE_STRIPPED);
		} else {
			return $this->post($key, FILTER_SANITIZE_STRIPPED);
		}
	}

	/**
	 *
	 * @param string $action
	 * @param string $key
	 * @param string $method
	 * @return int
	 * @since 0.0.1
	 * @author jprieton
	 */
	public function verify_wpnonce($action, $key = '_wpnonce', $method = 'post') {
		$nonce = $this->get_wpnonce($key, $method);
		return wp_verify_nonce($nonce, $action);
	}

}
