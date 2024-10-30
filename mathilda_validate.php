<?php

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

/* Validate Twitter API Authentification Options */ 

function mathilda_valdidate_twitterauthdata ($oauthdata) {
    
    if( ($oauthdata['oauth_access_token']=='') OR ($oauthdata['oauth_access_token_secret']=='') OR ($oauthdata['consumer_key']=='') OR ($oauthdata['consumer_secret']=='') )
    {
        $returncode=false;
    }
    else {
        $returncode=true;
    }
    return $returncode;
}

/*  
Validate Twitter User
*/

function mathilda_valdidate_twitteruser ($twitteruser) {
    
    if($twitteruser=='')
    {
        $returncode=false;
    }
    else {
        $returncode=true;
    }
    return $returncode;
}

/* Validate Input Slug */

function mathilda_validate_slug ( $slug ) {

     if ($slug == '') {
        add_settings_error( 'mathilda-options', 'invalid-slug', 'Mathilda Slug is required. Field cannot be empty.' );
        $output=get_option( 'mathilda_slug' );
     }
     else {
        if(mathilda_is_pretty_permalink_enabled() ) {
            $slug=str_replace (' ', '-', $slug);
        }
        $output = $slug;
        update_option('mathilda_slug_is_changed',"1");
     }
     return $output;
} 

/* Validate Cron Period */

function mathilda_validate_cron_period ( $period ) {

     if ($period == '') {
        add_settings_error( 'mathilda-options', 'invalid-period', 'Cron Period is required. Field cannot be empty.' );
        $output=get_option( 'mathilda_cron_period' );
     }
     elseif ($period < 15) {
        add_settings_error( 'mathilda-options', 'invalid-period', 'Cron cannot run more often as every 15 minutes.' );
        $output=get_option( 'mathilda_cron_period' );
     }
     else {
        $period=$period*60;
        $output = $period;
     }
     return $output;
} 

/* Validate Input: Tweets on Page */

function mathilda_validate_tweetsonpage ( $tweetsonpage ) {
    
    $output = $tweetsonpage;
    
    if ( $tweetsonpage < 10 ) {
        $output=get_option( 'mathilda_tweets_on_page' );
        add_settings_error( 'mathilda-options', 'invalid-tweetsonpage', 'Less than 10 Tweets are not allowed for Tweets on Page.' );
    }
    if ( $tweetsonpage > 1000 ) {
        $output=get_option( 'mathilda_tweets_on_page' );
        add_settings_error( 'mathilda-options', 'invalid-tweetsonpage', 'More than 1000 Tweets are not allowed for Tweets on Page.' );
    }
  
    return $output;
} 

/* Validate Input: Replies */

function mathilda_validate_replies ( $replies ) {
    
    $output = $replies;

    if ( $replies == FALSE ) {
        $output='0';
    }
  
    return $output;
} 

/* Validate Input: Quotes */

function mathilda_validate_quotes ( $quotes ) {
    
    $output = $quotes;

    if ( $quotes == FALSE ) {
        $output='0';
    }
  
    return $output;
} 

/* Validate Input: Embed */

function mathilda_validate_embed ( $embed ) {
    
    $output = $embed;

    if ( $embed == FALSE ) {
        $output='0';
    }
  
    return $output;
} 

/* Validate Input: Tweet Backlink */

function mathilda_validate_tweet_backlink ( $backlink ) {
    
    $output = $backlink;

    if ( $backlink == FALSE ) {
        $output='0';
    }
  
    return $output;
} 

/* Format Input: Twitter Login */

function mathilda_format_twitterlogin ( $twitterlogin ) {
    
    $output = $twitterlogin; 
    $at_pos=strpos($twitterlogin, "@");
    
    if (!($twitterlogin=='')) {
        if ( !($at_pos === 0) ) {
        $output="@".$twitterlogin;
        }
     }
  
     return $output;
} 

/* Validate Input: Mathilda CSS */

function mathilda_validate_css ( $css ) {
    
    $output = $css;

    if ( $css == FALSE ) {
        $output='0';
    }
  
    return $output;
} 

?>