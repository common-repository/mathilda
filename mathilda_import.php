<?php

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

/* 
Import Process
*/

function mathilda_import_tool() {

	/*
	Variables
	*/

	$import_status=get_option('mathilda_import_running');
	$import_already_done=get_option('mathilda_import');
	$import_confirmation=false;

	/*
	Retrieve Confirmation
	*/

	if(isset($_GET['import_confirm_yes'])) {
		if($_GET['import_confirm_yes']=='true')
		{
		$import_confirmation=true;
		}	
	}

	/* 
	Headline Output 
	*/

	echo '<h1 class="mathilda_tools_headline">Import Archive</h1>';

	if($import_status==0) {

		/*
		Directories
		*/

		$twitter_import_path = mathilda_get_import_directory();

		/*
		Variables & Arrays
		*/

		$list_import_files_draft=array();
		$number_of_files_draft=0;
		$list_import_files=array();
		$number_of_files=0;
		$max_file_size=0;
		$filesize_max_threshold=get_option('mathilda_import_filesize_max');
		$filesize_max_string=$filesize_max_threshold;
		$filesize_max_string=$filesize_max_string/1024;

		/*
		Read Files @ Import Directory
		*/ 

		if ( is_dir ( $twitter_import_path ))
		{
			if ( $handle = opendir($twitter_import_path) )
			{
				while (($file = readdir($handle)) !== false)
				{
					$list_import_files_draft[]=array($file);
					$number_of_files_draft++;
				}
			closedir($handle);
			}
		}
		else {
			echo '<p>Error: Import Folder does not exist. Please create folder "mathilda-import" in your uploads directory. </p>';
			return;
		}

		/*
		Removing System Files 
		*/

		for($i=0; $i<$number_of_files_draft; $i++)
		{
				$unknown_pos=strpos($list_import_files_draft[$i][0], ".");
				if($unknown_pos === 0)
						{
						unset($list_import_files_draft[$i]);
						}
		}

		/* 
		Create Valid File List 
		*/

		for($i=0; $i<$number_of_files_draft; $i++)
		{

				if (isset($list_import_files_draft[$i]) ) {
				
				$list_import_files[$number_of_files][0]=$list_import_files_draft[$i][0];
				$number_of_files++;
				}
			
		}

		array_multisort($list_import_files);

		/*
		Get Max File Size
		*/

		for($i=0; $i<$number_of_files; $i++)
		{

			$filename_with_path=$twitter_import_path . '/' . $list_import_files[$i][0]; 
			$filesize_this=filesize($filename_with_path);

			if($filesize_this>$max_file_size) {
				$max_file_size=$filesize_this;
			}
			
		}

		/*
		Check Number of Files 
		*/
	
		if ($number_of_files==0) {

			echo '<p><strong>Manual</strong></p>';
			echo '<p>With this import script you can load your complete tweet history into WordPress.<br/>';
			echo 'Please follow the instructions below.</p>';
			echo '<p><strong>Required Steps</strong></p>';
			echo '<p>1. Download your tweet archive from Twitter (Profile/Settings).<br/>';
			echo '2. <a href="https://www.unmus.de/wp-content/uploads/Mathilda-JSON-File-Split-EN.pdf" target="_blank">Split the file</a> data/tweets.js into smaller files (<'.$filesize_max_string.' KB) with a local app.<br/>';
			echo '3. Upload all files to the folder wp-content/uploads/mathilda-import.</br>';
			echo '4. Run this import script again.</p>';
			echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';
			return;
			
		}

		if ($max_file_size>$filesize_max_threshold) {

			echo '<p><strong>Error</strong></p>';
			echo '<p>One or more files in the import folder are larger as '.$filesize_max_string.' KB.<br/>';
			echo 'Unfortunately the import process is limited to files not larger as 400 KB.<br/>';
			echo 'You must split the affected files in smaller files with a local app.<br/>';
			echo 'After that run this import script again.</p>';
			echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';
			return;
			
		}

		if ($number_of_files>0) {

				if($import_already_done==1 && $import_confirmation==false) {
					echo '<p class="mathilda_tools_description">';
					echo 'Your tweet archive was already imported.<br/>';
					echo 'Do you want to run the import procedure again?</p>';
					echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu&import=true&import_confirm_yes=true">Yes, go for it!</a>&nbsp;&nbsp;&nbsp;<a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Cancel</a></p>';
					return;
				}

				echo '<p><strong>Process</strong></p>';
				echo '<p>Import Cron is scheduled<br/>';
				echo 'The tweet history will be loaded in the background.<br/>';
				echo 'You can close the window and work with WordPress as normal.<br/>';
				echo 'If the import process has finished, you get a notification at the dashboard.<br/>';
				echo '<p>Estimate to finish: '.$number_of_files.' Minutes</p>';
				echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu&import=true">Show Status</a>&nbsp;&nbsp;&nbsp;<a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';

		}

		/* 
		Status Flag 
		*/

		$timestamp = wp_next_scheduled( 'mathilda_import_schedule' );
		wp_unschedule_event($timestamp, 'mathilda_import_schedule' );
		
		update_option('mathilda_import_interval',60);
		update_option('mathilda_import_open',$number_of_files);
		update_option('mathilda_import_numberoffiles',$number_of_files);
		update_option('mathilda_import_files',$list_import_files);
		update_option('mathilda_import_running', 1);

	}
	else {

		$mathilda_import_numberoffiles=get_option('mathilda_import_numberoffiles');
		$mathilda_import_open=get_option('mathilda_import_open');
		$mathilda_import_status=mathilda_get_import_status();
		$mathilda_import_done=mathilda_get_import_files_done();

		echo '<p><strong>Status Progress</strong></p>';
		echo '<p>Number of Files: '.$mathilda_import_numberoffiles.'<br/>';
		echo 'Files open: '.$mathilda_import_open.'<br/>';
		echo 'Files done: '.$mathilda_import_done.'</p>';
		echo '<p>Status: '.$mathilda_import_status .' %</p>';
		echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu&import=true">Update Status</a>&nbsp;&nbsp;&nbsp;<a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu&importbreak=true">Abort</a>&nbsp;&nbsp;&nbsp;<a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';

	}

}

function mathilda_import_break() {

	update_option('mathilda_import_running',0);
	update_option('mathilda_import_interval',86400);
	update_option('mathilda_import_open','0');
	update_option('mathilda_import_files','0');
	update_option('mathilda_import_numberoffiles','0');

	echo '<h1 class="mathilda_tools_headline">Import Archive</h1>';
	echo '<p><strong>Status</strong></p>';
	echo '<p>Aborted.</p>';
	echo '<p>&nbsp;<br/><a class="button" href="'.admin_url().'tools.php?page=mathilda-tools-menu">Close</a></p>';
}

function mathilda_import_process() {

	$secure_execution=get_option('mathilda_import_subprocess_running');
	if($secure_execution==1) {
		return;
	}
	else {
		update_option('mathilda_import_subprocess_running',1);
	}

	$number_of_files=get_option('mathilda_import_open');
	$list_import_files=get_option('mathilda_import_files');

	/*
	Magic
	*/

	mathilda_import_file( $list_import_files[$number_of_files-1][0] );

	/* 
	Update Meta 
	*/

	$latest_tweet=mathilda_latest_tweet();
	$number_of_tweets=mathilda_tweets_count();
	$number_of_select=mathilda_select_count();
	
	update_option('mathilda_latest_tweet', $latest_tweet);
	update_option('mathilda_tweets_count', $number_of_tweets);
	update_option('mathilda_select_amount', $number_of_select);

	$json_import=1;
	update_option('mathilda_import', $json_import);

	/*
	Update Import Status
	*/

	unset($list_import_files[$number_of_files-1]);
	$number_of_files=$number_of_files-1;

	update_option('mathilda_import_open',$number_of_files);
	update_option('mathilda_import_files',$list_import_files);
	update_option('mathilda_import_subprocess_running',0);

	if(empty($list_import_files)) {
		
		update_option('mathilda_import_running',0);
		update_option('mathilda_import_finish',1);
		update_option('mathilda_import_interval',86400);

		echo '<p>***</p>';
		echo '<p>Finished!</p>';

		/* Fire: Tweets are updated */

		mathilda_tweets_updated_fire();
	}

} 

function mathilda_import_file( $file ) {

	/*
	Runtime
	*/

	set_time_limit(900);

	/*
	Echo
	*/
	echo '<h2>File Select</h2>'; 
	echo '<p>'.$file . '</p>';

	/*
	Directories
	*/

	$mathilda_import_path = mathilda_get_import_directory();
	$mathilda_images_path = mathilda_get_image_directory();

	/* 
	Load Data 
	*/

	$filename_with_path=$mathilda_import_path . '/' . $file; 
	$file_content = file_get_contents($filename_with_path);
	$import_content=$file_content;

	/*
	Remove JSON Twitter Präfix
	*/

	$upos=strpos($import_content, "[");
	$import_content=substr_replace ( $import_content , '' , 0, $upos );

	/* 
	Counter
	*/

	$num_tweets=0;
	$num_hashtags=0;
	$num_mentions=0;
	$num_media=0;
	$num_urls=0;
	$highest_imported_tweet=0;

	/* 
	Arrays
	*/

	$tweet_cache=array();
	$hashtag_cache=array();
	$mention_cache=array();
	$media_cache=array();
	$url_cache=array();

	/* 
	Decode JSON
	*/

	$json_content=$import_content;
	$string = json_decode($json_content,$assoc = TRUE);

	/* 
	Tweet Loop 
	*/

	foreach($string as $items)
	{
		
		/* Hashtags */
		
		$hashtag_text=false;
		$hashtag_index_s=false;
		$hashtag_index_e=false;
		$hashtags_yes_or_no="FALSE";
		
		if(array_key_exists('0', $items['tweet']['entities']['hashtags'])) 
		{
			foreach($items['tweet']['entities']['hashtags'] as $hashtags)
			{	
			$hashtag_text=$hashtags['text'];
			$hashtag_index_s=$hashtags['indices'][0];
			$hashtag_index_e=$hashtags['indices'][1];
			$hashtags_yes_or_no="TRUE";
			$hashtag_cache[]=array($hashtag_text,$hashtag_index_s,$hashtag_index_e,$items['tweet']['id_str']);
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
		
		if(array_key_exists('0', $items['tweet']['entities']['user_mentions'])) 
		{
			foreach($items['tweet']['entities']['user_mentions'] as $mentions)
			{	
			$mention_useridstr=$mentions['id_str'];
			$mention_screenname=$mentions['screen_name'];
			$mention_fullname=$mentions['name'];
			$mention_index_start=$mentions['indices'][0];
			$mention_index_end=$mentions['indices'][1];
			$mentions_yes_or_no="TRUE";
			$mention_cache[]=array($mention_useridstr,$mention_screenname,$mention_fullname,$mention_index_start,$mention_index_end,$items['tweet']['id_str']);
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
		
		if(array_key_exists('0', $items['tweet']['entities']['urls'])) 
		{
			foreach($items['tweet']['entities']['urls'] as $urls)
			{	
			$url_tco=$urls['url'];
			$url_extended=$urls['expanded_url'];
			$url_display=$urls['display_url'];
			$url_index_start=$urls['indices'][0];
			$url_index_end=$urls['indices'][1];
			$urls_yes_or_no="TRUE";
			$url_cache[]=array($url_tco,$url_extended,$url_display,$url_index_start,$url_index_end,$items['tweet']['id_str']);
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
		
		if(isset($items['tweet']['entities']['media'])) 
		{
			foreach($items['tweet']['entities']['media'] as $images)
			{	

			$media_idstr=$images['id_str'];
			$media_mediaurl=$images['media_url'];
			$media_mediaurlhttps=$images['media_url_https'];
			$media_url=$images['url'];
			$media_displayurl=$images['display_url'];
			$media_extendedurl=$images['expanded_url'];
			
			$media_size_w=$images['sizes']['large']['w'];
			$media_size_h=$images['sizes']['large']['h'];
			$media_size_resize=$images['sizes']['large']['resize'];
			$media_type='photo';
			$index_start=$images['indices'][0];
			$index_end=$images['indices'][1];
		
			// Get the file

			$image_url_large=$media_mediaurl.':large';
			$content = file_get_contents($image_url_large);
			
			// Store on filesystem
			
			$filename = substr(strrchr($media_mediaurl, "/"), 1);
			$mathilda_images_target = $mathilda_images_path . $filename;
			
			if (!file_exists($mathilda_images_target))
			{
			$fp = fopen($mathilda_images_target, "w");
			fwrite($fp, $content);
			fclose($fp);		
			}
			$loaded='TRUE';
			
			// Update Media Array
			
			$media_cache[]=array($media_idstr,$media_mediaurl,$media_mediaurlhttps,$media_url,$media_displayurl,$media_extendedurl, $media_size_w, $media_size_h, $media_size_resize,$media_type, $index_start, $index_end,$items['tweet']['id_str'],$filename,$loaded);
			$media_yes_or_no="TRUE";
			$num_media=$num_media+1;
			}
		}
	
		/* Tweets */
	
		$tweet_truncate="FALSE";
		$tweet_reply="FALSE";
		$tweet_retweet="FALSE";
		$tweet_quote="FALSE";

		// Identify Retweets
		$is_retweet = strpos($items['tweet']['full_text'], 'RT ');
		if($is_retweet===0) {
			$tweet_retweet="TRUE";
		}

		if(isset($items['tweet']['truncated'])) {
			if($items['tweet']['truncated']=='true') {
			$tweet_truncate="TRUE";
			}
		}
		if(isset($items['tweet']['in_reply_to_user_id'])) {
			if($items['tweet']['in_reply_to_user_id']!=null) {
			$tweet_reply="TRUE";
			}
		}
		// Special Query for Quotes from Import 
		$url_to_tweet = strpos($url_extended, 'twitter.com');
		if($url_to_tweet!==false) {
		$tweet_quote="TRUE";
		}
		
		$tweet_cache[]=array($num_tweets,
							$items['tweet']['id_str'],
							$items['tweet']['full_text'],
							$items['tweet']['created_at'],
							$hashtags_yes_or_no,
							$mentions_yes_or_no,
							$media_yes_or_no,
							$urls_yes_or_no,
							$tweet_truncate,
							$tweet_reply,
							$tweet_retweet,
							$tweet_quote
							);
								
		$num_tweets=$num_tweets+1;

	} // End Tweet Loop
	
	/* 
	Update Database 
	*/

	/* Tweets */

	for($i=0; $i < $num_tweets; $i++) 
	{

		// Convert Date

		$tweet_date=strtotime($tweet_cache[$i][3]);
		$tweet_cache[$i][3]=date('YmdHis', $tweet_date);
		
		// Update Tweets
		
		if ($i==0) { echo '<h2>Tweet Import</h2>'; }
		
		$tweet_existing=2;
		$tweet_existing=mathilda_is_tweetid_existing($tweet_cache[$i][1]);
		
		if($tweet_existing==1) {
				echo ($i+1) . ': Tweet ' . $tweet_cache[$i][1] . ' already exists.<br/>';
		}
		else {
				mathilda_add_tweets($tweet_cache[$i][1],$tweet_cache[$i][2],$tweet_cache[$i][3],$tweet_cache[$i][4],$tweet_cache[$i][5], $tweet_cache[$i][6], $tweet_cache[$i][7], $tweet_cache[$i][8], $tweet_cache[$i][9], $tweet_cache[$i][10], $tweet_cache[$i][11], "FALSE", "FALSE", "IMPORT");		
				echo ($i+1) . ': Tweet ' . $tweet_cache[$i][1] . ' imported.<br/>';
		}
		
		if($tweet_cache[$i][1]>$highest_imported_tweet) {
			$highest_imported_tweet=$tweet_cache[$i][1];
		}
	
	}

	/* Hashtags */

	if($num_hashtags>0)
	{	
		for($i=0; $i < $num_hashtags; $i++) 
		{
			if ($i==0) { echo '<h2>Hashtag Import</h2>'; }
			
			$hashtag_existing=2;
			$hashtag_existing=mathilda_is_hashtag_existing($hashtag_cache[$i][3], $hashtag_cache[$i][2]);
			
			if($hashtag_existing==1) {
				echo ($i+1) . ': Hashtag ' . $hashtag_cache[$i][0] . ' already exists.<br/>';
			}
			else {
				mathilda_add_hashtags($hashtag_cache[$i][0],$hashtag_cache[$i][1],$hashtag_cache[$i][2],$hashtag_cache[$i][3]);
				echo ($i+1) . ': Hashtag ' . $hashtag_cache[$i][0] . ' imported.<br/>';
			}
		}
	}

	/* Mentions */

	if($num_mentions>0)
	{	
		for($i=0; $i < $num_mentions; $i++) 
		{
			if ($i==0) { echo '<h2>Mention Import</h2>'; }
			
			$mention_existing=2;
			$mention_existing=mathilda_is_mention_existing($mention_cache[$i][5], $mention_cache[$i][4]);
			
			if($mention_existing==1) {
				echo ($i+1) . ': Mention ' . $mention_cache[$i][1] . ' already exists.<br/>';
			}
			else {
				mathilda_add_mentions($mention_cache[$i][0],$mention_cache[$i][1],$mention_cache[$i][2],$mention_cache[$i][3],$mention_cache[$i][4],$mention_cache[$i][5]);
				echo ($i+1) . ': Mention ' . $mention_cache[$i][1] . ' imported.<br/>';
			}
		}
	}

	/* Media */

	if($num_media>0)
	{	
		for($i=0; $i < $num_media; $i++) 
		{
			if ($i==0) { echo '<h2>Media Import</h2>'; }
			
			$media_existing=2;
			$media_existing=mathilda_is_image_file_existing($media_cache[$i][13]);
			
			if($media_existing==1) {
				echo ($i+1) . ': Media ' . $media_cache[$i][1] . ' already exists.<br/>';
			}
			else {
				mathilda_add_media($media_cache[$i][0],$media_cache[$i][1],$media_cache[$i][2],$media_cache[$i][3],$media_cache[$i][4],$media_cache[$i][5],$media_cache[$i][6],$media_cache[$i][7],$media_cache[$i][8],$media_cache[$i][9],$media_cache[$i][10],$media_cache[$i][11],$media_cache[$i][12],$media_cache[$i][13],$media_cache[$i][14]);
				echo ($i+1) . ': Media ' . $media_cache[$i][1] . ' imported.<br/>';
			}
		}
	}

	/* URLS */

	if($num_urls>0)
	{	
		for($i=0; $i < $num_urls; $i++) 
		{
			if ($i==0) { echo '<h2>URL Import</h2>'; }
			
			$url_existing=2;
			$url_existing=mathilda_is_url_existing($url_cache[$i][5], $url_cache[$i][4]);
			
			if($url_existing==1) {
				echo ($i+1) . ': Media ' . $media_cache[$i][1] . ' already exists.<br/>';
			}
			else {
				mathilda_add_urls($url_cache[$i][0],$url_cache[$i][1],$url_cache[$i][2],$url_cache[$i][3],$url_cache[$i][4],$url_cache[$i][5]);
				echo ($i+1) . ': URL ' . $url_cache[$i][1] . ' imported.<br/>';
			}
		}
	}

	update_option('mathilda_highest_imported_tweet', $highest_imported_tweet);

}

?>