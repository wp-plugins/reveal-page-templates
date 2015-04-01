<?php
/*
Plugin Name: Reveal Page Templates
Plugin URI: http://www.studiograsshopper.ch/reveal-page-templates/
Version: 1.3.1
Text Domain: reveal-page-templates
Domain Path: /lang
Author: Ade Walker, Studiograsshopper
Author URI: http://www.studiograsshopper.ch
Description: Adds a column to the Edit Pages Dashboard screen to display the Page Template assigned to each Page. Requires WP 2.8+.
*/

/*  Copyright 2009-2015  Ade WALKER  (email : info@studiograsshopper.ch)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License 2 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
	
*/

/* Version History

1.3.1		- Enhance:	Fixed I18n coding to meet WP 3.7+ standards
			- Enhance:	SGR_RPT_DOMAIN constant deprecated
			- Enhance:	Merged old WPMU code with main code

1.3			- Feature:	Reveal Template column is now sortable
			- Enhance:	Cleaned up some code in older functions

1.2			- Bug fix:	Added is_admin() check for loading actions/hooks
			
1.1			- Bug fix:	SGR_RPT_FILE_NAME constant now properly defined.
			
1.0			- Public Release
	
*/

/* ******************** DO NOT edit below this line! ******************** */

/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
	exit( __( "Sorry, you are not allowed to access this page directly.", 'reveal-page-templates' ) );
}



/* Pre-2.6 compatibility to find directories */
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


/* Set constants for plugin */
define( 'SGR_RPT_URL', WP_PLUGIN_URL.'/reveal-page-templates' );
define( 'SGR_RPT_DIR', WP_PLUGIN_DIR.'/reveal-page-templates' );
define( 'SGR_RPT_VER', '1.3.1' );
define( 'SGR_RPT_WP_VERSION_REQ', '3.1' );
define( 'SGR_RPT_FILE_NAME', 'reveal-page-templates/reveal-page-templates-plugin.php' );


/***** Set up variables needed throughout the plugin *****/

// Internationalisation functionality
$sgr_rpt_text_loaded = false;



/***** Load files needed for plugin to run ********************/

/**
 * Load files needed for plugin to run
 *
 * Required for Public
 * None
 *
 * Required for Admin
 * rpt-admin-core.php - Main plugin functions
 *
 * @since 1.0
 */ 
if( is_admin() ) {
	require_once( SGR_RPT_DIR . '/includes/rpt-admin-core.php');
}



/***** Add filters and actions ********************/

if( is_admin() ) {

	/* Admin - Adds WP version warning on main Plugins screen */
	// Function defined in rpt-admin-core.php
	add_action('after_plugin_row_reveal-page-templates/reveal-page-templates-plugin.php', 'sgr_rpt_wp_version_check');

	/* Admin - Adds additional links in main Plugins page */
	// Function defined in rpt-admin-core.php
	add_filter( 'plugin_row_meta', 'sgr_rpt_plugin_meta', 10, 2 );

	/* Plugin - Adds column to Edit Pages screen */
	// Function defined in rpt-admin-core.php
	add_filter('manage_pages_columns', 'sgr_rpt_posts_columns');

	/* Plugin - Populates new column in Edit Pages screen */
	// Function defined in rpt-admin-core.php
	add_action('manage_pages_custom_column', 'sgr_rpt_custom_posts_column', 10, 2);

	/* Plugin & Admin - Loads language support */
	// Function defined in rpt-admin-core.php
	add_action('plugins_loaded', 'sgr_rpt_load_textdomain');
	
	/* Plugin - Registers new column as sortable */
	// Function defined in rpt-admin-core.php
	add_filter( 'manage_edit-page_sortable_columns', 'sgr_rpt_column_register_sortable' );
	
	/* Plugin - Deals with the orderby= request when sorting */
	// Function defined in rpt-admin-core.php
	add_filter( 'request', 'sgr_rpt_column_orderby' );
}



/***** Functions used by both public and admin *****/

/**
 * Function to load textdomain for Internationalisation functionality
 *
 * Plugin updated as per ottopress.com article:
 * http://ottopress.com/2013/language-packs-101-prepwork/
 *
 * Loads textdomain if $sgr_rpt_text_loaded is false
 *
 * Hooked to 'plugins_loaded' action
 *
 * @since 1.0
 * @updated 1.3.1
 */
function sgr_rpt_load_textdomain() {
	
	global $sgr_rpt_text_loaded;
   	
	// If textdomain is already loaded, do nothing
	if( $sgr_rpt_text_loaded ) {
   		return;
   	}
	
	// Textdomain isn't already loaded, let's load it
   	load_plugin_textdomain( 'reveal-page-templates', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
   	
	// Change variable to prevent loading textdomain again
	$sgr_rpt_text_loaded = true;
}