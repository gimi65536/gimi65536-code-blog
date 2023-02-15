<?php

/**
 * Plugin Name:       	 gimi65536's Tools
 * Plugin URI:           https://blog.gimi65536.xyz/
 * Description:       	 A custom plugin used for gimi65536's wordpress blog to invoke my tool API
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

function g6tools_load_style(){
	$plugin_url = plugin_dir_url(__FILE__);
	wp_enqueue_style('g6tools-style', $plugin_url . 'style.css');
}
add_action('wp_enqueue_scripts', 'g6tools_load_style');

$rootdir = dirname(__FILE__);
require_once($rootdir . '/options.php');
require_once($rootdir . '/main.php');
require_once($rootdir . '/formfields.php');