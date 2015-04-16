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
	public function post($index, $fallback = '', $filter = FILTER_DEFAULT, $options = array()) {
		$value = filter_input(INPUT_POST, $index, $filter, $options);
		$sanitized_value = sanitize_text_field($value);
		return (empty($sanitized_value)) ? $fallback : $sanitized_value;
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
	public function get($index, $fallback = '', $filter = FILTER_DEFAULT, $options = array()) {
		$value = filter_input(INPUT_GET, $index, $filter, $options);
		$sanitized_value = sanitize_text_field($value);
		return (empty($sanitized_value)) ? $fallback : $sanitized_value;
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
	public function post_get($index, $fallback = '', $filter = FILTER_DEFAULT, $options = array()) {
		$post = $this->post($index, $fallback, $filter, $options);
		return !empty($post) ? $post : $this->get($index, $fallback, $filter, $options);
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
	public function get_post($index, $fallback = '', $filter = FILTER_DEFAULT, $options = array()) {
		$get = $this->post($index, $fallback, $filter, $options);
		return !empty($get) ? $get : $this->post($index, $fallback, $filter, $options);
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
			return $this->{$method}($key, '', FILTER_SANITIZE_STRIPPED);
		} else {
			return $this->post($key, '', FILTER_SANITIZE_STRIPPED);
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
