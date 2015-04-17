<?php

namespace jpwp\core;

defined('ABSPATH') or die('No direct script access allowed');

/**
 * @since 0.0.1
 * @author jprieton
 */
class Loader {

	public function model($model) {
		if (file_exists(JPWP_THEME_PATH . '/models/class-' . $model . '.php')) {
			require_once JPWP_THEME_PATH . '/models/class-' . $model . '.php';
		} elseif (file_exists(JPWP_PLUGIN_PATH . '/models/class-' . $model . '.php')) {
			require_once JPWP_PLUGIN_PATH . '/models/class-' . $model . '.php';
		}

		$jpwp = get_instance();
		if (empty($jpwp->$model)) {
			try {
				$class_name = '\jpwp\models\\' . ucfirst($model);
				$jpwp->$model = new $class_name;
			} catch (Exception $exc) {
				echo $exc->getTraceAsString();
				$class_name = ucfirst($model);
				$jpwp->$model = new $class_name;
			}
		}
	}

	public function filter($filter) {
		if (file_exists(JPWP_THEME_PATH . '/filters/' . $filter . '.php')) {
			include_once JPWP_THEME_PATH . '/filters/' . $filter . '.php';
		} elseif (file_exists(JPWP_PLUGIN_PATH . '/filters/' . $filter . '.php')) {
			include_once JPWP_PLUGIN_PATH . '/filters/' . $filter . '.php';
		}
	}

	public function module($module, $override = FALSE) {
		if (file_exists(JPWP_THEME_PATH . '/modules/' . $module . '.php')) {
			include_once JPWP_THEME_PATH . '/modules/' . $module . '.php';
		}
		if (file_exists(JPWP_PLUGIN_PATH . '/modules/' . $module . '.php') && !$override) {
			include_once JPWP_PLUGIN_PATH . '/modules/' . $module . '.php';
		}
	}

	public function post_type($post_type) {
		if (file_exists(JPWP_THEME_PATH . '/post_types/' . $post_type . '.php')) {
			include_once JPWP_THEME_PATH . '/post_types/' . $post_type . '.php';
		} elseif (file_exists(JPWP_PLUGIN_PATH . '/post_types/' . $post_type . '.php')) {
			include_once JPWP_PLUGIN_PATH . '/post_types/' . $post_type . '.php';
		}
	}

	public function taxonomy($taxonomy) {
		if (file_exists(JPWP_THEME_PATH . '/taxonomies/' . $taxonomy . '.php')) {
			include_once JPWP_THEME_PATH . '/taxonomies/' . $taxonomy . '.php';
		} elseif (file_exists(JPWP_PLUGIN_PATH . '/taxonomies/' . $taxonomy . '.php')) {
			include_once JPWP_PLUGIN_PATH . '/taxonomies/' . $taxonomy . '.php';
		}
	}

}
