<?php
/**	This file is part of the REVEAL PAGE TEMPLATES Plugin
*	*****************************************************
*	Copyright 2009  Ade WALKER  (email : info@studiograsshopper.ch)
*
* 	@package	reveal_page_templates
*	@version	1.0
*
*	Core Admin Functions called by various add_filters and add_actions:
*		- Plugin action links
*		- Plugin row meta
*		- WP Version check
*		- Plugin function: add column to Edit Pages
*		- Plugin function: populate new column in Edit Pages
*
*	@since	1.0
*
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', SGR_RPT_DOMAIN) );
}



/***** Admin Functions *****/


/**	Display Plugin Meta Links in main Plugin page in Dashboard
*
*	Adds additional meta links in the plugin's info section in main Plugins Settings page
*
*	Hooked to plugin_row_meta filter, so only works for WP 2.8+
*
*	@since	1.0
*/
function sgr_rpt_plugin_meta($links, $file) {
 
	// $file is the main plugin filename
 
	// Check we're only adding links to this plugin
	if( $file == SGR_RPT_FILE_NAME ) {
	
		// Create links
		$faq_link = '<a href="http://www.studiograsshopper.ch/reveal-page-templates/faq/" target="_blank">' . __('FAQ', SGR_RPT_DOMAIN) . '</a>';
		$docs_link = '<a href="http://www.studiograsshopper.ch/reveal-page-templates/documentation/" target="_blank">' . __('Documentation', SGR_RPT_DOMAIN) . '</a>';
		$donation_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10131319">' . __('Donate', SGR_RPT_DOMAIN) . '</a>';
		
		return array_merge(
			$links,
			array( $faq_link, $docs_link, $donation_link )
			
		);
	}
 
	return $links;
}


/**	Function to do WP Version check
*
*	RPT v1.0 requires WP 2.8+ to run. This function prints a warning
*	message in the main Plugins screen if version is less than 2.8.
*
*	Called by add_filter('after_action_row_$plugin', )
*
*	@since	1.0
*/
function sgr_rpt_wp_version_check() {
	
	$rpt_wp_valid = version_compare(get_bloginfo("version"), SGR_RPT_WP_VERSION_REQ, '>=');
	
	$current_page = basename($_SERVER['PHP_SELF']);
	
	// Check we are on the right screen
	if( $current_page == "plugins.php" ) {
	
		if( $rpt_wp_valid ) {
			// We're good, so do nothing
			return;
			
		} elseif( !function_exists('wpmu_create_blog') ) {
			// We're in WP
			$version_message = '<tr class="plugin-update-tr"><td class="plugin-update" colspan="3">';
			$version_message .= '<div class="update-message" style="background:#FFEBE8;border-color:#BB0000;">';
			$version_message .= __('Warning! This version of Reveal Page Templates requires Wordpress', SGR_RPT_DOMAIN) . ' <strong>' . SGR_RPT_WP_VERSION_REQ . '</strong>+ ' . __('Please upgrade Wordpress to run this plugin.', SGR_RPT_DOMAIN);
			$version_message .= '</div></td></tr>';
			echo $version_message;
			
		} else {
			// We're in WPMU
			$version_message = '<tr class="plugin-update-tr"><td class="plugin-update" colspan="3">';
			$version_message .= '<div class="update-message" style="background:#FFEBE8;border-color:#BB0000;">';
			$version_message .= __('Warning! This version of Reveal Page Templates requires WPMU', SGR_RPT_DOMAIN) . ' <strong>' . SGR_RPT_WP_VERSION_REQ . '</strong>+ ' . __('Please contact your Site Administrator.', SGR_RPT_DOMAIN);
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
			
		} elseif( !function_exists('wpmu_create_blog') ) {
			// We're in WP
			$version_msg .= '<strong>' . __('Warning! This version of Reveal Page Templates requires Wordpress', SGR_RPT_DOMAIN) . ' ' . SGR_RPT_WP_VERSION_REQ . '+ ' . __('Please upgrade Wordpress to run this plugin.', SGR_RPT_DOMAIN) . '</strong>';
			echo $version_top_msg_start . $version_msg . $version_top_msg_end;
			
		} else {
			// We're in WPMU
			$version_msg .= '<strong>' . __('Warning! This version of Reveal Page Templates requires WPMU', SGR_RPT_DOMAIN) . ' ' . SGR_RPT_WP_VERSION_REQ . '+ ' . __('Please contact your Site Administrator.', SGR_RPT_DOMAIN) . '</strong>';
			echo $version_top_msg_start . $version_msg . $version_top_msg_end;
		}
	}
}



/***** Plugin Functions *****/


/**	Function to do add column to Edit Pages screen
*
*	Adds new column in Dashboard Edit Pages
*
*	Called by add_filter('manage_pages_columns', )
*
*	@since	1.0
*/
function sgr_rpt_posts_columns($defaults) {
    $defaults['sgr_rpt_page_template'] = __('Page Template', SGR_RPT_DOMAIN);
    return $defaults;
}


/**	Function to do populate new column in Edit Pages screen
*
*	Populates new column with filenames of Page Templates
*	using _wp_page_template in wp_postmeta table
*
*	Called by add_action('manage_pages_custom_column', )
*
*	@since	1.0
*/
function sgr_rpt_custom_posts_column($column_name, $post_id) {
    
	global $wpdb;
    
	// Check we're only messing with my column
	if( $column_name == 'sgr_rpt_page_template' ) {
        
		$sgr_query = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.post_id = %d AND $wpdb->postmeta.meta_key = %s", $post_id, '_wp_page_template')
			);
        
        if( $sgr_query ) {
            $my_func = create_function('$att', 'return $att->meta_value;');
            $text = array_map( $my_func, $sgr_query );
            echo implode(', ',$text);
        } else {
			// This should never be called - but just in case
            echo '<i>'.__('default').'</i>';
        }
    }
}
