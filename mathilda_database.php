<?php

/*
Security
*/

if (!defined('ABSPATH'))
{
	exit;
}

/*
Create Mathilda Tables
*/

function mathilda_tables_create () {

    global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	/*  Tweet Table */

	$table_name = $wpdb->prefix . "mathilda_tweets";
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
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

	/* Hashtag Table */

    $table_name = $wpdb->prefix . "mathilda_hashtags";
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	mathilda_hashtag_id bigint NOT NULL AUTO_INCREMENT,
	mathilda_hashtag_text varchar(140) NOT NULL,
	mathilda_hashtag_index_start varchar(3) NOT NULL,
	mathilda_hashtag_index_end varchar(3) NOT NULL,
	mathilda_hashtag_reference_tweet varchar(20) NOT NULL,
	PRIMARY KEY (mathilda_hashtag_id)
	) $charset_collate;";
	dbDelta( $sql );

	/* Mentions Table */

    $table_name = $wpdb->prefix . "mathilda_mentions";
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	mathilda_mention_id bigint NOT NULL AUTO_INCREMENT,
	mathilda_mention_useridstr varchar(20) NOT NULL,
	mathilda_mention_screenname varchar(15) NOT NULL,
	mathilda_mention_fullname varchar(20) NOT NULL,
	mathilda_mention_index_start varchar(3) NOT NULL,
	mathilda_mention_index_end varchar(3) NOT NULL,
	mathilda_mention_reference_tweet varchar(20) NOT NULL,
	PRIMARY KEY (mathilda_mention_id)
	) $charset_collate;";
	dbDelta( $sql );

	/* Media Table */

    $table_name = $wpdb->prefix . "mathilda_media";
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	mathilda_media_id bigint NOT NULL AUTO_INCREMENT,
	mathilda_media_mediaidstr varchar(20) NOT NULL,
	mathilda_media_mediaurl varchar(255) NOT NULL,
	mathilda_media_mediaurlhttps varchar(255) NOT NULL,
	mathilda_media_url varchar(23) NOT NULL,
	mathilda_media_displayurl varchar(50) NOT NULL,
	mathilda_media_extendedurl varchar(255) NOT NULL,
	mathilda_media_size_w smallint NOT NULL,
	mathilda_media_size_h smallint NOT NULL,
	mathilda_media_size_resize varchar(4) NOT NULL,
	mathilda_media_type varchar(5) NOT NULL,
	mathilda_media_index_start varchar(3) NOT NULL,
	mathilda_media_index_end varchar(3) NOT NULL,
	mathilda_media_tweetid varchar(20) NOT NULL,
	mathilda_media_filename varchar(20) NOT NULL,
	mathilda_media_loaded varchar(5) NOT NULL,
	PRIMARY KEY (mathilda_media_id)
	) $charset_collate;";
	dbDelta( $sql );

	/* URL Table */

    $table_name = $wpdb->prefix . "mathilda_urls";
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
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

	$database_version=1;
	update_option('mathilda_database_version', $database_version);

}

/*
Delete Mathilda Tables
*/

function mathilda_tables_delete() {

		global $wpdb;
		$wpdb->query( "DROP TABLE {$wpdb->prefix}mathilda_tweets" );
		$wpdb->query( "DROP TABLE {$wpdb->prefix}mathilda_hashtags" );
		$wpdb->query( "DROP TABLE {$wpdb->prefix}mathilda_mentions" );
		$wpdb->query( "DROP TABLE {$wpdb->prefix}mathilda_media" );
		$wpdb->query( "DROP TABLE {$wpdb->prefix}mathilda_urls" );

}

/*
Add Tweets
*/

function mathilda_add_tweets($id,$tweet,$date,$hashtags,$mentions,$media,$urls,$truncate,$reply,$retweet,$quote,$index_start,$index_end,$source) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'mathilda_tweets';

	$wpdb->insert(
		$table_name,
		array(
			'mathilda_tweet_date' => $date,
			'mathilda_tweet_content' => $tweet,
			'mathilda_tweet_twitterid' => $id,
			'mathilda_tweet_hashtags' => $hashtags,
			'mathilda_tweet_mentions' => $mentions,
			'mathilda_tweet_media' => $media,
			'mathilda_tweet_urls' => $urls,
			'mathilda_tweet_truncate' => $truncate,
			'mathilda_tweet_reply' => $reply,
			'mathilda_tweet_retweet' => $retweet,
			'mathilda_tweet_quote' => $quote,
			'mathilda_tweet_content_display_index_start' => $index_start,
			'mathilda_tweet_content_display_index_end' => $index_end,
			'mathilda_tweet_source' => $source)
		);
}

/*
Add Hastags
*/

function mathilda_add_hashtags($hashtag,$index_start,$index_end,$reference_tweet) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'mathilda_hashtags';

	$wpdb->insert(
		$table_name,
		array(
			'mathilda_hashtag_text' => $hashtag,
			'mathilda_hashtag_index_start' => $index_start,
			'mathilda_hashtag_index_end' => $index_end,
			'mathilda_hashtag_reference_tweet' => $reference_tweet)
		);
}

/*
Add Mentions
*/

function mathilda_add_mentions($useridstr,$screenname,$fullname,$index_start,$index_end,$reference_tweet) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'mathilda_mentions';

	$wpdb->insert(
		$table_name,
		array(
			'mathilda_mention_useridstr' => $useridstr,
			'mathilda_mention_screenname' => $screenname,
			'mathilda_mention_fullname' => $fullname,
			'mathilda_mention_index_start' => $index_start,
			'mathilda_mention_index_end' => $index_end,
			'mathilda_mention_reference_tweet' => $reference_tweet)
		);
}

/*
Add Media
*/

function mathilda_add_media($media_idstr, $media_mediaurl, $media_mediaurlhttps, $media_url,$media_displayurl, $media_extendedurl, $media_size_w, $media_size_h, $media_size_resize, $media_type, $index_start, $index_end, $reference_tweet, $filename, $loaded) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'mathilda_media';

	$wpdb->insert(
		$table_name,
		array(
			'mathilda_media_mediaidstr' => $media_idstr,
			'mathilda_media_mediaurl' => $media_mediaurl,
			'mathilda_media_mediaurlhttps' => $media_mediaurlhttps,
			'mathilda_media_url' => $media_url,
			'mathilda_media_displayurl' => $media_displayurl,
			'mathilda_media_extendedurl' => $media_extendedurl,
			'mathilda_media_size_w' => $media_size_w,
			'mathilda_media_size_h' => $media_size_h,
			'mathilda_media_size_resize' => $media_size_resize,
			'mathilda_media_type' => $media_type,
			'mathilda_media_index_start' => $index_start,
			'mathilda_media_index_end' => $index_end,
			'mathilda_media_tweetid' => $reference_tweet,
			'mathilda_media_filename' => $filename,
			'mathilda_media_loaded' => $loaded,
			)
		);
}

/*
Add URLs
*/

function mathilda_add_urls($url_tco,$url_extended,$url_display,$index_start,$index_end,$reference_tweet,$url_embed='OPEN') {

	global $wpdb;
	$table_name = $wpdb->prefix . 'mathilda_urls';

	$wpdb->insert(
		$table_name,
		array(
			'mathilda_url_tco' => $url_tco,
			'mathilda_url_extended' => $url_extended,
			'mathilda_url_display' => $url_display,
			'mathilda_url_index_start' => $index_start,
			'mathilda_url_index_end' => $index_end,
			'mathilda_url_reference_tweet' => $reference_tweet,
			'mathilda_url_embed' => $url_embed)
		);
}

/*
Mathilda Select 
*/

function mathilda_select() {

	// Begin
	$mathilda_select_condition="WHERE ";

	// Retweets
	$mathilda_select_condition.="mathilda_tweet_retweet = 'FALSE' ";

	// Replies
	if(get_option('mathilda_replies')==="0") {
		$mathilda_select_condition.="AND mathilda_tweet_reply = 'FALSE' ";
	}

	// Quotes
	if(get_option('mathilda_quotes')==="0") {
		$mathilda_select_condition.="AND mathilda_tweet_quote = 'FALSE' ";
	}

	return $mathilda_select_condition; 
}


/*
Read Tweets
*/

function mathilda_read_tweets($tweets_on_page, $mathilda_show_page) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';

	$mathilda_select_condition=mathilda_select();

	if($mathilda_show_page==1)
			{
			 /* return $wpdb->get_results( "SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls FROM (SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls FROM $table_name $mathilda_select_condition ORDER BY mathilda_tweet_date DESC) AS SOURCE ORDER BY mathilda_tweet_date DESC LIMIT $tweets_on_page", ARRAY_N);*/
			 return $wpdb->get_results( "SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls FROM $table_name $mathilda_select_condition ORDER BY mathilda_tweet_date DESC LIMIT $tweets_on_page", ARRAY_N);
			}
			
	else
			{
			/*$offset=$tweets_on_page*($mathilda_show_page-1);
			return $wpdb->get_results( "SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls FROM (SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls FROM $table_name $mathilda_select_condition ORDER BY mathilda_tweet_date DESC) AS SOURCE LIMIT $offset, $tweets_on_page", ARRAY_N);*/
			$offset=$tweets_on_page*($mathilda_show_page-1);
			return $wpdb->get_results( "SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls FROM $table_name $mathilda_select_condition ORDER BY mathilda_tweet_date DESC LIMIT $offset, $tweets_on_page", ARRAY_N);
			}
}

/*
Read Image
*/

function mathilda_read_image($tweetid) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_media';
	return $wpdb->get_results( "SELECT * FROM $table_name WHERE mathilda_media_tweetid= $tweetid ", ARRAY_N);
}

/*
Read Hashtag
*/

function mathilda_read_hashtag($tweetid) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_hashtags';
	return $wpdb->get_results( "SELECT * FROM $table_name WHERE mathilda_hashtag_reference_tweet=$tweetid", ARRAY_N);
}

/*
Read Mention
*/

function mathilda_read_mention($tweetid) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_mentions';
	return $wpdb->get_results( "SELECT * FROM $table_name WHERE mathilda_mention_reference_tweet=$tweetid", ARRAY_N);
}

/*
Read URL
*/

function mathilda_read_url($tweetid) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';
	return $wpdb->get_results( "SELECT * FROM $table_name WHERE mathilda_url_reference_tweet=$tweetid", ARRAY_N);
}

/*
Is Tweet Existing
*/

function mathilda_is_tweetid_existing($tweetid) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	$result=$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE mathilda_tweet_twitterid=$tweetid");

	return $result;
}

/*
Is Hashtag existing
*/

function mathilda_is_hashtag_existing($tweetid, $index_end) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_hashtags';
	$result=$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE mathilda_hashtag_reference_tweet=$tweetid AND mathilda_hashtag_index_end=$index_end");

	return $result;
}

/*
Is Mention Existing
*/

function mathilda_is_mention_existing($tweetid, $index_end) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_mentions';
	$result=$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE mathilda_mention_reference_tweet=$tweetid AND mathilda_mention_index_end=$index_end");

	return $result;
}

/*
Is URL Existing
*/

function mathilda_is_url_existing($tweetid, $index_end) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';
	$result=$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE mathilda_url_reference_tweet=$tweetid AND mathilda_url_index_end=$index_end");

	return $result;
}

/*
Is Media Existing
*/

function mathilda_is_media_existing($tweetid) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_media';
	$result=$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE mathilda_media_tweetid=$tweetid");

	return $result;
}

/*
Is Image File Existing
*/

function mathilda_is_image_file_existing($imagefile) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_media';
	$result=$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE mathilda_media_filename='$imagefile'");

	return $result;
}

/*
Latest Tweet
*/

function mathilda_latest_tweet() {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	return $wpdb->get_var( "SELECT MAX(CONVERT(`mathilda_tweet_twitterid`, UNSIGNED)) FROM $table_name" );
}

/*
Mathilda Reset Data
*/

function mathilda_reset_data() {

	$secure_reset_import=get_option('mathilda_import_subprocess_running');
	$secure_reset_load=get_option('mathilda_load_process_running');

	if($secure_reset_import==0 AND $secure_reset_load==0) {

		update_option('mathilda_initial_load',0);
		update_option('mathilda_import_running',"0");
		update_option('mathilda_latest_tweet',"");
		update_option('mathilda_tweets_count',0);
		update_option('mathilda_import', "0");
		update_option('mathilda_highest_imported_tweet','');
		update_option('mathilda_select_amount','0');
		update_option('mathilda_cron_lastrun', "0");
		update_option('mathilda_import_open',"0");
		update_option('mathilda_import_files',"0");
		update_option('mathilda_import_numberoffiles',"0");
		update_option('mathilda_import_subprocess_running',0);
		update_option('mathilda_load_process_running',0);
		update_option('mathilda_import_interval', "86400");
		update_option('mathilda_cron_status',"0");
 
		global $wpdb;
		$table_name=$wpdb->prefix . 'mathilda_tweets';
		$wpdb->get_var( "DELETE FROM $table_name" );
		$table_name=$wpdb->prefix . 'mathilda_hashtags';
		$wpdb->get_var( "DELETE FROM $table_name" );
		$table_name=$wpdb->prefix . 'mathilda_mentions';
		$wpdb->get_var( "DELETE FROM $table_name" );
		$table_name=$wpdb->prefix . 'mathilda_media';
		$wpdb->get_var( "DELETE FROM $table_name" );
		$table_name=$wpdb->prefix . 'mathilda_urls';
		$wpdb->get_var( "DELETE FROM $table_name" );

		$message="Mathilda Reset is done.";
		return $message;

	}
	elseif ($secure_reset_import==1) {
		return $message="Oh No! Culture Clash! Please wait a second and reset Mathilda again!";
	}
	elseif ($secure_reset_load==1) {
		return $message="Oh No! Culture Clash! Please wait a second and reset Mathilda again!";
	}

}

/*
Reset Embed
*/

// Resets the Embeds
// Input: None
// Output: Message

function mathilda_reset_embed() {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';

	$reset_value='OPEN';

	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_url_embed= %s",$reset_value));

	$message="Embed Reset is done.";
	return $message;

}

/*
Get Embed
*/

// Returns the Embed Value
// Input: Mathilda URL-ID
// Output: Embed Value

function mathilda_get_embed($mathilda_url_id) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';

	$result=$wpdb->get_var("SELECT mathilda_url_embed FROM {$table_name} WHERE mathilda_url_id=$mathilda_url_id");

	return $result;

}

/*
Add Embed 
*/

// Saves the Embed Value
// Input: Embed Value
// Output: None

function mathilda_add_embed($mathilda_embed_value,$mathilda_url_id) {

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';

	$wpdb->query($wpdb->prepare("UPDATE {$table_name} SET mathilda_url_embed= %s WHERE mathilda_url_id=%d",$mathilda_embed_value, $mathilda_url_id));

}

/*
Update Cron LastRun Timestamp
*/

// Saves the time of the last cron run
// Input: None
// Output: None

function mathilda_update_cron_lastrun() {

	$crontime = current_time( 'mysql', $gmt = 0 );
	update_option('mathilda_cron_lastrun', $crontime);

}

?>