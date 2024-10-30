<?php

// Security
if (!defined('ABSPATH')) { exit; }

/* 
Mathilda Options 
*/

function mathilda_options_menu() {
add_options_page('Mathilda', 'Mathilda', 'manage_options', 'mathilda-options', "mathilda_options_content");
}

add_action( 'admin_menu', 'mathilda_options_menu');

/*
Options Page
*/

function mathilda_options_content() {
	
	$mathilda_slug_changed=get_option('mathilda_slug_is_changed');
	if($mathilda_slug_changed==1)
	{flush_rewrite_rules();update_option('mathilda_slug_is_changed','0');}
	
	$number_of_select=mathilda_select_count();
	update_option('mathilda_select_amount', $number_of_select);

	echo '
	<div class="wrap">
	<h1>Options › Mathilda</h1>
	<p class="mathilda_settings">All Settings<br/>&nbsp;</p>
	
	<form method="post" action="options.php">';
	
	do_settings_sections( 'mathilda-options' );
	settings_fields( 'mathilda_settings' );
	submit_button();

	echo '</form></div><div class="clear"></div>';
}

/*
Fields
#*/

function mathilda_options_display_oauth_access_token()
{
	echo '<input class="regular-text" type="text" name="mathilda_oauth_access_token" id="mathilda_oauth_access_token" value="'. get_option('mathilda_oauth_access_token') .'"/>';
}

function mathilda_options_display_oauth_access_token_secret()
{
	echo '<input class="regular-text" type="text" name="mathilda_oauth_access_token_secret" id="mathilda_oauth_access_token_secret" value="'. get_option('mathilda_oauth_access_token_secret') .'"/>';
}

function mathilda_options_display_consumer_key()
{
	echo '<input class="regular-text" type="text" name="mathilda_consumer_key" id="mathilda_consumer_key" value="'. get_option('mathilda_consumer_key') .'"/>';
}

function mathilda_options_display_consumer_secret()
{
	echo '<input class="regular-text" type="text" name="mathilda_consumer_secret" id="mathilda_consumer_secret" value="'. get_option('mathilda_consumer_secret') .'"/>';
}

function mathilda_options_display_twitter_user()
{
	echo '<input type="text" name="mathilda_twitter_user" id="mathilda_twitter_user" value="'. get_option('mathilda_twitter_user') .'"/>';
}

function mathilda_options_display_slug()
{
	echo '<input type="text" name="mathilda_slug" id="mathilda_slug" value="'. get_option('mathilda_slug') .'"/>';
}

function mathilda_options_display_cron_period()
{
	$custom_cron_period=get_option('mathilda_cron_period');
	$custom_cron_period=$custom_cron_period/60;
	echo '<input type="text" name="mathilda_cron_period" id="mathilda_cron_period" value="'. $custom_cron_period .'"/>';
}

function mathilda_options_display_tweets_on_page()
{
	echo '<input type="text" name="mathilda_tweets_on_page" id="mathilda_tweets_on_page" value="'. get_option('mathilda_tweets_on_page') .'"/>';
}

function mathilda_options_display_show_replies()
{
	 
	echo '<input type="checkbox" name="mathilda_replies" value="1" ' .  checked(1, get_option('mathilda_replies'), false) . '/>'; 
}	

function mathilda_options_display_show_quotes()
{
	 
	echo '<input type="checkbox" name="mathilda_quotes" value="1" ' .  checked(1, get_option('mathilda_quotes'), false) . '/>'; 
}	

function mathilda_options_display_embed()
{
	 
	echo '<input type="checkbox" name="mathilda_embed" value="1" ' .  checked(1, get_option('mathilda_embed'), false) . '/>'; 
}	

function mathilda_options_display_tweet_backlink()
{
	 
	echo '<input type="checkbox" name="mathilda_tweet_backlink" value="1" ' .  checked(1, get_option('mathilda_tweet_backlink'), false) . '/>'; 
}	

function mathilda_options_display_navigation()
{
	echo '<input type="radio" id="mathilda_navigation_standard" name="mathilda_navigation" value="Standard" ' .  checked('Standard', get_option('mathilda_navigation'), false) . '/>'; 
	echo '<label for="mathilda_navigation_standard">Standard</label>';
	echo '<br/>&nbsp;<br/>';
	echo '<input type="radio" id="mathilda_navigation_numbering" name="mathilda_navigation" value="Numbering" ' .  checked('Numbering', get_option('mathilda_navigation'), false) . '/>'; 
	echo '<label for="mathilda_navigation_numbering">Numbering</label>';
	echo '<br/>&nbsp;<br/>';
	echo '<input type="radio" id="mathilda_navigation_numbering_limit" name="mathilda_navigation" value="Limited Numbering" ' .  checked('Limited Numbering', get_option('mathilda_navigation'), false) . '/>'; 
	echo '<label for="mathilda_navigation_numbering_limit">Limited Numbering</label>';
}	

function mathilda_options_display_hyperlink_rendering()
{
	echo '<input type="radio" id="mathilda_hyperling_rendering_short" name="mathilda_hyperlink_rendering" value="Shortlink" ' .  checked('Shortlink', get_option('mathilda_hyperlink_rendering'), false) . '/>'; 
	echo '<label for="mathilda_hyperling_rendering_short">Short Link</label>';
	echo '<br/>&nbsp;<br/>';
	echo '<input type="radio" id="mathilda_hyperling_rendering_long" name="mathilda_hyperlink_rendering" value="Longlink" ' .  checked('Longlink', get_option('mathilda_hyperlink_rendering'), false) . '/>'; 
	echo '<label for="mathilda_hyperling_rendering_long">Real Link</label>';
}	

function mathilda_options_display_mathilda_css()
{
	 
	echo '<input type="checkbox" name="mathilda_css" value="1" ' .  checked(1, get_option('mathilda_css'), false) . '/>'; 
}	

/* 
Sections
*/

function mathilda_options_display_twitterapi_description()
{ echo '<p>Following data is required to authenticate with the Twitter API</p>'; }

function mathilda_options_display_plugin_description()
{ echo '<p>Basic Settings</p>'; }

function mathilda_options_display_userinterface_description()
{ echo '<p>FrondEnd Settings</p>'; }

function mathilda_options_display_expert_description()
{ echo '<p>Expert Settings</p>'; }

/* 
Definitions
*/

// Twitter API Settings

function mathilda_options_twitterapi_display()
{
	
	add_settings_section("twitterapi_settings_section", "Twitter API", "mathilda_options_display_twitterapi_description", "mathilda-options");
	
	add_settings_field("mathilda_oauth_access_token", "OAUTH Access Token", "mathilda_options_display_oauth_access_token", "mathilda-options", "twitterapi_settings_section");
	add_settings_field("mathilda_oauth_access_secret", "OAUTH Access Token Secret", "mathilda_options_display_oauth_access_token_secret", "mathilda-options", "twitterapi_settings_section");
	add_settings_field("mathilda_consumer_key", "Consumer Key", "mathilda_options_display_consumer_key", "mathilda-options", "twitterapi_settings_section");
	add_settings_field("mathilda_consumer_secret", "Consumer Secret", "mathilda_options_display_consumer_secret", "mathilda-options", "twitterapi_settings_section");
	
	register_setting("mathilda_settings", "mathilda_oauth_access_token");
	register_setting("mathilda_settings", "mathilda_oauth_access_token_secret");
	register_setting("mathilda_settings", "mathilda_consumer_key");
	register_setting("mathilda_settings", "mathilda_consumer_secret");

}

// Plugin Basic Settings 

function mathilda_options_plugin_display()
{
	
	add_settings_section("plugin_settings_section", "Plugin", "mathilda_options_display_plugin_description", "mathilda-options");
	
	add_settings_field("mathilda_twitter_user", "Twitter Account", "mathilda_options_display_twitter_user", "mathilda-options", "plugin_settings_section");
	add_settings_field("mathilda_slug", "Slug", "mathilda_options_display_slug", "mathilda-options", "plugin_settings_section");
	
	register_setting("mathilda_settings", "mathilda_twitter_user", "mathilda_format_twitterlogin");
	register_setting("mathilda_settings", "mathilda_slug", "mathilda_validate_slug");

}

// User Interface Settings 

function mathilda_options_userinterface_display()
{
	
	add_settings_section("userinterface_settings_section", "User Interface", "mathilda_options_display_userinterface_description", "mathilda-options");
	
	add_settings_field("mathilda_tweets_on_page", "Tweets on Page", "mathilda_options_display_tweets_on_page", "mathilda-options", "userinterface_settings_section");
	add_settings_field("mathilda_replies", "Show Replies?", "mathilda_options_display_show_replies", "mathilda-options", "userinterface_settings_section");
	add_settings_field("mathilda_quotes", "Show Quotes?", "mathilda_options_display_show_quotes", "mathilda-options", "userinterface_settings_section");
	add_settings_field("mathilda_navigation", "Navigation Type", "mathilda_options_display_navigation", "mathilda-options", "userinterface_settings_section");
	add_settings_field("mathilda_hyperlink_rendering", "Hyperlink Rendering", "mathilda_options_display_hyperlink_rendering", "mathilda-options", "userinterface_settings_section");
	add_settings_field("mathilda_embed", "Embedding?", "mathilda_options_display_embed", "mathilda-options", "userinterface_settings_section");
	add_settings_field("mathilda_tweet_backlink", "Backlink @ Tweet?", "mathilda_options_display_tweet_backlink", "mathilda-options", "userinterface_settings_section");
	
	register_setting("mathilda_settings", "mathilda_tweets_on_page", "mathilda_validate_tweetsonpage");
	register_setting("mathilda_settings", "mathilda_replies", "mathilda_validate_replies");
	register_setting("mathilda_settings", "mathilda_quotes", "mathilda_validate_quotes");
	register_setting("mathilda_settings", "mathilda_embed", "mathilda_validate_embed");
	register_setting("mathilda_settings", "mathilda_tweet_backlink", "mathilda_validate_tweet_backlink");
	register_setting("mathilda_settings", "mathilda_navigation");
	register_setting("mathilda_settings", "mathilda_hyperlink_rendering");

}

// Expert Settings 

function mathilda_options_expert_display()
{
	
	add_settings_section("expert_settings_section", "Expert", "mathilda_options_display_expert_description", "mathilda-options");
	
	add_settings_field("mathilda_css", "Deactivate Mathilda CSS?", "mathilda_options_display_mathilda_css", "mathilda-options", "expert_settings_section");;
	add_settings_field("mathilda_cron_period", "Cron Period", "mathilda_options_display_cron_period", "mathilda-options", "expert_settings_section");
	
	register_setting("mathilda_settings", "mathilda_cron_period", "mathilda_validate_cron_period");
	register_setting("mathilda_settings", "mathilda_css", "mathilda_validate_css");

}

/*
Actions
*/

add_action("admin_init", "mathilda_options_twitterapi_display");
add_action("admin_init", "mathilda_options_plugin_display");
add_action("admin_init", "mathilda_options_userinterface_display");
add_action("admin_init", "mathilda_options_expert_display");

?>