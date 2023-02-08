<?php

/**
 * Plugin Name:       	 gimi65536's Tools
 * Plugin URI:           https://blog.gimi65536.xyz/
 * Description:       	 A custom plugin used for gimi65536's wordpress blog
 * Version:           	 0.0.1
 * Requires at least:    6.0.0
 * Requires PHP:         7.4
 * Author:            	 Wayne Zeng
 * Author URI:        	 https://blog.gimi65536.xyz/
 * Text Domain:       	 gimi65536-tools
 * Domain Path:          /languages
 */

function g6tools_load_textdomain(){
	load_plugin_textdomain('gimi65536-tools', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'g6tools_load_textdomain');

$rootdir = dirname(__FILE__);
require_once($rootdir . '/options.php');
require_once($rootdir . '/main.php');