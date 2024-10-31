<?php
/**
 * Plugin Name: Searchlive Lite
 * Plugin URI:  https://wordpress.org/plugins/
 * Description: Searchlive is a live search plugin for WordPress. This live search engine provides a user friendly and modern ajax powered search form.
 * Version:     1.0
 * Author:      colbycooper
 * Author URI:  https://themeforest.net/user/colbycooper
 * Text Domain: searchlive
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SEARCHLIVE__FILE__', __FILE__ );
define( 'SEARCHLIVE_PATH', plugin_dir_path( SEARCHLIVE__FILE__ ) );
define( 'SEARCHLIVE_URL', plugins_url( '/', SEARCHLIVE__FILE__ ) );
define( 'SEARCHLIVE_ASSETS_URL', SEARCHLIVE_URL . 'assets/' );
define( 'SEARCHLIVE_TEXT_DOMAIN', 'searchlive' );
define( 'SEARCHLIVE_PLUGIN_BASE', plugin_basename( SEARCHLIVE__FILE__ ) );
define( 'SEARCHLIVE_PLUGIN_NAME', 'Searchlive Lite');


function searchlive_plugins_loaded()
{
	// include main plugin file
	include_once ( SEARCHLIVE_PATH . 'inc/plugin.php' );
	load_plugin_textdomain(SEARCHLIVE_TEXT_DOMAIN, false, plugin_basename(__DIR__) . '/languages/');
}

add_action('plugins_loaded', 'searchlive_plugins_loaded');