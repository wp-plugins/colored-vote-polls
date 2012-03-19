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
	global $wpdb,$table_prefix,$wpcvp_options;

	$poll_title=$_POST['pollp_title'];
	$poll_desc=$_POST['polldescedit'];
	
	$expiry=$_POST['pollp_expiry'];
	if( ( empty($expiry) or !isset($expiry) or !$expiry ) and  $expiry != 0 ){
		$expiry='1';
	}
	
	$sql = "INSERT INTO ".$table_prefix.WPCVP_POLLS_TABLE." (pollp_title, pollp_desc, pollp_aorder, pollp_unans_color, pollp_status, pollp_expiry, pollp_expiry_date, pollp_adorder, pollp_vresult, pollp_url, pollp_aq, pollp_cat) VALUES ('".$_POST['pollp_title']."', '".$_POST['polldescedit']."', '".$_POST['pollp_aorder']."', '".$_POST['pollp_unans_color']."', '".$_POST['pollp_status']."', '".$expiry."', '".$_POST['pollp_actual_expiry_date']."', '".$_POST['pollp_adorder']."', '".$_POST['pollp_vresult']."' , '".$_POST['pollp_url']."' , '".$_POST['pollp_aq']."' , '".$_POST['pollp_cat']."' )";
	$wpdb->query($sql);
	$pollid=$wpdb->insert_id;
	
	
//for questions
	$j=0;
	foreach ($_POST['qorder'] as $qorder) {
		$j++;
	}
	for($i=0;$i<$j;$i++){
		$sql="INSERT INTO ".$table_prefix.WPCVP_QUESTIONS_TABLE." ( pollq_pid, pollq_order, pollq_title, pollq_desc, pollq_optional) VALUES ('".$pollid."','".$i."' ,'".$_POST['pollq']['title'][$i]."', '".$_POST['pollq']['desc'][$i]."', '".$_POST['qopt'][$i]."')";
		$wpdb->query($sql);
	}

//for answers
	$j=0;
	foreach ($_POST['aorder'] as $aorder) {
		$j++;
	}
	for($i=0;$i<$j;$i++){
		$sql="INSERT INTO ".$table_prefix.WPCVP_ANSWERS_TABLE." ( polla_pid, polla_order, polla_bg, polla_fg, polla_answer) VALUES ('".$pollid."','".$i."' ,'".$_POST['polla']['bg'][$i]."', '".$_POST['polla']['fg'][$i]."', '".$_POST['polla']['text'][$i]."')";
		
		$wpdb->query($sql);
	}
	
	//echo '{ "id": "'.$pollid.'" }';
	wpcvp_get_edit_poll_form($pollid);
?>