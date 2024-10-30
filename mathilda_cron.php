<?php

/* 
Security
*/

if (!defined('ABSPATH')) 
{  
	exit;
}

function mathilda_cron_script() {

	/*
	Runtime
	*/

	set_time_limit(900);
		
	/*
	Headline
	*/

	echo '
	<h1 class="mathilda_tools_headline">Load Tweets</h1>
	<p class="mathilda_tools_description">Output</p>';

	/*
	Secure Execution
	*/

	$secure_execution=get_option('mathilda_load_process_running');
	if($secure_execution==1) {
		echo '<p><strong>I cannot!</strong></p>';
		echo '<p>Loading Process is already running.</p>';
		echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';
		return;
	}
	else {
		update_option('mathilda_load_process_running',1);
	}

	/* 
	Authorization Data @ Twitter API
	*/

	$user = get_option('mathilda_twitter_user');
	$authorization = array(
	'oauth_access_token' => get_option('mathilda_oauth_access_token'),
	'oauth_access_token_secret' => get_option('mathilda_oauth_access_token_secret'),
	'consumer_key' => get_option('mathilda_consumer_key'),
	'consumer_secret' => get_option('mathilda_consumer_secret')
	);

	/* 
	Processing Data 
	*/

	$num_tweets=0;
	$num_hashtags=0;
	$num_mentions=0;
	$num_media=0;
	$num_urls=0;

	$tweet_cache=array();
	$hashtag_cache=array();
	$mention_cache=array();
	$media_cache=array();
	$url_cache=array();

	$last_tweet;
	$first_tweet_in_the_next_call;
	$update;		
	$json_message=true;

	$twitter_api_data_dirwithpath = mathilda_get_apidata_directory();
	$mathilda_images_dirwithpath = mathilda_get_image_directory();

	/*
	Validation
	*/

	if(mathilda_valdidate_twitterauthdata($authorization)==false)
	{
	echo "<p>Error: Twitter Authentification Data is missing! Please check the Mathilda Options!</p>";
	echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'options-general.php?page=mathilda-options">Go to Mathilda Options</a></p>';
	update_option('mathilda_load_process_running',0);
	return;
	}

	if(mathilda_valdidate_twitteruser($user)==false)
	{
	echo "<p>Error: Twitter User is missing! Please check the Mathilda Options!</p>";
	echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'options-general.php?page=mathilda-options">Go to Mathilda Options</a></p>';
	update_option('mathilda_load_process_running',0);
	return;
	}

	/* 
	Twitter Interface 
	*/

	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	$requestMethod = "GET";

	/* 
	Fetch Options 
	*/

	$initial_load = get_option('mathilda_initial_load');
	$mathilda_import=get_option('mathilda_import');
	$highest_imported_tweet = get_option('mathilda_highest_imported_tweet');

	if($initial_load == 0)
	{
	$fetches = get_option('mathilda_num_fetches');
	$count = get_option('mathilda_num_tweets_fetch_call');
	$update=false;
	}
	else
	{
	$fetches=1;
	$latest_tweet = get_option('mathilda_latest_tweet');
	$update=true;
	}

	/* 
	Fetch Loop
	*/

	$i = 0;
	while ($i < $fetches) {

		if ($i==0) {
				if ($update==true) 
				{
				// Regular Load
				$getfield = "?screen_name=$user&since_id=$latest_tweet";
				}
				else
				{
				// Initial Load
				$getfield = "?screen_name=$user&count=$count";
				}
		} 
		else 
		{
		// 2-n Call @ Initial Load
		$getfield = "?screen_name=$user&count=$count&max_id=$first_tweet_in_the_next_call";
		}

		/* Call Twitter API */

		$getfield .= "&tweet_mode=extended"; 
		$twitter = new TwitterAPIExchange($authorization);
		$json_output=$twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest();
		$string = json_decode($json_output,$assoc = TRUE);
		if(isset($string['errors'])) {
		if($string['errors'][0]['message'] != "") {echo "<p><strong>Sorry, there was a problem.</strong></p><p>Twitter returned the following error message:</p><p><em>".$string['errors'][0]['message']."</em></p>";update_option('mathilda_load_process_running',0);return;}
		}

		/* Save JSON */

		if(isset($string[0]['id_str']))
		{
			if ($json_message==true)
			{
			echo '<div>';
				if($initial_load==0) {
					echo '<p><strong>Response from Twitter API</strong></p>';
					echo '<p>Tweets are available.<br/>API Response will be archived.</p>';
				} else {
					echo '<p><strong>Response from Twitter API</strong></p>';
					echo '<p>New Tweets are available.<br/>API Response will be archived.</p>';	
				}
			echo '</div>';
			$json_message=false;	
			}
			$time_now=date("YmdHis");
			$fp = fopen($twitter_api_data_dirwithpath . "/api_output_" . $time_now . '_' . ($i+1) . '.json', "w");
			fwrite($fp, $json_output);
			fclose($fp);
		}
		else {
			if ($i==0) {
			echo '<div>';
			echo '<p><strong>Response from Twitter API</strong></p>';
			echo '<p>New Tweets are not available.<br/>API Response will not be archived.</p>';
			echo '<p><em>Loading is stopped.</em></p></div>';
			echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';
			mathilda_update_cron_lastrun();
			update_option('mathilda_load_process_running',0);
			return;
			}
			else
			{
				break;
			}
		}

		/* Tweet Loop */

		foreach($string as $items)
			{
				
				/* @Initial Load & Import = True : Cancel - if Gap is filled */
				
				if($initial_load==0) {
					if($mathilda_import==1) {
						$current_tweet=$items['id_str'];
						if($current_tweet==$highest_imported_tweet) {
							break 2;
						}	
					}
				}
				
				/* Hashtags */
				
				$hashtag_text=false;
				$hashtag_index_s=false;
				$hashtag_index_e=false;
				$hashtags_yes_or_no="FALSE";
				
				if(array_key_exists('0', $items['entities']['hashtags'])) 
				{
					foreach($items['entities']['hashtags'] as $hashtags)
					{	
					$hashtag_text=$hashtags['text'];
					$hashtag_index_s=$hashtags['indices'][0];
					$hashtag_index_e=$hashtags['indices'][1];
					$hashtags_yes_or_no="TRUE";
					$hashtag_cache[]=array($hashtag_text,$hashtag_index_s,$hashtag_index_e,$items['id_str']);
					$num_hashtags=$num_hashtags+1;
					}
				}
				
				/* Mentions */
				
				$mention_useridstr=false;
				$mention_screenname=false;
				$mention_fullname=false;
				$mention_index_start=false;
				$mention_index_end=false;
				$mentions_yes_or_no="FALSE";
				
				if(array_key_exists('0', $items['entities']['user_mentions'])) 
				{
					foreach($items['entities']['user_mentions'] as $mentions)
					{	
					$mention_useridstr=$mentions['id_str'];
					$mention_screenname=$mentions['screen_name'];
					$mention_fullname=$mentions['name'];
					$mention_index_start=$mentions['indices'][0];
					$mention_index_end=$mentions['indices'][1];
					$mentions_yes_or_no="TRUE";
					$mention_cache[]=array($mention_useridstr,$mention_screenname,$mention_fullname,$mention_index_start,$mention_index_end,$items['id_str']);
					$num_mentions=$num_mentions+1;
					}
				}
				
				/* URLS */
				
				$url_tco=false;
				$url_extended=false;
				$url_display=false;
				$url_index_start=false;
				$url_index_end=false;
				$urls_yes_or_no="FALSE";
				
				if(array_key_exists('0', $items['entities']['urls'])) 
				{
					foreach($items['entities']['urls'] as $urls)
					{	
					$url_tco=$urls['url'];
					$url_extended=$urls['expanded_url'];
					$url_display=$urls['display_url'];
					$url_index_start=$urls['indices'][0];
					$url_index_end=$urls['indices'][1];
					$urls_yes_or_no="TRUE";
					$url_cache[]=array($url_tco,$url_extended,$url_display,$url_index_start,$url_index_end,$items['id_str']);
					$num_urls=$num_urls+1;
					}
				}
				
				/* Media */
				
				$media_idstr=false;
				$media_mediaurl=false;
				$media_mediaurlhttps=false;
				$media_url=false;
				$media_displayurl=false;
				$media_extendedurl=false;
				$media_size_w=false;
				$media_size_h=false;
				$media_size_resize=false;
				$media_type=false;
				$index_start=false;
				$index_end=false;
				$media_yes_or_no="FALSE";
				
				if(isset($items['extended_entities']['media'])) 
				{
					foreach($items['extended_entities']['media'] as $images)
					{	

					/* Image Meta Data */
					
					$media_idstr=$images['id_str'];
					$media_mediaurl=$images['media_url'];
					$media_mediaurlhttps=$images['media_url_https'];
					$media_url=$images['url'];
					$media_displayurl=$images['display_url'];
					$media_extendedurl=$images['expanded_url'];
					$media_size_w=$images['sizes']['large']['w'];
					$media_size_h=$images['sizes']['large']['h'];
					$media_size_resize=$images['sizes']['large']['resize'];
					$media_type=$images['type'];
					$index_start=$images['indices'][0];
					$index_end=$images['indices'][1];
					
					// Load Image File @ Twitter
			
					$image_url_large=$media_mediaurl.':large';
					$content = file_get_contents($image_url_large);
					
					// Store Image File @ WordPress
					
					$image_filename = substr(strrchr($media_mediaurl, "/"), 1);
					$mathilda_images_target = $mathilda_images_dirwithpath . $image_filename;
					
					if (!file_exists($mathilda_images_target))
					{
						$image_filename = substr(strrchr($media_mediaurl, "/"), 1);
						$mathilda_images_target = $mathilda_images_dirwithpath . $image_filename;
						$fp = fopen($mathilda_images_target, "w");
						fwrite($fp, $content);
						fclose($fp);
					}
					$loaded='TRUE';		
					
					// Add Entry @ Media Cache Array
					
					$media_cache[]=array($media_idstr,$media_mediaurl,$media_mediaurlhttps,$media_url,$media_displayurl,$media_extendedurl, $media_size_w, $media_size_h, $media_size_resize,$media_type, $index_start, $index_end,$items['id_str'],$image_filename,$loaded);
					$media_yes_or_no="TRUE";
					$num_media=$num_media+1;
					}
				}
				
				/* Tweets */
				
				$tweet_truncate="FALSE";
				$tweet_reply="FALSE";
				$tweet_retweet="FALSE";
				$tweet_quote="FALSE";

				if(isset($items['retweeted_status'])) {
				$tweet_retweet="TRUE";
				}
				if($items['truncated']=='true') {
				$tweet_truncate="TRUE";
				}
				if($items['in_reply_to_status_id']!=null) {
				$tweet_reply="TRUE";
				}
				if(isset($items['quoted_status_id'])) {
				$tweet_quote="TRUE";
				}

				$tweet_index_start=$items['display_text_range'][0];
				$tweet_index_end=$items['display_text_range'][1];

				$source='';
				if($initial_load == 0) {
					$source='INITIAL';
				} else {
					$source='REGULAR';
				}

				$tweet_cache[]=array($num_tweets,
									$items['id_str'],
									$items['full_text'],
									$items['created_at'],
									$hashtags_yes_or_no,
									$mentions_yes_or_no,
									$media_yes_or_no,
									$urls_yes_or_no,
									$tweet_truncate,
									$tweet_reply,
									$tweet_retweet,
									$tweet_quote,
									$tweet_index_start,
									$tweet_index_end,
									$source
									);
									
				/* Prepare Next Tweet Loop */

				$num_tweets=$num_tweets+1;
				$last_tweet=$items['id_str'];
				
			}	// Tweet Loop
		
		/* Prepare Next Fetch */

		$first_tweet_in_the_next_call=($last_tweet-1);
		$i=$i+1;	

	}   // Fetch Loop

	/*
	Import 
	*/

	/* 
	Tweets 
	*/

	for($i=0; $i < $num_tweets; $i++) 
	{

			/* Convert Date */
			
			$day=substr($tweet_cache[$i][3], 8, 2);
			$year=substr($tweet_cache[$i][3], 26, 4);
			$hours=substr($tweet_cache[$i][3], 11, 2);
			$minutes=substr($tweet_cache[$i][3], 14, 2);
			$seconds=substr($tweet_cache[$i][3], 17, 2);
			$month=substr($tweet_cache[$i][3], 4, 3);
			
			if( strcasecmp ( $month , "Jan" ) == 0) { $m="01"; } ;
			if( strcasecmp ( $month , "Feb" ) == 0) { $m="02"; } ;
			if( strcasecmp ( $month , "Mar" ) == 0) { $m="03"; } ;
			if( strcasecmp ( $month , "Apr" ) == 0) { $m="04"; } ;
			if( strcasecmp ( $month , "May" ) == 0) { $m="05"; } ;
			if( strcasecmp ( $month , "Jun" ) == 0) { $m="06"; } ;
			if( strcasecmp ( $month , "Jul" ) == 0) { $m="07"; } ;
			if( strcasecmp ( $month , "Aug" ) == 0) { $m="08"; } ;
			if( strcasecmp ( $month , "Sep" ) == 0) { $m="09"; } ;
			if( strcasecmp ( $month , "Oct" ) == 0) { $m="10"; } ;
			if( strcasecmp ( $month , "Nov" ) == 0) { $m="11"; } ;
			if( strcasecmp ( $month , "Dec" ) == 0) { $m="12"; } ;
			
			$dateconverted=$year . $m . $day . $hours . $minutes . $seconds;
			
			/* Save Tweet @ MySQL */
			
			mathilda_add_tweets($tweet_cache[$i][1],$tweet_cache[$i][2],$dateconverted,$tweet_cache[$i][4],$tweet_cache[$i][5], $tweet_cache[$i][6], $tweet_cache[$i][7], $tweet_cache[$i][8], $tweet_cache[$i][9], $tweet_cache[$i][10], $tweet_cache[$i][11],$tweet_cache[$i][12], $tweet_cache[$i][13], $tweet_cache[$i][14]);
			if ($i==0) { echo '<p><strong>Tweet Import</strong></p>'; }
			echo ($i+1) . ': Tweet ' . $tweet_cache[$i][1] . ' added.<br/>';
	}
		
	/* Hashtags */

	if($num_hashtags>0)
	{	
			for($i=0; $i < $num_hashtags; $i++) 
			{
			mathilda_add_hashtags($hashtag_cache[$i][0],$hashtag_cache[$i][1],$hashtag_cache[$i][2],$hashtag_cache[$i][3]);
			if ($i==0) { echo '<p><strong>Hashtag Import</strong></p>'; }
			echo ($i+1) . ': Hashtag ' . $hashtag_cache[$i][0] . ' added.<br/>';
			}
	}
		
	/* Mentions */

	if($num_mentions>0)
	{	
			for($i=0; $i < $num_mentions; $i++) 
			{
			mathilda_add_mentions($mention_cache[$i][0],$mention_cache[$i][1],$mention_cache[$i][2],$mention_cache[$i][3],$mention_cache[$i][4],$mention_cache[$i][5]);
			if ($i==0) { echo '<p><strong>Mention Import</strong></p>'; }
			echo ($i+1) . ': Mention ' . $mention_cache[$i][1] . ' added.<br/>';
			}
	}
		
	/* Media */

	if($num_media>0)
	{	
			for($i=0; $i < $num_media; $i++) 
			{
			mathilda_add_media($media_cache[$i][0],$media_cache[$i][1],$media_cache[$i][2],$media_cache[$i][3],$media_cache[$i][4],$media_cache[$i][5],$media_cache[$i][6],$media_cache[$i][7],$media_cache[$i][8],$media_cache[$i][9],$media_cache[$i][10],$media_cache[$i][11],$media_cache[$i][12],$media_cache[$i][13],$media_cache[$i][14]);
			if ($i==0) { echo '<p><strong>Media Import</strong></p>'; }
			echo ($i+1) . ': Media ' . $media_cache[$i][1] . ' added.<br/>';
			}
	}
		
	/* URLs */

	if($num_urls>0)
	{	
			for($i=0; $i < $num_urls; $i++) 
			{
			mathilda_add_urls($url_cache[$i][0],$url_cache[$i][1],$url_cache[$i][2],$url_cache[$i][3],$url_cache[$i][4],$url_cache[$i][5]);
			if ($i==0) { echo '<p><strong>URL Import</strong></p>'; }
			echo ($i+1) . ': URL ' . $url_cache[$i][1] . ' added.<br/>';
			}
}

/* 
Update Meta 
*/

if($initial_load == 0) { 
	update_option('mathilda_initial_load', 1); 
	update_option('mathilda_cron_status', 1); 
}
$latest_tweet=mathilda_latest_tweet();
$number_of_tweets=mathilda_tweets_count();
$number_of_select=mathilda_select_count();
update_option('mathilda_latest_tweet', $latest_tweet);
update_option('mathilda_tweets_count', $number_of_tweets);
update_option('mathilda_select_amount', $number_of_select);
update_option('mathilda_load_process_running',0);
mathilda_update_cron_lastrun();

/*
Close 
*/

echo '<p><strong>Finish!</strong></p>';
echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';

/* Fire: Tweets are updated */

mathilda_tweets_updated_fire();

}

?>