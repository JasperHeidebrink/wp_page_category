<?php
/**
 * @package Jasper
 * @version 1.0
 */
/*
Plugin Name:	DG Category Link
Plugin URI:		http://d
Description: 	Deze plugin koppelt categoriën aan pagina's
Author: 		Dragonet
Version: 		1.0
Author URI: 	http://www.dragonet.nl/

This plugin is heavily inspired from Category Page by Paolo Tresso / Pixline (http://pixline.net/). 

*/
if ( ! defined( 'DGCAT_VERSION ' ) ) {
	define( DGCAT_VERSION, '1.0' );
	define( DGCAT_TABLENAME, 'dg_category_link' );
	define( DGCAT_PARENTCAT, 3 );
}

require( dirname( __FILE__ ) . '/dg_category-admin-functions.php' );
require( dirname( __FILE__ ) . '/dg_category-front-functions.php' );


// add the plugin to the menu
function dbcat_plugin_menu() {
	// add the options page
	// add_options_page( 'Categorie link opties', 'Categorie-link opties', 8, 'dgcat-page-options', 'dgcat_option_page');

	// add a box to the edit page
	add_meta_box( 'dgcat', 'Gekoppelde categoriën', 'dgcat_add_meta_box', 'page', 'normal', 'low' );
}


// init the plugin
function dbcat_init() {
	//call register settings function
	register_setting( 'dgcat_options', 'dgcat_single_page' );

	// load the javascript
	wp_register_script( 'dgcat_script', WP_PLUGIN_URL . '/dg_category/js/script.js' );
	wp_enqueue_script( 'dgcat_script' );
}

/**
 * The Real init part
 * adding extra functionality to the PostObject and PageObject Save Handler
 * INIT specific admin functions
 * extend the admin menu
 */
register_activation_hook( __FILE__, 'dgcat_categorylink_activate' );
add_action( 'save_post', 'dbcat_save' );
add_action( 'admin_init', 'dbcat_init' );
add_action( 'admin_menu', 'dbcat_plugin_menu' );
