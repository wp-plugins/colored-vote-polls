<?php
/*
Plugin Name: Colored Vote Polls
Plugin URI: http://www.webfanzine.com/colored-vote-polls/
Description: Adds an AJAX poll system, with each poll resembling a survey with multiple questions to your WordPress site. The results of the survey or the poll will be displayed in multi-colored format and can be sorted on the basis of most positive or negative answers. This will help you gather feedback from the visitors and distinguish the the most positively voted questions easily.  
Version: 1.0
Author: Kim Gjerstad, Tejaswini Deshpande
Author URI: http://www.lafabriquedeblogs.com/
*/

/*  
	Copyright 2011-2012  Tejaswini Deshpande  (email : tedeshpa@gmail.com), 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
global $wpcvp_db_version, $wpcvp_options;
$wpcvp_db_version = "1.0";
$wpcvp_options=get_option('wpcvp_options');
define("WPCVP_VER","1.0",false);//Current Version of Colored Vote Polls plugin
define('WPCVP_POLLS_TABLE','wpcvp_pollsp'); //Polls TABLE NAME
define('WPCVP_QUESTIONS_TABLE','wpcvp_pollsq'); //Questions TABLE NAME
define('WPCVP_ANSWERS_TABLE','wpcvp_pollsa'); //Answers TABLE NAME
define('WPCVP_LOGS_TABLE','wpcvp_pollsip'); //Poll Logs TABLE NAME
if ( ! defined( 'WPCVP_PLUGIN_BASENAME' ) )
	define( 'WPCVP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Create Text Domain For Translations
load_plugin_textdomain('wpcvp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

global $wpcvp_message; //global messages
$wpcvp_message=array('poll_closed'=>__('This Poll is closed!!','wpcvp'),
					 'reg_user_vote'=>__('Please log in to Vote!!','wpcvp'),
					 'already_voted'=>__('You Have Already Voted For This Poll!!','wpcvp')
					 );

//Code executed while activating the plugin
function install_wpcvp() {
	global $wpdb, $table_prefix, $wpcvp_db_version, $wpcvp_options;

	$installed_ver = get_option( "wpcvp_db_version" );
	if( $installed_ver != $wpcvp_db_version ) {
		// Create Colored Vote Polls Tables (4 Tables)
		$charset_collate = '';
		if($wpdb->supports_collation()) {
			if(!empty($wpdb->charset)) {
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if(!empty($wpdb->collate)) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}
		$create_table = array();
		$create_table['WPCVP_POLLS_TABLE'] = "CREATE TABLE ".$table_prefix.WPCVP_POLLS_TABLE." (".
										"pollp_id int(10) NOT NULL auto_increment,".
										"pollp_title varchar(200) character set utf8 NOT NULL default '',".
										"pollp_desc text character set utf8 NOT NULL default '',".
										"pollp_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,".
										"pollp_status tinyint(1) NOT NULL default '0',".
										"pollp_expiry tinyint(1) NOT NULL default '0',".
										"pollp_expiry_date varchar(20) NOT NULL default '',".
										"pollp_aorder tinyint(1) NOT NULL default '0',".
										"pollp_unans_color char(7) NOT NULL default '#CCCCCC',".
										"pollp_adorder tinyint(1) NOT NULL default '0',".
										"pollp_vresult tinyint(1) NOT NULL default '0',".
										"pollp_url varchar(200) character set utf8 NOT NULL default '',".
										"pollp_aq tinyint(1) NOT NULL default '0',".
										"pollp_cat varchar(200) NOT NULL default '',".
										"pollp_totalvotes int(10) NOT NULL default '0',".
										"pollp_totalvoters int(10) NOT NULL default '0',".
										"PRIMARY KEY (pollp_id)) $charset_collate;";
		$create_table['WPCVP_QUESTIONS_TABLE'] = "CREATE TABLE ".$table_prefix.WPCVP_QUESTIONS_TABLE." (".
										"pollq_qid int(10) NOT NULL auto_increment,".
										"pollq_pid int(10) NOT NULL default '0',".
										"pollq_order int(3) NOT NULL default '1',".
										"pollq_title varchar(200) character set utf8 NOT NULL default '',".
										"pollq_desc text character set utf8 NOT NULL default '',".
										"pollq_optional tinyint(1) NOT NULL default '0',".
										"pollq_votes int(10) NOT NULL default '0',".
										"PRIMARY KEY (pollq_qid)) $charset_collate;";
		$create_table['WPCVP_ANSWERS_TABLE'] = "CREATE TABLE ".$table_prefix.WPCVP_ANSWERS_TABLE." (".
										"polla_aid int(10) NOT NULL auto_increment,".
										"polla_pid int(10) NOT NULL default '0',".
										"polla_order int(3) NOT NULL default '1',".
										"polla_bg char(7) NOT NULL default '',".
										"polla_fg char(7) NOT NULL default '',".
										"polla_answer varchar(200) character set utf8 NOT NULL default '',".
										"polla_votes int(10) NOT NULL default '0',".
										"PRIMARY KEY (polla_aid)) $charset_collate;";
		$create_table['WPCVP_LOGS_TABLE'] = "CREATE TABLE ".$table_prefix.WPCVP_LOGS_TABLE." (".
										"pollip_id int(10) NOT NULL auto_increment,".
										"pollip_pid varchar(10) NOT NULL default '',".
										"pollip_qid varchar(10) NOT NULL default '',".
										"pollip_aid varchar(10) NOT NULL default '',".
										"pollip_ip varchar(100) NOT NULL default '',".
										"pollip_host VARCHAR(200) NOT NULL default '',".
										"pollip_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,".
										"pollip_user tinytext NOT NULL,".
										"pollip_userid int(10) NOT NULL default '0',".
										"PRIMARY KEY (pollip_id),".
										"KEY pollip_ip (pollip_id),".
										"KEY pollip_pid (pollip_pid)".
										") $charset_collate;";
										
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $create_table['WPCVP_POLLS_TABLE'] );	
		dbDelta( $create_table['WPCVP_QUESTIONS_TABLE'] );
		dbDelta( $create_table['WPCVP_ANSWERS_TABLE'] );
		dbDelta( $create_table['WPCVP_LOGS_TABLE'] );
		
		$default_options=array('permission'=>'0', 
							   'track'=>'0',
							   'colors'=>array( 
												array(
													'bg'=>'#cccccc',
													'fg'=>'#000000',
													'text'=>'I don\'t know'
												),
												array(
													'bg'=>'#cf2a27',
													'fg'=>'#ffffff',
													'text'=>'I don\'t like it'
												),
												array(
													'bg'=>'#ffff00',
													'fg'=>'#000000',
													'text'=>'It\'s ok'
												),
												array(
													'bg'=>'#b6d7a8',
													'fg'=>'#000000',
													'text'=>'I like'
												),
												array(
													'bg'=>'#38761d',
													'fg'=>'#ffffff',
													'text'=>'I love it'
												)			
							   ),
							   'maxchar'=>'50',
							   'notify'=>'0'
							   );
		
		foreach($default_options as $key=>$value) {
		  if(!isset($wpcvp_options[$key])) {
			 $wpcvp_options[$key] = $value;
		  }
		}
		delete_option('wpcvp_options');	  
		update_option('wpcvp_options',$wpcvp_options);
		
		update_option( "wpcvp_db_version", $wpcvp_db_version );
	}
	
	$userid=get_current_user_id( );
	if( !wpcvp_page_exists_by_slug('wpcvp-preview') ){
		$preview_page_args = array(
		  'comment_status' => 'closed',
		  'ping_status' => 'closed',
		  'post_author' => $userid, 
		  'post_content' => '[colorvote]', 
		  'post_name' => 'wpcvp-preview', 
		  'post_status' => 'draft', 
		  'post_title' => __("Colored Vote Poll Preview","wpcvp"),
		  'post_type' => 'page' 
		); 
		// Insert the page into the database
		wp_insert_post( $preview_page_args );
	}	
}
register_activation_hook( __FILE__, 'install_wpcvp' );

function wpcvp_update_db_check() {
    global $wpcvp_db_version;
    if (get_site_option('wpcvp_db_version') != $wpcvp_db_version) {
        install_wpcvp();
    }
}
add_action('plugins_loaded', 'wpcvp_update_db_check');

function wpcvp_plugin_url( $path = '' ) {
	return plugins_url( $path, __FILE__ );
}


function wpcvp_wp_head(){ 
global $wpcvp_options;
?>
<script type="text/javascript">
		jQuery(document).ready(function() {			
		});
	
</script>
<?php
}
add_action( 'wp_head', 'wpcvp_wp_head' );

function wpcvp_enqueue_scripts() {	
global $wpcvp_options;
	wp_enqueue_script( 'formtips', wpcvp_plugin_url( 'js/formtips.js' ),array('jquery'), WPCVP_VER, false);
	wp_enqueue_style( 'wpcvp_css', wpcvp_plugin_url( 'css/style.css' ),
			false, WPCVP_VER, 'all');
	wp_enqueue_script( 'wpcvp', wpcvp_plugin_url( 'js/wpcvp.js' ),array('jquery'), WPCVP_VER, false);
	
	if($wpcvp_options['maxchar'] > 0){
		$maxchar=$wpcvp_options['maxchar'];
	}else{
		$maxchar=3000;
	}
	$nonce= wp_create_nonce('colorvotepoll');
	wp_localize_script('wpcvp', 'wpcvpL10n', array(
			'submit_url' => wpcvp_plugin_url( 'includes/front.php' ),
			'delete_poll_confirm' => __('Delete the Poll with ID ','wpcvp'),
			'mandatory_question' => __('Answer is mandatory!! ','wpcvp'),
			'maxchar' => $maxchar,
			'read_more' => __('Expand to read more. ','wpcvp'),
			'ajn'=> $nonce
		));
}

add_action( 'wp', 'wpcvp_enqueue_scripts' );
add_action('admin_head','wpcvp_tiny_mce', 12);
function wpcvp_tiny_mce() {
	wp_tiny_mce( false ); // true gives you a stripped down version of the editor
}

//Admin Menu
require_once (dirname (__FILE__) . '/admin/menu.php');
require_once (dirname (__FILE__) . '/includes/functions.php');
//Polls Shortcode
require_once (dirname (__FILE__) . '/includes/shortcodes.php');
?>