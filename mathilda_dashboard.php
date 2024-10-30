<?php

// Security
if (!defined('ABSPATH')) { exit; }

/*
Mathilda Dashboard
*/

/* Mathilda @ Auf einen Blick */
 
function mathilda_glance_counter() {
	
	$mathilda_tweets_count=get_option('mathilda_tweets_count');
	$text='<li class="post-count"><a class="tweet-count" href="tools.php?page=mathilda-tools-menu">';
	$text.=$mathilda_tweets_count . ' Tweets</a</a></li>';
	echo $text;

}

add_filter( 'dashboard_glance_items', 'mathilda_glance_counter');

/*
Mathilda Dashboard Widget
*/

// Register the Mathilda Dashboard Widget
function register_mathilda_dashboard_widget_reporting() {
	wp_add_dashboard_widget(
		'mathilda_dashboard_widget_reporting',
		'Mathilda',
		'mathilda_dashboard_widget_reporting_display'
	);

}
add_action( 'wp_dashboard_setup', 'register_mathilda_dashboard_widget_reporting' );

// Mathilda Dashboard User Interface
function mathilda_dashboard_widget_reporting_display() {

	if(mathilda_select_count()==0) {
		echo '<p>Hello, I am Mathilda and I will show you some tweet statistics here, after you have loaded your tweets with me.</p>';
	} else {
		echo '<p>Hello, I am Mathilda and you have posted the following.</p>';
		echo '<table>';
		echo '<tr><td>' . mathilda_tweets_count() . '&nbsp;&nbsp;&nbsp;</td><td>Tweets</td></tr>';
		echo '<tr><td>' . mathilda_retweets_count() . '</td><td>Retweets</td></tr>';
		echo '<tr><td>' . mathilda_replies_count() . '</td><td>Replies</td></tr>';
		echo '<tr><td>' . mathilda_quotes_count() . '</td><td>Quotes</td></tr>';
		echo '<tr><td>' . mathilda_images_count() . '</td><td>Images</td></tr>';
		echo '<tr><td>' . mathilda_mentions_count() . '&nbsp;&nbsp;&nbsp;</td><td>Mentions</td></tr>';
		echo '<tr><td>' . mathilda_hashtags_count() . '&nbsp;&nbsp;&nbsp;</td><td>Hashtags</td></tr>';
		echo '<tr><td>' . mathilda_urls_count() . '</td><td>Links</td></tr>';
		echo '</table>';
		$lastjobrun=get_option('mathilda_cron_lastrun');
		if($lastjobrun !== '0') {
		echo '<p>Last Tweet Update @ WordPress:<br/>'. $lastjobrun .'</p>';
		}

	}

	$mathilda_import_running=get_option('mathilda_import_running');
	$mathilda_import_status=mathilda_get_import_status();
	if($mathilda_import_running == 1) {
		echo '<p>Mathilda Import Status: '. $mathilda_import_status .' %</p>';
	}

}

?>