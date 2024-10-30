<?php

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

function mathilda_tweet_filter ($tweet_cache, $num_tweets, $e) {

/* Building Fiter Array */

$tweet_filter_cache=array();

for($i=0; $i < $num_tweets; $i++) 
{
	if ($e=='L') {
	$tweet_filter_cache[]=array($tweet_cache[$i][0],
								$tweet_cache[$i][1],
								$tweet_cache[$i][2],
								$tweet_cache[$i][3],
								$tweet_cache[$i][4],
								$tweet_cache[$i][5],
								$tweet_cache[$i][6]
								);
	} elseif ($e=='E') {
	$tweet_filter_cache[]=array($tweet_cache[$i][0],
								$tweet_cache[$i][1],
								$tweet_cache[$i][2],
								$tweet_cache[$i][3],
								$tweet_cache[$i][4],
								$tweet_cache[$i][5],
								$tweet_cache[$i][6],
								$tweet_cache[$i][7],
								$tweet_cache[$i][8],
								$tweet_cache[$i][9],
								$tweet_cache[$i][10]
								);	
	}
}

return $tweet_filter_cache;

}

?>