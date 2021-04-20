<?php
/**
 * Multilist Subscribe, for Sendy
 *
 * Multilist Subscribe, for Sendy adds a subscribe widget and campaign templates, for one or more Sendy lists, to your WordPress website.
 *
 * @package   Multilist_Subscribe_for_Sendy
 * @author    Chris Nesbit <chris.nesbit1+wp@gmail.com>
 * @license   GPL-2.0+
 * @link      https://chrisanesbit.com
 * @copyright 2020 Chris A Nesbit
 *
 * @wordpress-plugin
 * Plugin Name:       Multilist Subscribe, for Sendy
 * Plugin URI:        https://chrisanesbit.com/multilist-subscribe-for-sendy
 * Description:       Multilist Subscribe, for Sendy adds a subscribe widget and campaign templates, for one or more Sendy lists, to your WordPress website.
 * Version:           1.6.1
 * Author:            Chris Nesbit
 * Author URI:        https://chrisanesbit.com
 * Text Domain:       sendy-multilist
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://plugins.svn.wordpress.org/multilist-subscribe-for-sendy/
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

// Init Freemius.
require_once 'lib/wpsm-freemius.php';
msfs_fs(wpsm_server_plugin_directory());

//sendy email template posttype
require_once 'lib/wpsm-campaign-templates.php';

require 'lib/wpsm-widget.php';

add_action( 'widgets_init', function(){
    register_widget( 'WPSM_Widget' );
});

function wpsm_support_enqueue() {
  $screen = get_current_screen();
  //sendy_multilist pages
  $wpsm_pages = array('widgets', 'edit-sendyemailtemplates', 'sendyemailtemplates');
  if (in_array($screen->id, $wpsm_pages)) {
     wp_enqueue_style('wpsm_admin_css', wpsm_client_plugin_directory() . 'css/admin.css');
  }
}
add_action('admin_enqueue_scripts', 'wpsm_support_enqueue');