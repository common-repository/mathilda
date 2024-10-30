<?php

// Security
if (!defined('ABSPATH')) { exit; }

/*
Mathilda Hooks
*/

function mathilda_tweets_updated_fire() {
	do_action( 'mathilda_tweets_updated' );
}

function mathilda_custom_tweets_updated_action(){
	// copy this function (including add_action below and paste your custom code here
}
add_action( 'mathilda_tweets_updated', 'mathilda_custom_tweets_updated_action' );

?>