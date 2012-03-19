<?php
### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	$wp_root = '../../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}
check_ajax_referer( 'colorvotepoll' );
global $wpdb,$table_prefix,$wpcvp_options;
$poll_table = $table_prefix.WPCVP_POLLS_TABLE;	
$questions_table = $table_prefix.WPCVP_QUESTIONS_TABLE;
$answers_table = $table_prefix.WPCVP_ANSWERS_TABLE;
$pollid=$_POST['pollid'];
if($pollid and $pollid>0) {
	$delete_poll = "DELETE FROM ".$poll_table." where pollp_id = '".$pollid."'";
	$wpdb->query($delete_poll);
	$delete_questions = "DELETE FROM ".$questions_table." where pollq_pid = '".$pollid."'";
	$wpdb->query($delete_questions);
	$delete_answers = "DELETE FROM ".$answers_table." where polla_pid = '".$pollid."'";
	$wpdb->query($delete_answers);
}
?>