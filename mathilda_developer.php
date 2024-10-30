<?php

/* 
Security
*/

if (!defined('ABSPATH')) { exit; }

/*
Is Developer
*/

$mathilda_developer=get_option('mathilda_developer');
if ($mathilda_developer == 1)
{add_action( 'admin_menu', 'mathilda_developer_options_menu');}

/* 
Developer Options Menu
*/

function mathilda_developer_options_menu() {
add_options_page('Mathilda Developer', 'Mathilda Developer', 'manage_options', 'mathilda-developer-options', "mathilda_developer_options_content");
}

/*
Developer Options Page
*/

function mathilda_developer_options_content() {
	
	echo '<div class="wrap">
	<h1>Options › Mathilda Developer</h1>
	<p class="mathilda_settings">Internal Process Options<br/>&nbsp;</p>
	<form method="post" action="options.php">';
	
	do_settings_sections( 'mathilda-developer-options' );
	settings_fields( 'mathilda_developer_settings' );
	submit_button();

	echo '</form></div><div class="clear"></div>';
}

/*
Fields
#*/

function mathilda_options_display_num_tweets_fetch_call()
{
	echo '<input type="text" name="mathilda_num_tweets_fetch_call" id="mathilda_num_tweets_fetch_call" value="'. get_option('mathilda_num_tweets_fetch_call') .'"/>';
}

function mathilda_options_display_num_fetches()
{
	echo '<input type="text" name="mathilda_num_fetches" id="mathilda_num_fetches" value="'. get_option('mathilda_num_fetches') .'"/>';
}

function mathilda_options_display_initial_load()
{
	echo '<input type="text" name="mathilda_initial_load" id="mathilda_initial_load" value="'. get_option('mathilda_initial_load') .'"/>';
}

function mathilda_options_display_latest_tweet()
{
	echo '<input type="text" name="mathilda_latest_tweet" id="mathilda_latest_tweet" value="'. get_option('mathilda_latest_tweet') .'"/>';
}

function mathilda_options_display_database_version()
{
	echo '<input type="text" name="mathilda_database_version" id="mathilda_database_version" value="'. get_option('mathilda_database_version') .'"/>';
}

function mathilda_options_display_import()
{
	echo '<input type="text" name="mathilda_import" id="mathilda_import" value="'. get_option('mathilda_import') .'"/>';
}

function mathilda_options_display_plugin_version()
{
	echo '<input type="text" name="mathilda_plugin_version" id="mathilda_plugin_version" value="'. get_option('mathilda_plugin_version') .'"/>';
}

function mathilda_options_display_cron_period_seconds()
{
	echo '<input type="text" name="mathilda_cron_period" id="mathilda_cron_period" value="'. get_option('mathilda_cron_period') .'" disabled/>';
}

function mathilda_options_display_tweets_count()
{
	echo '<input type="text" name="mathilda_tweets_count" id="mathilda_tweets_count" value="'. get_option('mathilda_tweets_count') .'"/>';
}

function mathilda_options_display_highest_tweet()
{
	echo '<input type="text" name="mathilda_highest_imported_tweet" id="mathilda_highest_imported_tweet" value="'. get_option('mathilda_highest_imported_tweet') .'"/>';
}

/* 
Sections
*/

function mathilda_options_display_developer_description()
{ echo '<p>Database Values</p>'; }

/* 
Definitions
*/

function mathilda_options_developer_display()
{
	
	add_settings_section("developer_settings_section", "Read / Manipulate", "mathilda_options_display_developer_description", "mathilda-developer-options");
	
	add_settings_field("mathilda_num_tweets_fetch_call", "Number Tweets @ Call", "mathilda_options_display_num_tweets_fetch_call", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_num_fetches", "Fetches", "mathilda_options_display_num_fetches", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_initial_load", "Initial Load", "mathilda_options_display_initial_load", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_latest_tweet", "Latest Tweet", "mathilda_options_display_latest_tweet", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_database_version", "Database Version", "mathilda_options_display_database_version", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_import", "Import", "mathilda_options_display_import", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_plugin_version", "Plugin Version", "mathilda_options_display_plugin_version", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_cron_period_seconds", "Cron Period Seconds", "mathilda_options_display_cron_period_seconds", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_tweets_count", "Tweet Counter", "mathilda_options_display_tweets_count", "mathilda-developer-options", "developer_settings_section");
	add_settings_field("mathilda_highest_imported_tweet", "Highest Imported Tweet", "mathilda_options_display_highest_tweet", "mathilda-developer-options", "developer_settings_section");

	register_setting("mathilda_developer_settings", "mathilda_num_tweets_fetch_call");
	register_setting("mathilda_developer_settings", "mathilda_num_fetches");
	register_setting("mathilda_developer_settings", "mathilda_initial_load");
	register_setting("mathilda_developer_settings", "mathilda_latest_tweet");
	register_setting("mathilda_developer_settings", "mathilda_database_version");
	register_setting("mathilda_developer_settings", "mathilda_import");
	register_setting("mathilda_developer_settings", "mathilda_plugin_version");
	register_setting("mathilda_developer_settings", "mathilda_tweets_count");
	register_setting("mathilda_developer_settings", "mathilda_highest_imported_tweet");

}

add_action("admin_init", "mathilda_options_developer_display");

?>