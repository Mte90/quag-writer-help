<?php
/**
 * Quag Writer Help Plugin
 * @package   Quag_Writer_Help
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://wpandmore.info/quag-writer-help
 * @copyright 2013 WpAndMore
 *
 * @wordpress-plugin
 * Plugin Name: Quag Writer Help
 * Plugin URI:  http://wpandmore.info/quag-writer-help
 * Description: Quag Writer Help is a...
 * Version:     0.0.1
 * Author:      Andrea Barghigiani, Daniele Scasciafratte
 * Author URI:  TODO
 * Text Domain: plugin-name-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-qwh.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Quag_Writer_Help', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quag_Writer_Help', 'deactivate' ) );

Quag_Writer_Help::get_instance();