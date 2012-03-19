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
	
	$poll_table = $table_prefix.WPCVP_POLLS_TABLE;
	$questions_table = $table_prefix.WPCVP_QUESTIONS_TABLE;
	$answers_table = $table_prefix.WPCVP_ANSWERS_TABLE;
	
	$pollid=$_POST['pollid'];
	
	$expiry=$_POST['pollp_expiry'];
	if( ( empty($expiry) or !isset($expiry) or !$expiry ) and  $expiry != 0 ){
		$expiry='1';
	}
	$poll_update='UPDATE '.$poll_table.' 
						SET pollp_title = "'.$_POST['pollp_title'].'" ,
						pollp_desc = "'.$_POST['polldescedit'].'" ,
						pollp_status="'.$_POST['pollp_status'].'",
						pollp_expiry="'.$expiry.'",
						pollp_expiry_date="'.$_POST['pollp_actual_expiry_date'].'",
						pollp_aorder="'.$_POST['pollp_aorder'].'",
						pollp_unans_color="'.$_POST['pollp_unans_color'].'",
						pollp_adorder="'.$_POST['pollp_adorder'].'",
						pollp_vresult="'.$_POST['pollp_vresult'].'",
						pollp_url="'.$_POST['pollp_url'].'",
						pollp_aq="'.$_POST['pollp_aq'].'",
						pollp_cat="'.$_POST['pollp_cat'].'"
				WHERE pollp_id = "'.$_POST['pollid'].'"';
	
	$wpdb->query($poll_update);

	//for delete
	$qdel_arr=explode(',',$_POST['qid_del']);
	if($qdel_arr){
		foreach($qdel_arr as $qid){
			if( $qid != '0' ) {
				$sql = "DELETE FROM ".$table_prefix.WPCVP_QUESTIONS_TABLE." where pollq_qid = '".$qid."'";
				$wpdb->query($sql);
			}
		}
	}
	
	$qids=$_POST['qid'];
	$i=0;
	foreach($qids as $qid){
		if($qid=='0'){
			$questions_insert="INSERT INTO ".$questions_table." ( pollq_pid, pollq_order, pollq_title, pollq_desc, pollq_optional) VALUES ('".$pollid."','".$i."' ,'".$_POST['pollq']['title'][$i]."', '".$_POST['pollq']['desc'][$i]."', '".$_POST['qopt'][$i]."')";
			$wpdb->query($questions_insert);
		}
		else{
			$questions_update= 'UPDATE '.$questions_table.' 
									SET pollq_pid = "'.$pollid.'",
										pollq_order = "'.$i.'",
										pollq_title = "'.$_POST['pollq']['title'][$i].'",
										pollq_desc = "'.$_POST['pollq']['desc'][$i].'",
										pollq_optional = "'.$_POST['qopt'][$i].'"
								WHERE pollq_qid = "'.$qid.'"';
			$wpdb->query($questions_update);
		}
		$i++;
	}
	
	//for delete
	$adel_arr=explode(',',$_POST['aid_del']);
	if($adel_arr){
		foreach($adel_arr as $aid){
			if( $aid != '0' ) {
				$sql = "DELETE FROM ".$answers_table." where polla_aid = '".$aid."'";
				$wpdb->query($sql);
			}
		}
	}
	
	$aids=$_POST['aid'];
	$i=0;
	foreach($aids as $aid){
		if($aid=='0'){
			$answers_insert="INSERT INTO ".$answers_table." ( polla_pid, polla_order, polla_bg, polla_fg, polla_answer) VALUES ('".$pollid."','".$i."' ,'".$_POST['polla']['bg'][$i]."', '".$_POST['polla']['fg'][$i]."', '".$_POST['polla']['text'][$i]."')";
			$wpdb->query($answers_insert);
		}
		else{
			$answers_update= 'UPDATE '.$answers_table.' 
									SET polla_pid = "'.$pollid.'",
										polla_order = "'.$i.'",
										polla_bg = "'.$_POST['polla']['bg'][$i].'",
										polla_fg = "'.$_POST['polla']['fg'][$i].'",
										polla_answer = "'.$_POST['polla']['text'][$i].'"
								WHERE polla_aid = "'.$aid.'"';
			$wpdb->query($answers_update);
		}
		$i++;
	}
?>