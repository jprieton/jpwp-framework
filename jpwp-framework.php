<?php

defined('ABSPATH') OR exit('No direct script access allowed');

/**
 * Plugin Name: JP WordPress Framework
 * Plugin URI: https://github.com/jprieton/jpwp-framework/
 * Description: Extends WordPress functionality
 * Version: 0.0.1
 * Author: Javier Prieto
 * Text Domain: jpwp
 * Domain Path: /languages
 * Author URI: https://github.com/jprieton/
 * License: GPL2
 */
define('JPWP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('JPWP_PLUGIN_URI', plugin_dir_url(__FILE__));

define('JPWP_THEME_PATH', get_stylesheet_directory());
define('JPWP_THEME_URI', get_stylesheet_directory_uri());

require_once JPWP_PLUGIN_PATH . '/core/class-jpwp.php';

global $jpwp;
$jpwp = new jpwp();

/**
 *
 * @global jpwp $jpwp
 * @return \jpwp
 */
function &get_instance() {
	global $jpwp;
	return $jpwp;
}
