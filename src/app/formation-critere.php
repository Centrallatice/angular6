<?php
/*
Plugin Name: Proposition de formation par critères
Plugin URI: https://www.facebook.com/Krealine.fr/
Description: Proposition de formation par critères
Author: DUPONT Sylvain
Version: 1.0
Author URI: https://www.facebook.com/Krealine.fr/
*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

register_activation_hook( __FILE__, array( 'formationCritere', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'formationCritere', 'plugin_deactivation' ) );


define( 'CC__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( CC__PLUGIN_DIR . 'class.formationCritere.php' );

add_action( 'init', array( 'formationCritere', 'init' ) );
