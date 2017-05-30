<?php
/**
 * Sendy Multilist
 *
 * Sendy Multilist adds a subscribe widget, for one or more Sendy lists, to your WordPress website.
 *
 * @package   Sendy_Multilist
 * @author    Chris Nesbit <admin@wplikeapro.com>
 * @license   GPL-2.0+
 * @link      https://wplikeapro.com
 * @copyright 2017 WP Like a Pro
 *
 * @wordpress-plugin
 * Plugin Name:       Sendy Multilist
 * Plugin URI:        https://wplikeapro.com/sendy-multilist
 * Description:       Sendy Multilist adds a subscribe widget, for one or more Sendy lists, to your WordPress website.
 * Version:           1.0.0
 * Author:            Chris Nesbit
 * Author URI:        https://wplikeapro.com
 * Text Domain:       sendy-multilist
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/chrisnesbit1/sendy-multilist
 */
 
 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

function wpsm_client_plugin_directory() {
    return WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
}

function wpsm_server_plugin_directory() {
    return dirname(__FILE__);
}

//sendy email template posttype
require_once 'lib/wpsm-campaign-templates.php';

require 'lib/wpsm-widget.php';

add_action( 'widgets_init', function(){
    register_widget( 'WPSM_Widget' );
});
