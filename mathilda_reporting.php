<?php

// Security
if (!defined('ABSPATH')) { exit; }

/*
Mathilda Reporting
*/

/* 
Number of Tweets 
*/

function mathilda_tweets_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
}

/* 
Number of Retweets 
*/

function mathilda_retweets_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE mathilda_tweet_retweet='TRUE'" );
}

/* 
Number of Replies 
*/

function mathilda_replies_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE mathilda_tweet_reply='TRUE'" );
}

/* 
Number of Quotes 
*/

function mathilda_quotes_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE mathilda_tweet_quote='TRUE'" );
}

/* 
Number of Images 
*/

function mathilda_images_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_media';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
}

/* 
Number of Mentions 
*/

function mathilda_mentions_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_mentions';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
}

/* 
Number of Hashtags 
*/

function mathilda_hashtags_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_hashtags';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
}

/* 
Number of Links 
*/

function mathilda_urls_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
}

/* 
Number of Selection 
*/

function mathilda_select_count() {
	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_tweets';
	$mathilda_select_condition=mathilda_select();
	return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $mathilda_select_condition" );
}

?>