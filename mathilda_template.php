<?php

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

/*
 *
 * Mathilda Template
 *
 */

// HTML Start
$mathilda_content_html.='<div id="mathilda-area">';

// Run Mathilda

if($alliswell==true) {
	
	// Mathilda Loop	
	$mathilda_content_html.= mathilda_loop($mathilda_show_page);
	
	/* Mathilda Bottom Navigation */
	$mathilda_content_html.= mathilda_create_menu($mathilda_pages_amount, $mathilda_show_page );
	
}
else {
	
	// Oh No!

	if ( is_user_logged_in() ) {
		$mathilda_content_html.='<p>Hello! I am Mathilda.<br/>Please run import or cron!</p>';
	}
	else {
		$mathilda_content_html.='<p>Hello! I am Mathilda.<br/>In a short while you find some nice tweets here.</p>';
	}

	

}

// HTML End
$mathilda_content_html.='</div>';

?>