<?php

/*
Security
*/

if (!defined('ABSPATH'))
{
	exit;
}

/*
Mathilda Scheduling
*/

/* General Interval */

function mathilda_cron_interval( $schedules ) {

	$period=get_option('mathilda_cron_period');

    $schedules['mathilda_duration'] = array(
        'interval' => $period,
        'display'  => esc_html__( 'Mathilda Custom Duration' ),
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'mathilda_cron_interval' );

/* Import Interval */

function mathilda_import_interval( $schedules ) {

	$period=get_option('mathilda_import_interval');

    $schedules['mathilda_import_window'] = array(
        'interval' => $period,
        'display'  => esc_html__( 'Mathilda Import' ),
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'mathilda_import_interval' );

/* Mathilda Tweet Load Cron */

function mathilda_cron_execute_tweetload() {

	$initial_load = get_option('mathilda_initial_load');
	$cron_status=get_option('mathilda_cron_status');

	if($initial_load==1 && $cron_status==1) {
		mathilda_cron_script();
	}
}
add_action( 'mathilda_tweetload_schedule', 'mathilda_cron_execute_tweetload' );

/* Mathilda Import Cron */

function mathilda_import_execute() {

	$import_status=get_option('mathilda_import_running');
	if($import_status==1) {
		mathilda_import_process();
	}
}
add_action( 'mathilda_import_schedule', 'mathilda_import_execute' );

/* Mathilda Embed Cron */

function mathilda_cron_update_embed() {
	$number_of_links=mathilda_urls_count();
	if($number_of_links>0) {
		mathilda_update_embed();
		mathilda_tweets_updated_fire();
	}
}
add_action( 'mathilda_embed_schedule', 'mathilda_cron_update_embed' );

/* Mathilda Scripts */

function mathilda_update_embed() {

	set_time_limit(600);

	$open_embeds=array();

	global $wpdb;
	$table_name=$wpdb->prefix . 'mathilda_urls';
	$open_embeds=$wpdb->get_results( "SELECT mathilda_url_id, mathilda_url_extended, mathilda_url_embed FROM $table_name WHERE mathilda_url_embed='OPEN'", ARRAY_N);

	$number_of_open_embeds = count($open_embeds);

	if($number_of_open_embeds<1) {
		echo 'No embeds to load';
		return;
	}

	if($number_of_open_embeds>30) {
		$number_of_open_embeds=30;
	}

	$i=0;
	while($i < $number_of_open_embeds) {

		$mathilda_embed_code = wp_oembed_get($open_embeds[$i][1]);
		$mathilda_embed_code_length = strlen($mathilda_embed_code);

		if($mathilda_embed_code_length!=0) {

			$mathilda_embed_code_transform=htmlentities($mathilda_embed_code, ENT_QUOTES);
    		mathilda_add_embed($mathilda_embed_code_transform,$open_embeds[$i][0]);
			echo ($i+1) . ': '. $open_embeds[$i][1] . ' was embedded.<br/>';

		} else {

			mathilda_add_embed('NOEMBED',$open_embeds[$i][0]);
			echo ($i+1) . ': '. $open_embeds[$i][1] . ' delivers no embed code.<br/>';
		
		}

   		$i++;
	}

}

/* Schedule Mathilda Crons */

if( !wp_next_scheduled( 'mathilda_tweetload_schedule' ) ) {
	wp_schedule_event( time(), 'mathilda_duration', 'mathilda_tweetload_schedule' );
}
if( !wp_next_scheduled( 'mathilda_import_schedule' ) ) {
		wp_schedule_event( time(), 'mathilda_import_window', 'mathilda_import_schedule' );
}
if( !wp_next_scheduled( 'mathilda_embed_schedule' ) ) {
	wp_schedule_event( time(), 'mathilda_duration', 'mathilda_embed_schedule' );
}

?>
