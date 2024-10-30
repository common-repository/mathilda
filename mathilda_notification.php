<?php

// Security
if (!defined('ABSPATH')) { exit; }

/*
Mathilda Notifications
*/

/*
Mathilda Import Status Notification
* Function creates Notification including Import Status
* Input: none
* Output: none
*/

function mathilda_import_status_notification(){

	if(get_option('mathilda_import_running')==0) {return;}

	global $pagenow;
	$mathilda_notification_show=false;

	/*
	Determine Page
	*/

	if ( isset($_GET['page']) ) {
		if( in_array( $pagenow, array('tools.php') ) && ( $_GET['page'] == 'mathilda-tools-menu') && !( $_GET['import'] == 'true') ){ $mathilda_notification_show=true; }
	}

	if ( isset($_GET['page']) ) {
		if( in_array( $pagenow, array('options-general.php') ) && ( $_GET['page'] == 'mathilda-options' ) ){ $mathilda_notification_show=true; }
	}

	/*
	Show Message
	*/

	if($mathilda_notification_show==true) {

		if(!(isset($_GET['resetisconfirmed'])) AND !(isset($_GET['importbreak']))) {
			if(!($_GET['resetisconfirmed']=='true') AND !($_GET['importbreak']=='true'))
			{
				$mathilda_import_status=mathilda_get_import_status();
				echo '<div class="notice notice-success is-dismissible"><p>';
				echo 'Mathilda Import Status: '.$mathilda_import_status.' %</p></div>';
			}
		}

	}

}
// add_action('admin_notices', 'mathilda_import_status_notification');

// mathilda_update_notice
// The function brings a message, if activites are required
// Input: none
// Output: none

function mathilda_update_notice(){
    global $pagenow;
    if ( $pagenow == 'index.php' OR $pagenow == 'tools.php' OR $pagenow == 'options-general.php' OR $pagenow == 'plugins.php') {
		if(get_option('mathilda_plugin_version')==7) {
			if(mathilda_update_seven_aftercheck()==false) {
				echo '<div class="notice notice-warning is-dismissible"><p>';
         		echo 'Mathilda Update Notice: Please reset Mathilda and reload your data from twitter! <a href="'.admin_url().'tools.php?page=mathilda-tools-menu">Yes, I do it now!</a>';
         		echo '</p></div>';
			}
		}
    }
}

add_action('admin_notices', 'mathilda_update_notice');

// mathilda_import_notice
// The function brings a message, if import is finished
// Input: none
// Output: none

function mathilda_import_notice(){
    global $pagenow;
    if ( $pagenow == 'index.php' OR $pagenow == 'tools.php' OR $pagenow == 'options-general.php' OR $pagenow == 'plugins.php') {
		if(get_option('mathilda_import_finish')==1) {

				$number_of_files=get_option('mathilda_import_numberoffiles');
				echo '<div class="notice notice-success is-dismissible"><p>';
				echo 'Mathilda Import Notice: Tweet History imported completly. '.$number_of_files.' Files processed.</p></div>';
				update_option('mathilda_import_finish',0);

		}
    }
}

add_action('admin_notices', 'mathilda_import_notice');

/*
WordPress Multisite Notice
*/

// Shows Message Box on Child Sites within WordPress Multisite Network
// Input: None
// Output: None

function mathilda_multisite_notice(){
    
	if ( is_multisite() ) { 
		if (get_current_blog_id()>1) {

			global $pagenow;

			if ( $pagenow == 'index.php' OR $pagenow == 'tools.php' OR $pagenow == 'options-general.php' OR $pagenow == 'plugins.php') {

				echo '<div class="notice notice-warning is-dismissible"><p>';
				echo '<strong>Mathilda Plugin Notice:</strong><br/>Mathilda does not support child sites within WordPress Networks. Please deactivate the plugin on this instance! <a href="'.get_admin_url( get_current_blog_id() ) .'plugins.php?">Understood, I do it now!</a>';
				echo '</p></div>';	
					
			}
		}
	} 
    
}

add_action('admin_notices', 'mathilda_multisite_notice');

?>