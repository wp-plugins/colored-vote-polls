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
$log_table = $table_prefix.WPCVP_LOGS_TABLE;
$pollid=$_GET['pollid'];

$poll=wpcvp_get_poll_id( $pollid );
$questions=wpcvp_get_questions_poll_id($pollid);
$answers=wpcvp_get_answers_poll_id($pollid);

if($pollid and $pollid>0) {
	if(  ($poll[0]['pollp_aorder']=='0' and $poll[0]['pollp_adorder']=='0') or ($poll[0]['pollp_aorder']=='1' and $poll[0]['pollp_adorder']=='1') ){
			$answers=array_reverse($answers);
	}
	@header("Content-type: text/x-csv");
	// If the file is NOT requested via AJAX, force-download
	if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		header("Content-Disposition: inline; filename=pollID-".$pollid.".csv");
	}
	$exportCSV='';
	$heading_arr=array();
	$heading_arr[]=__('Question Title','wpcvp');
	foreach($answers as $answer ){
			$heading_arr[]=$answer['polla_answer'];
	}
	$heading_arr[]=__('No vote','wpcvp');
	$exportCSV.= stripslashes( implode(',',$heading_arr) );
	$exportCSV.="\n";
	
	//Logic start
	$counta=count($answers);
	
	//+1 for unanswered question
	if($question['pollq_optional']!='0'){
		$tdwidth=floor( 100 / ($counta + 1) );
	}
	if($counta=='0') $counta='1';
	
	$results=array();
	foreach($questions as $question ){
		$sql='SELECT COUNT(*) from '.$log_table.' WHERE pollip_pid='.$poll[0]['pollp_id'].' AND pollip_qid='.$question['pollq_qid'].'';
		$count_arr=$wpdb->get_results($sql, ARRAY_A);
		$qvotes=$count_arr[0]['COUNT(*)'];
		$table_width='';
		if(!$qvotes or $qvotes==0) {
			$table_width='style="width:0;"';
		}
		
		$result=array();
		$result['acount']=$counta;
		$result['qid']=$question['pollq_qid'];
		$result['qcount']=$qvotes;
		$result['qtitle']=$question['pollq_title'];
		$result['qdesc']=$question['pollq_desc'];
		$result['twidth']=$table_width;
		$i=0;
		foreach( $answers as $answer ){
			$sql='SELECT COUNT(*) from '.$log_table.' WHERE pollip_pid='.$poll[0]['pollp_id'].' AND pollip_qid='.$question['pollq_qid'].' AND pollip_aid='.$answer['polla_aid'].'';
			$count_arr=$wpdb->get_results($sql, ARRAY_A);
			$aqvotes=$count_arr[0]['COUNT(*)'];
			if($qvotes and $qvotes>0) {
				$tdwidth = floor( (100 * $aqvotes) / $qvotes );
			}
			else{
				$tdwidth='0';
			}
			if($tdwidth == '0') $tdwidth_str='';
			else $tdwidth_str=$tdwidth.'%';
			
			$result['results'][$i]['avotes']=$aqvotes;
			$result['results'][$i]['bg']=$answer['polla_bg'];
			$result['results'][$i]['fg']=$answer['polla_fg'];
			$result['results'][$i]['tdstr']=$tdwidth_str;
			$result['results'][$i]['percent']=$tdwidth;

			$i++;	
		}
		
		//for non-mandatory question
		//if($question['pollq_optional']!='0'){
			$sql='SELECT COUNT(*) from '.$log_table.' WHERE pollip_pid='.$poll[0]['pollp_id'].' AND pollip_qid='.$question['pollq_qid'].' AND pollip_aid=""';
			$unans_count_arr=$wpdb->get_results($sql, ARRAY_A);
			$unans_qvotes=$unans_count_arr[0]['COUNT(*)'];
			if($qvotes and $qvotes>0) {
				$tdwidth = floor( (100 * $unans_qvotes) / $qvotes );
			}
			else{
				$tdwidth='0';
			}
			if($tdwidth == '0') $tdwidth_str='';
			else $tdwidth_str=$tdwidth.'%';
			
			$result['acount']=$result['acount'] + 1;
			$result['results'][$i]['avotes']=$unans_qvotes;
			$result['results'][$i]['bg']=$poll[0]['pollp_unans_color'];
			$result['results'][$i]['fg']='inherit';
			$result['results'][$i]['tdstr']=$tdwidth_str;
			$result['results'][$i]['percent']=$tdwidth;
			
			$i++;
		//}
		
		array_push($results,$result);
	}
	unset($result);
	
	//sort if necessary
	if( $poll[0]['pollp_adorder']=='0' or $poll[0]['pollp_adorder']=='1' ){
		usort($results, 'wpcvp_results_cmp') ;
	}
	
	$data_arr=array();
	foreach($results as $result){
		$data_arr[]='"'.$result['qtitle'].'"';
		$acount=$result['acount'];
		for($j=0;$j<$acount;$j++){
			$data_arr[]='"'.$result['results'][$j]['percent'].'%'.'"';
		}
		$exportCSV.=stripslashes( implode(',',$data_arr) );
		$exportCSV.="\n";
		unset($data_arr);
	}
	
    print($exportCSV); 
	exit();
}
?>