<?php

/*
Plugin Name:  Mathilda
Plugin URI:   https://www.unmus.de/wordpress-plugin-mathilda/
Description:  Mathilda copies your tweets from Twitter to WordPress.
Version:	  0.12
Author:       Marco Hitschler
Author URI:   https://www.unmus.de/
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Domain Path:  /languages
Text Domain:  mathilda
*/

/*
Security
*/

if (!defined('ABSPATH'))
{
	exit;
}

/*
Basic Setup
*/

require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
require_once('mathilda_settings.php');
require_once('mathilda_database.php');
require_once('mathilda_painting.php');
require_once('mathilda_tools.php');
require_once('mathilda_filter.php');
require_once('mathilda_export.php');
require_once('mathilda_developer.php');
require_once('mathilda_validate.php');
require_once('mathilda_utilities.php');
require_once('mathilda_twitterapi.php');
require_once('mathilda_cron.php');
require_once('mathilda_import.php');
require_once('mathilda_scripting.php');
require_once('mathilda_dashboard.php');
require_once('mathilda_reporting.php');
require_once('mathilda_update.php');
require_once('mathilda_schedule.php');
require_once('mathilda_notification.php');
require_once('mathilda_hooks.php');

/*
Activate Plugin
*/

function mathilda_activate () {

	if (! get_option('mathilda_activated') ) {

	/* Initialize Settings */

	add_option('mathilda_oauth_access_token',"");
	add_option('mathilda_oauth_access_token_secret', "");
	add_option('mathilda_consumer_key', "");
	add_option('mathilda_consumer_secret', "");
	add_option('mathilda_twitter_user', "");
	add_option('mathilda_num_tweets_fetch_call', "200");
	add_option('mathilda_num_fetches', "1");
	add_option('mathilda_retweets', "0");
	add_option('mathilda_replies', "0");
	add_option('mathilda_quotes', "0");
	add_option('mathilda_initial_load', "0");
	add_option('mathilda_latest_tweet', '');
	add_option('mathilda_tweets_on_page', "300");
	add_option('mathilda_slug', "tweets");
	add_option('mathilda_tweets_count', "0");
	add_option('mathilda_activated', "1");
	add_option('mathilda_database_version', "1");
	add_option('mathilda_plugin_version', "15");
	add_option('mathilda_import', "0");
	add_option('mathilda_slug_is_changed', "0");
	add_option('mathilda_cron_period', "900");
	add_option('mathilda_highest_imported_tweet', '');
	add_option('mathilda_navigation', 'Standard');
	add_option('mathilda_hyperlink_rendering', 'Longlink');
	add_option('mathilda_css', "0");
	add_option('mathilda_select_amount', "0");
	add_option('mathilda_embed', "0");
	add_option('mathilda_tweet_backlink', "0");
	add_option('mathilda_cron_lastrun', "0");
	add_option('mathilda_import_running',"0");
	add_option('mathilda_import_open',"0");
	add_option('mathilda_import_files',"0");
	add_option('mathilda_import_numberoffiles',"0");
	add_option('mathilda_import_interval', "86400");
	add_option('mathilda_import_finish', "0");
	add_option('mathilda_import_subprocess_running', "0");
	add_option('mathilda_load_process_running',"0");
	add_option('mathilda_import_filesize_max',"409600");
	add_option('mathilda_cron_status',"0");

	/* Create Mathilda Tables */

	mathilda_tables_create();

	/* Create Directories */

	$upload_dir = wp_upload_dir();

	$twitter_apidata_dirname="mathilda-twitterapidata";
	$twitter_apidata_dirwithpath = $upload_dir['basedir'].'/'.$twitter_apidata_dirname;
	if ( ! file_exists( $twitter_apidata_dirwithpath ) ) {
		wp_mkdir_p( $twitter_apidata_dirwithpath );
		}

	$mathilda_images_dirname="mathilda-images";
	$mathilda_images_dirwithpath = $upload_dir['basedir'].'/'.$mathilda_images_dirname;
	if ( ! file_exists( $mathilda_images_dirwithpath ) ) {
		wp_mkdir_p( $mathilda_images_dirwithpath );
		}

	$twitter_import_directory="mathilda-import";
	$twitter_import_path = $upload_dir['basedir'].'/'.$twitter_import_directory;
	if ( ! file_exists( $twitter_import_path ) ) {
		wp_mkdir_p( $twitter_import_path );
		}

	$twitter_export_directory="mathilda-export";
	$twitter_export_path = $upload_dir['basedir'].'/'.$twitter_export_directory;
	if ( ! file_exists( $twitter_export_path ) ) {
		wp_mkdir_p( $twitter_export_path );
		}

	/* Rewrite Rules Refresh */

	flush_rewrite_rules();

	}

}

register_activation_hook( __FILE__ , 'mathilda_activate' );

/*
Deactivate
*/

function mathilda_deactivate () {

	flush_rewrite_rules();

	$timestamp = wp_next_scheduled( 'mathilda_embed_schedule' );
   	wp_unschedule_event($timestamp, 'mathilda_embed_schedule' );

	$timestamp = wp_next_scheduled( 'mathilda_tweetload_schedule' );
	wp_unschedule_event($timestamp, 'mathilda_tweetload_schedule' );
	   
	$timestamp = wp_next_scheduled( 'mathilda_import_schedule' );
   	wp_unschedule_event($timestamp, 'mathilda_import_schedule' );

}

register_deactivation_hook( __FILE__ , 'mathilda_deactivate' );

/*
Delete
*/

function mathilda_delete () {

		if ( get_option('mathilda_activated') ) {

		/* Delete Options */

		delete_option('mathilda_oauth_access_token');
		delete_option('mathilda_oauth_access_token_secret');
		delete_option('mathilda_consumer_key');
		delete_option('mathilda_consumer_secret');
		delete_option('mathilda_twitter_user');
		delete_option('mathilda_num_tweets_fetch_call');
		delete_option('mathilda_num_fetches');
		delete_option('mathilda_retweets');
		delete_option('mathilda_replies');
		delete_option('mathilda_initial_load');
		delete_option('mathilda_latest_tweet');
		delete_option('mathilda_tweets_on_page');
		delete_option('mathilda_slug');
		delete_option('mathilda_tweets_count');
		delete_option('mathilda_activated');
		delete_option('mathilda_database_version');
		delete_option('mathilda_plugin_version');
		delete_option('mathilda_tweets_count');
		delete_option('mathilda_import');
		delete_option('mathilda_slug_is_changed');
		delete_option('mathilda_cron_period');
		delete_option('mathilda_highest_imported_tweet');
		delete_option('mathilda_navigation');
		delete_option('mathilda_hyperlink_rendering');
		delete_option('mathilda_css');
		delete_option('mathilda_select_amount');
		delete_option('mathilda_quotes');
		delete_option('mathilda_embed');
		delete_option('mathilda_tweet_backlink');
		delete_option('mathilda_cron_lastrun');
		delete_option('mathilda_import_running');
		delete_option('mathilda_import_open');
		delete_option('mathilda_import_files');
		delete_option('mathilda_import_numberoffiles');
		delete_option('mathilda_import_interval');
		delete_option('mathilda_import_finish');
		delete_option('mathilda_import_subprocess_running');
		delete_option('mathilda_load_process_running');
		delete_option('mathilda_import_filesize_max');
		delete_option('mathilda_cron_status');

		/* Delete Tables */

		mathilda_tables_delete();

		// Directories will not removed

	}

}

register_uninstall_hook( __FILE__ , 'mathilda_delete' );

/*
Template
*/

function mathilda_template($content) {

	// Run Mathilda
	if ( mathilda_is_tweet_page() ) {

		// Prepare
		$alliswell=mathilda_run_yes_or_no();
		$mathilda_pages_amount=mathilda_pages();
		$mathilda_show_page=mathilda_which_page();
		$mathilda_content_html='';

		// Template
		require('mathilda_template.php');
		return $content . $mathilda_content_html;

	} else {

		// Content without Tweets
		return $content;

	}

}
add_filter ('the_content', 'mathilda_template');

/*
CSS @ Mathilda
*/

function mathilda_css() {
			if ( mathilda_is_tweet_page() )
			{
				if(get_option('mathilda_css')==0) {
				$add_css='<link rel="stylesheet" id="mathilda-css" href="'. plugins_url() .'/mathilda/mathilda_tweets.css" type="text/css" media="all">';
				echo $add_css;
				}
			}
}

add_action('wp_head','mathilda_css');

function mathilda_admin_css() {
			$add_css='<link rel="stylesheet" id="mathilda-css" href="'. plugins_url() .'/mathilda/mathilda_options.css" type="text/css" media="all">';
			echo $add_css;
}
add_action( 'admin_head', 'mathilda_admin_css' );

function mathilda_class( $classes ) {

	if ( mathilda_is_tweet_page() ) {
        $classes[] = 'mathilda-is-here';
        return $classes;
    }
	else {
		$classes[] = 'mathilda-is-not-here';
        return $classes;
	}
}
add_filter( 'body_class', 'mathilda_class' );

/*
Query Vars @ Mathilda
*/

function mathilda_urlvars( $qvars )
{
$qvars[] = 'mathilda';
return $qvars;
}

add_filter('query_vars', 'mathilda_urlvars' );

/*
Run Mathilda - Yes or No?
*/

function mathilda_run_yes_or_no() {

	if( ( get_option('mathilda_initial_load') == 0 ) AND ( get_option('mathilda_import') == 0) ) {
		return false;
	}
	else {
		return true;
	}

}

/*
Mathilda Which Page
*/

function mathilda_which_page() {

	$mathilda_show_page=get_query_var( 'mathilda');
	if ($mathilda_show_page=="") {$mathilda_show_page="1/";}
	$mathilda_show_page=intval ($mathilda_show_page);
	return $mathilda_show_page;

}

/*
Mathilda Loop
*/

function mathilda_loop($mathilda_show_page) {

	$loop_output='';
	$me=get_option('mathilda_twitter_user');
	$tweets_on_page=get_option('mathilda_tweets_on_page');

	/* Build Tweet Cache */
	$tweet_cache=array();
	$tweet_cache=mathilda_read_tweets($tweets_on_page, $mathilda_show_page);
	$num_tweets=count($tweet_cache);

	/* Apply Filter */
	$tweet_filter_cache=array();
	$tweet_filter_cache=mathilda_tweet_filter($tweet_cache, $num_tweets, 'L');

	/* Display Tweets */
	for($i=0; $i < $num_tweets; $i++)
	{
		if(isset($tweet_filter_cache[$i][0]))
		{
			$loop_output.=mathilda_tweet_paint($tweet_filter_cache[$i][0],
								      $tweet_filter_cache[$i][1],
								 	  $tweet_filter_cache[$i][2],
								 	  $me,
								 	  $tweet_filter_cache[$i][5],
								 	  $tweet_filter_cache[$i][4],
								      $tweet_filter_cache[$i][6],
								 	  $tweet_filter_cache[$i][3]
								      );
		}
	}

	return $loop_output;

}

/*
Rewrite Rules
*/

function mathilda_insert_rewrite( $rules ) {

$slug = get_option( 'mathilda_slug' );
$newrules = array();
$newrules['('.$slug.')/(.+)$'] = 'index.php?pagename=$matches[1]&mathilda=$matches[2]';
return $newrules + $rules;

}
add_filter( 'rewrite_rules_array','mathilda_insert_rewrite' );

/*
The unbelievable shortcode
*/

function mathilda_shortcode() {

	// Code Code Code

}

add_shortcode('mathilda','mathilda_shortcode');

/*
Links @ Plugin Page
*/

function add_mathilda_action_links ( $links ) {
$mathildalinks = array('<a href="' . admin_url( 'options-general.php?page=mathilda-options' ) . '">Settings</a>','<a href="' . admin_url( 'tools.php?page=mathilda-tools-menu' ) . '">Tools</a>');
return array_merge( $links, $mathildalinks );
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_mathilda_action_links' );

?>
