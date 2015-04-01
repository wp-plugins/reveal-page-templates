<?php
/**	
 * Functions which do all the backend admin stuff
 *
 * @author Ade WALKER  (email : info@studiograsshopper.ch)
 * @copyright Copyright 2009-2015
 * @package	reveal_page_templates
 * @version	1.3.1
 *
 * Core Admin Functions called by various add_filters and add_actions:
 * - Plugin action links
 * - Plugin row meta
 * - WP Version check
 * - Plugin function: add column to Edit Pages
 * - Plugin function: populate new column in Edit Pages
 *
 * @since 1.0
 *
 */

/* Prevent direct access to this file */
if ( !defined( 'ABSPATH' ) ) {
	exit( __( 'Sorry, you are not allowed to access this file directly.', 'reveal-page-templates' ) );
}



/***** Admin Functions *****/


/**
 * Display Plugin Meta Links in main Plugin page in Dashboard
 *
 * Adds additional meta links in the plugin's info section in main Plugins Settings page
 *
 * Hooked to plugin_row_meta filter, so only works for WP 2.8+
 *
 * @since 1.0
 */
function sgr_rpt_plugin_meta($links, $file) {
 
	// $file is the main plugin filename
 
	// Check we're only adding links to this plugin
	if( $file == SGR_RPT_FILE_NAME ) {
	
		// Create links
		$faq_link = '<a href="http://www.studiograsshopper.ch/reveal-page-templates/faq/" target="_blank">' . __('FAQ', 'reveal-page-templates') . '</a>';
		$docs_link = '<a href="http://www.studiograsshopper.ch/reveal-page-templates/documentation/" target="_blank">' . __('Documentation', 'reveal-page-templates') . '</a>';
		$donation_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10131319">' . __('Donate', 'reveal-page-templates') . '</a>';
		
		return array_merge(
			$links,
			array( $faq_link, $docs_link, $donation_link )
			
		);
	}
 
	return $links;
}


/**
 * Function to do WP Version check
 *
 * RPT v1.3 requires WP 3.1+ to run. This function prints a warning
 * message in the main Plugins screen if version is less than 3.1.
 *
 * Called by add_filter('after_action_row_$plugin', )
 *
 * @since 1.0
 * @updated 1.3.1
 */
function sgr_rpt_wp_version_check() {
	
	$rpt_wp_valid = version_compare(get_bloginfo("version"), SGR_RPT_WP_VERSION_REQ, '>=');
	
	$current_page = basename($_SERVER['PHP_SELF']);
	
	// Check we are on the right screen
	if( $current_page == "plugins.php" ) {
	
		if( $rpt_wp_valid ) {
			// We're good, so do nothing
			return;
			
		} else {
			
			$version_message = '<tr class="plugin-update-tr"><td class="plugin-update" colspan="3">';
			$version_message .= '<div class="update-message" style="background:#FFEBE8;border-color:#BB0000;">';
			$version_message .= __('Warning! This version of Reveal Page Templates requires Wordpress', 'reveal-page-templates') . ' <strong>' . SGR_RPT_WP_VERSION_REQ . '</strong>+ ' . __('Please upgrade Wordpress to run this plugin.', 'reveal-page-templates');
			$version_message .= '</div></td></tr>';
			echo $version_message;
			
		}
	}
	
	// This will also show the version warning message at the top of the Plugins page
	// TODO: Clean this up
	if( $current_page == "plugins.php" ) {
		
		$version_top_msg_start = '<div class="error"><p>';
		$version_top_msg_end = '</p></div>';
		
		if( $dfcg_wp_valid ) {
			// We're good, so do nothing
			return;
			
		} else {
			
			$version_msg .= '<strong>' . __('Warning! This version of Reveal Page Templates requires Wordpress', 'reveal-page-templates') . ' ' . SGR_RPT_WP_VERSION_REQ . '+ ' . __('Please upgrade Wordpress to run this plugin.', 'reveal-page-templates') . '</strong>';
			echo $version_top_msg_start . $version_msg . $version_top_msg_end;
			
		}
	}
}



/***** Plugin Functions *****/


/**
 * Function to do add column to Edit Pages screen
 *
 * Adds new column in Dashboard Edit Pages
 *
 * Hooked to 'manage_pages_columns' filter
 *
 * @since 1.0
 * @updated 1.3
 */
function sgr_rpt_posts_columns( $columns ) {
    
    $columns['sgr_rpt_page_template'] = __( 'Page Template', 'reveal-page-templates' );
    
    return $columns;
}


/**
 * Function to populate new column in Edit Pages screen
 *
 * Populates new column with filenames of Page Templates
 * using _wp_page_template in wp_postmeta table
 *
 * Hooked to 'manage_pages_custom_column' action
 *
 * @since 1.0
 * @updated 1.3
 */
function sgr_rpt_custom_posts_column( $column_name, $post_id ) {
    
	// Check we're only messing with my column
	if( $column_name == 'sgr_rpt_page_template' ) {
	
		$column_content = get_post_meta( $post_id, '_wp_page_template', true );

        if( !$column_content )
        	echo '<em>'.__('default').'</em>';
		
		echo $column_content;
    }
}


/**
 * Function to register the column as sortable
 *
 * New WP 3.2 feature
 *
 * Hooked to 'manage_edit-page_sortable_columns' action
 *
 * @since 1.3
 */
function sgr_rpt_column_register_sortable( $columns ) {
	
	// Register the column and the query var whuch is used when sorting
	$columns['sgr_rpt_page_template'] = 'sgr_rpt_template';
 
	return $columns;
}


/**
 * Function to handle the query when sorting the column
 *
 * New WP 3.2 feature
 *
 * Hooked to 'request' filter
 * postmeta meta_key is _wp_page_template
 * sorted on orderby=meta_value
 *
 * @since 1.3
 */
function sgr_rpt_column_orderby( $vars ) {
	
	if ( isset( $vars['orderby'] ) && 'sgr_rpt_template' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => '_wp_page_template',
			'orderby' => 'meta_value'
		) );
	}
 
	return $vars;
}