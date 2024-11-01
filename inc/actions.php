<?php
//add our plugin non-admin scripts
function ultimatesocialshare_scripts() {
	global $post;

	if(!is_singular() || !isset($post)) {
		return;
	}

	//make sure shares are enabled for the post
	if(ultimatesocialshare_is_post_allowed($post)) {

		//front end css
		wp_register_style('ultimatesocialshare-css', plugin_dir_url(__DIR__).'assets/css/style.min.css', array(), ultimatesocialshare_version);
		wp_enqueue_style('ultimatesocialshare-css');

		if(!ultimatesocialshare_is_amp()) {

			//front end js
			wp_register_script('ultimatesocialshare-js', plugin_dir_url(__DIR__).'assets/js/ultimatesocialshare.min.js', array(), ultimatesocialshare_version);
			wp_enqueue_script('ultimatesocialshare-js');
		}
	}
}
add_action('wp_enqueue_scripts', 'ultimatesocialshare_scripts');

//add inline stylesheet for amp reader mode
function ultimatesocialshare_add_amp_css() {
	global $post;

	if(!is_singular() || !isset($post)) {

		return;
	}

	//make sure shares are enabled for the post
	if(ultimatesocialshare_is_post_allowed($post)) {
		include 'css/style.min.css';
	}
}
add_action('amp_post_template_css', 'ultimatesocialshare_add_amp_css');



/**
 * Register Enqueue admin CSS
*/
function ultimatesocialshare_enqueue_custom_admin_style() {
        wp_enqueue_style( 'ultimatesocialshare_wp_admin_css', plugin_dir_url(__DIR__).'assets/css/admin.css', false, '1.0.0' );
        wp_enqueue_style( 'ultimatesocialshare_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'ultimatesocialshare_enqueue_custom_admin_style' );



//get ultimatesocialshare meta details for post 
function ultimatesocialshare_get_post_details($post_id) {

	if($post_id) {

		global $ultimatesocialshare_post_details;

		if(isset($ultimatesocialshare_post_details[$post_id]) || (is_array($ultimatesocialshare_post_details) && array_key_exists($post_id, $ultimatesocialshare_post_details))) {
			return $ultimatesocialshare_post_details[$post_id];
		}

		global $wpdb;

		$ultimatesocialshare_post_details[$post_id] = maybe_unserialize($wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}ultimatesocialshare_meta WHERE post_id = %d AND meta_key = 'details'", $post_id)));

		return $ultimatesocialshare_post_details[$post_id];
	}
}