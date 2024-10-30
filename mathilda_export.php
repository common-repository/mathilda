<?php 

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

function mathilda_export_csv() {

$amount_of_tweets=mathilda_tweets_count();
if($amount_of_tweets==0) {
    return 'Error! No Tweets for Export available.';
}

/* 
File 
*/ 

$filename = 'tweets_export.csv';
$upload_dir = wp_upload_dir();
$upload_dir_path=$upload_dir['path'].'/mathilda-export/';

/* 
Load Data 
*/

global $wpdb;
$table_name=$wpdb->prefix . 'mathilda_tweets';

$tweet_cache=array();
$tweet_cache=$wpdb->get_results( "SELECT mathilda_tweet_date, mathilda_tweet_content, mathilda_tweet_twitterid,  mathilda_tweet_hashtags, mathilda_tweet_mentions, mathilda_tweet_media, mathilda_tweet_urls, mathilda_tweet_truncate, mathilda_tweet_reply, mathilda_tweet_retweet, mathilda_tweet_quote FROM $table_name ORDER BY mathilda_tweet_date DESC", ARRAY_N);	

$num_tweets=count($tweet_cache);

/* 
Filter
*/

$tweet_filter_cache=array();
$tweet_filter_cache=mathilda_tweet_filter($tweet_cache, $num_tweets, 'E');

/*
Manipulate
*/

for($i=0; $i < $num_tweets; $i++) 
{
        if(isset($tweet_filter_cache[$i][1]))
        {
        $tweet_filter_cache[$i][1] = str_replace("\n", '', $tweet_filter_cache[$i][1]); // remove new lines
        $tweet_filter_cache[$i][1] = str_replace("\r", '', $tweet_filter_cache[$i][1]); // remove carriage returns
        }
        
}

/* 
Create CSV 
*/

$csv_head = array("Date", "Tweet", "ID", "Hashtags", "Mentions", "Media", "URLs", "Truncate", "Reply", "Retweet", "Quote");
$csv_head_written=false;

$fp = fopen($upload_dir_path . $filename, "w");

foreach ($tweet_filter_cache as $fields) {
    
    if ($csv_head_written==false) {
        fputcsv($fp, $csv_head, '@', '"' );
        $csv_head_written=true;
    }
    
    if(isset($fields['0'])) {
        fputcsv($fp, $fields, '@', '"' );
    }
    
}

fclose($fp);

/* 
Return
*/

$output='Finish! You find the CSV export in the Mathilda export directory!';
return $output;

}

?>