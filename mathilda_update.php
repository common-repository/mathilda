<?php 

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

/*
Mathilda Update Related Functions
*/

/*
* Mathilda Update
*
* Plugin Update Procedure
*
* @param None
* @return None
*/

function mathilda_update () {

    $mathilda_previous_version = get_option('mathilda_plugin_version');

	/* Update Process Version 0.3 */
    if($mathilda_previous_version==1) {
	add_option('mathilda_slug_is_changed', "0");
	update_option('mathilda_plugin_version', "2");
	}

	/* Update Process Version 0.4 */
	if($mathilda_previous_version==2) {
	add_option('mathilda_cron_period', "900");
	update_option('mathilda_plugin_version', "3");
	$timestamp = wp_next_scheduled( 'mathilda_cron_hook' );
   	wp_unschedule_event($timestamp, 'mathilda_cron_hook' );
	$mathilda_replies_flag=get_option('mathilda_replies');
	if( $mathilda_replies_flag == FALSE ) {update_option('mathilda_replies','0');}
	add_option('mathilda_navigation', 'Numbering');
	}

	/* Update Process Version 0.4.1 */
    if($mathilda_previous_version==3) {
	update_option('mathilda_plugin_version', "4");
	}

	/* Update Process Version 0.4.2 */
    if($mathilda_previous_version==4) {
	update_option('mathilda_plugin_version', "5");
	}

	/* Update Process Version 0.5 */
    if($mathilda_previous_version==5) {
	update_option('mathilda_plugin_version', "6");
	add_option('mathilda_hyperlink_rendering', 'Longlink');
	add_option('mathilda_css', '0');
	}

	/* Update Process Version 0.6 */
    if($mathilda_previous_version==6) {
	update_option('mathilda_plugin_version', "7");
	add_option('mathilda_select_amount',"0");
	add_option('mathilda_quotes', "1");

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "mathilda_tweets";
	$sql = "CREATE TABLE $table_name (
	mathilda_tweet_id bigint NOT NULL AUTO_INCREMENT,
	mathilda_tweet_date varchar(14) NOT NULL,
	mathilda_tweet_content varchar(300) NOT NULL,
	mathilda_tweet_twitterid varchar(20) NOT NULL,
	mathilda_tweet_hashtags varchar(5) NOT NULL,
	mathilda_tweet_mentions varchar(5) NOT NULL,
	mathilda_tweet_media varchar(5) NOT NULL,
	mathilda_tweet_urls varchar(5) NOT NULL,
	mathilda_tweet_truncate varchar(5) NOT NULL,
	mathilda_tweet_reply varchar(5) NOT NULL,
	mathilda_tweet_retweet varchar(5) NOT NULL,
	mathilda_tweet_quote varchar(5) NOT NULL,
	PRIMARY KEY (mathilda_tweet_id),
 	UNIQUE KEY id (mathilda_tweet_twitterid)
	) $charset_collate;";
	dbDelta( $sql );

	$notnull="ND";

	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_truncate = %s", $notnull));
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_reply = %s", $notnull));
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_retweet = %s", $notnull));
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_quote = %s", $notnull));

	}

	/* Update Process Version 0.6.1 */
    if($mathilda_previous_version==7) {
	update_option('mathilda_plugin_version', "8");
	}

	/* Update Process Version 0.7 */
    if($mathilda_previous_version==8) {
	update_option('mathilda_plugin_version', "9");
	add_option('mathilda_embed', "0");

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "mathilda_tweets";
	$sql = "CREATE TABLE $table_name (
	mathilda_tweet_id bigint NOT NULL AUTO_INCREMENT,
	mathilda_tweet_date varchar(14) NOT NULL,
	mathilda_tweet_content varchar(3000) NOT NULL,
	mathilda_tweet_twitterid varchar(20) NOT NULL,
	mathilda_tweet_hashtags varchar(5) NOT NULL,
	mathilda_tweet_mentions varchar(5) NOT NULL,
	mathilda_tweet_media varchar(5) NOT NULL,
	mathilda_tweet_urls varchar(5) NOT NULL,
	mathilda_tweet_truncate varchar(5) NOT NULL,
	mathilda_tweet_reply varchar(5) NOT NULL,
	mathilda_tweet_retweet varchar(5) NOT NULL,
	mathilda_tweet_quote varchar(5) NOT NULL,
	mathilda_tweet_content_display_index_start varchar(5) NOT NULL,
	mathilda_tweet_content_display_index_end varchar(5) NOT NULL,
	mathilda_tweet_source varchar(7) NOT NULL,
	PRIMARY KEY (mathilda_tweet_id),
 	UNIQUE KEY id (mathilda_tweet_twitterid)
	) $charset_collate;";
	dbDelta( $sql );

	$valueupdate='FALSE';
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_content_display_index_start= %s",$valueupdate));
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_content_display_index_end= %s",$valueupdate));
	$source_update='UNKNOWN';
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_tweet_source= %s",$source_update));

	$table_name = $wpdb->prefix . "mathilda_urls";
	$sql = "CREATE TABLE $table_name (
	mathilda_url_id bigint NOT NULL AUTO_INCREMENT,
	mathilda_url_tco varchar(23) NOT NULL,
	mathilda_url_extended text NOT NULL,
	mathilda_url_display varchar(50) NOT NULL,
	mathilda_url_index_start varchar(3) NOT NULL,
	mathilda_url_index_end varchar(3) NOT NULL,
	mathilda_url_reference_tweet varchar(20) NOT NULL,
	mathilda_url_embed varchar(3000) NOT NULL,
	PRIMARY KEY (mathilda_url_id)
	) $charset_collate;";
	dbDelta( $sql );

	$valueupdate='OPEN';
	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_url_embed= %s",$valueupdate));

	$timestamp = wp_next_scheduled( 'mathilda_cron_hook' );
   	wp_unschedule_event($timestamp, 'mathilda_cron_hook' );

	}

	/* Update Process Version 0.8 */
    if($mathilda_previous_version==9) {
	update_option('mathilda_plugin_version', "10");
	add_option('mathilda_tweet_backlink', "1");
	add_option('mathilda_cron_lastrun', "0");
	}

	/* Update Process Version 0.8.1 */
	if($mathilda_previous_version==10) {
		update_option('mathilda_plugin_version', "11");
	}

	/* Update Process Version 0.9 */
	if($mathilda_previous_version==11) {
		update_option('mathilda_plugin_version', "12");
		add_option('mathilda_import_running',"0");
		add_option('mathilda_import_open',"0");
		add_option('mathilda_import_files',"0");
		add_option('mathilda_import_numberoffiles',"0");
		add_option('mathilda_import_interval', "60");
		add_option('mathilda_import_finish', "0");
		add_option('mathilda_import_subprocess_running', "0");
		add_option('mathilda_load_process_running',"0");
		update_option('mathilda_num_fetches', "1");
	}

	/* Update Process Version 0.10 */
	if($mathilda_previous_version==12) {
		update_option('mathilda_plugin_version', "13");
	}

	/* Update Process Version 0.11 */
	if($mathilda_previous_version==13) {
		update_option('mathilda_plugin_version', "14");
		add_option('mathilda_import_filesize_max',"409600");
	}

	/* Update Process Version 0.12 */
	if($mathilda_previous_version==14) {
		update_option('mathilda_plugin_version', "15");
		$cron_status='';
		$initial_load = get_option('mathilda_initial_load');
		if($initial_load==1) {
			$cron_status=1;
		} else {
			$cron_status=0;
		}
		add_option('mathilda_cron_status',$cron_status);
	}

}
add_action( 'plugins_loaded', 'mathilda_update' );

// mathilda_update_seven_aftercheck
// Description: Checks, if activities are required after the update to version 7
// Input: none
// Output: true (No Activities required) or false (Activites are required)

function mathilda_update_seven_aftercheck () {
    global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	$nd=$wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE mathilda_tweet_quote='ND'" );
    if($nd>0) {
        return false;
    } else {
        return true;
    }
}

function mathilda_update_eight_aftercheck () {
    global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	$truncates=$wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE mathilda_tweet_truncate='TRUE'" );
    if($truncates>0) {
        return false;
    } else {
        return true;
    }
}

?>