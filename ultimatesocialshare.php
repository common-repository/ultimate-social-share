<?php
/*
Plugin Name: Ultimate Social Share
Plugin URI: https://wpclubz.com/ultimate-social-share/
Description: Ultimate Social Media Share is a lightweight and fast social media sharing/follow buttons plugin for posts and custom posts.
Version: 1.0.4
Author: wpclubz
Author URI: https://wpclubz.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ultimatesocialshare
Domain Path: /languages
*/

//Defines
define('ultimatesocialshare_version', '1.0.4');

if( !defined( 'ULTIMATESOCIALSHARE_PATH' ) ) {
	define( 'ULTIMATESOCIALSHARE_PATH', plugin_dir_path( __FILE__ ) );
}

//load translations
function ultimatesocialshare_load_textdomain() {
	load_plugin_textdomain('ultimatesocialshare', false, dirname(plugin_basename( __FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'ultimatesocialshare_load_textdomain');



//Include Admin panel
require_once (ULTIMATESOCIALSHARE_PATH.'admin/classes/setup.class.php' );
require_once (ULTIMATESOCIALSHARE_PATH .'admin/options/admin-options.php' );



//Include all the files
include plugin_dir_path(__FILE__) . '/inc/actions.php';
include plugin_dir_path(__FILE__) . '/inc/functions.php';
include plugin_dir_path(__FILE__) . '/inc/share_counts.php';
include plugin_dir_path(__FILE__) . '/inc/share_counts_recovery.php';