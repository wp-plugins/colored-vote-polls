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
	global $wpdb,$table_prefix,$wpcvp_options,$wpcvp_message, $user_identity, $user_ID;
	$poll_table = $table_prefix.WPCVP_POLLS_TABLE; 
	$questions_table = $table_prefix.WPCVP_QUESTIONS_TABLE;
	$answers_table = $table_prefix.WPCVP_ANSWERS_TABLE;
	$log_table = $table_prefix.WPCVP_LOGS_TABLE;
	
	$pollid=$_POST['addq'];
	if($pollid and $pollid>0 ) {
		$sql='SELECT pollq_order from '.$questions_table.' WHERE pollq_pid='.$pollid.' ORDER BY pollq_order DESC ';
		$results=$wpdb->get_results($sql, ARRAY_A);
		$last_qorder=$results[0]['pollq_order'];
		$last_qorder++;
		//Add question to database
		$sql="INSERT INTO ".$questions_table." ( pollq_pid, pollq_order, pollq_title, pollq_desc, pollq_optional) VALUES ('".$pollid."','".$last_qorder."','".$_POST['title']."', '".$_POST['desc']."', '1' )";
		$wpdb->query($sql);
		$qid=$wpdb->insert_id;
		//get question gor question id
		$question=wpcvp_get_questions_qid($qid);
		
		//get answers for poll id
		$answers=wpcvp_get_answers_poll_id($pollid);
		$count=count($answers);
		if($count=='0') $count='1';
		
		$tdwidth=floor( 100 / $count );
		$i=$_POST['qi'];
		$html.='<div class="qdesc'.$question['pollq_qid'].'"><h3>'.$question['pollq_title'].'</h3>';
		$html.='<span class="qdesc">'.$question['pollq_desc'].'</span></div>';
		$html.='<table><tr>';
		$j=0;
		foreach($answers as $answer ){
			$html.='<td style="width:'.$tdwidth.'%;background-color:'.$answer['polla_bg'].';color:'.$answer['polla_fg'].';"><input name="aqid['.$question['pollq_qid'].']" class="aqid" id="radio-'.$question['pollq_qid'].'-'.$answer['polla_aid'].'" alt="'.$question['pollq_qid'].'" type="radio" value="'.$answer['polla_aid'].'" /> <small> &nbsp;<label style="color:'.$answer['polla_fg'].';" class="wpcvp-label" for="radio-'.$question['pollq_qid'].'-'.$answer['polla_aid'].'">'.$answer['polla_answer'].'</label></small><input type="hidden" name="aid['.$i.']['.$j.']" value="'.$answer['polla_aid'].'" /></td>';
			$j++;
		}
		$html.='</tr></table><input type="hidden" name="qid['.$i.']" value="'.$question['pollq_qid'].'" />';
		
		if($wpcvp_options['notify']=='1') {
			$data=array();
			$data['pollid']=$pollid;
			$data['qtitle']=$question['pollq_title'];
			$data['qdesc']=$question['pollq_desc'];
			
			wpcvp_addq_send_email($data);
		}
		
		print($html);
	}

//for 'Vote!' Submit	
	$pollid=0;
	$pollid=$_POST['pollid'];
	
	if($pollid and $pollid>0) {
	
		$check_voted = wpcvp_check_voted($pollid);
		if($check_voted == 0) {
			$poll=wpcvp_get_poll_id($pollid);
			
			if(!empty($user_identity)) {
				$pollip_user = htmlspecialchars(addslashes($user_identity));
			} elseif(!empty($_COOKIE['comment_author_'.COOKIEHASH])) {
				$pollip_user = htmlspecialchars(addslashes($_COOKIE['comment_author_'.COOKIEHASH]));
			} else {
				$pollip_user = __('Guest', 'wp-polls');
			}
			$pollip_userid = intval($user_ID);
			$pollip_ip = wpcvp_get_ipaddress();
			$pollip_host = esc_attr(@gethostbyaddr($pollip_ip));
			$pollip_timestamp = current_time('mysql');
		
			$totalvoters=$poll[0]['pollp_totalvoters'];
			$totalvotes=$poll[0]['pollp_totalvotes'];
			
			$qarr=$_POST['qarr'];
			$aarr=$_POST['aarr'];
			$count=$_POST['qcount'];

			$html='';

			for($i=0;$i<$count;$i++){
				$qid=$qarr[$i];
				$aid=$aarr[$i];
				if($aid>0) {	
					$question=wpcvp_get_questions_qid($qid);
					$qvotes=$question['pollq_votes'];
					$qvotes++;
					$questions_update='UPDATE '.$questions_table.' SET pollq_votes  = "'.$qvotes.'" WHERE pollq_qid = "'.$qid.'"';
					
					$wpdb->query($questions_update);
					
					$answer=wpcvp_get_answers_aid($aid);
					$avotes=$answer['polla_votes'];
					$avotes++;
					$answers_update='UPDATE '.$answers_table.' SET polla_votes  = "'.$avotes.'" WHERE polla_aid = "'.$aid.'"';
					
					$wpdb->query($answers_update);	
					$totalvotes++;
				}
				
				if($aid=='0') $aid='';

				$log_insert="INSERT INTO ".$log_table." (pollip_pid, pollip_qid, pollip_aid, pollip_ip, pollip_host, pollip_timestamp, pollip_user, pollip_userid  ) VALUES ( '".$poll[0]['pollp_id']."', '".$qid."', '".$aid."', '".$pollip_ip."', '".$pollip_host."', '".$pollip_timestamp."', '".$pollip_user."','".$pollip_userid."' )";
					
				$wpdb->query($log_insert);
				$logid=$wpdb->insert_id;
				
				$logarray[$i]=$aid;
			}
			$logstring=implode(',',$logarray);
			// Only Create Cookie If User Choose Logging Method 0 Or 1
			$poll_logging_method = intval($wpcvp_options['track']);
			if($poll_logging_method == 0 or $poll_logging_method == 1) {
					$cookie_expiry = 30000000;
					$vote_cookie = setcookie('wpcvp_voted_'.$poll[0]['pollp_id'], $logstring, ( current_time('timestamp') + $cookie_expiry), COOKIEPATH);
			}
				
			$totalvoters++;
			
			$poll_update='UPDATE '.$poll_table.' SET pollp_totalvoters = "'.$totalvoters.'" ,
												 pollp_totalvotes = "'.$totalvotes.'"
											 WHERE pollp_id = "'.$poll[0]['pollp_id'].'"';
			
			$wpdb->query($poll_update);
			
			$checkvoted = $logarray;
			$html.=wpcvp_display_poll_results($poll[0]['pollp_id'], $checkvoted);
			print($html);
		}
		else{
			$html=wpcvp_display_poll_results($pollid, $check_voted);
			print('<div class="wpcvp-message">'.$wpcvp_message['already_voted'].'</div>'.$html);
		}
	}

//For viewing results	
	$rid='0';
	
	$rid=$_POST['rid'];
	
	if($rid and $rid>0 ) {
		$html='';
		$check_voted = wpcvp_check_voted($rid);
		$html.=wpcvp_display_poll_results($rid, $check_voted);
		print($html);
	}
//For Submitting questions thru results page
	$raddq='0';
	
	$raddq=$_POST['raddq'];
	
	if($raddq and $raddq>0 ) {
		$sql='SELECT pollq_order from '.$questions_table.' WHERE pollq_pid='.$raddq.' ORDER BY pollq_order DESC ';
		$results=$wpdb->get_results($sql, ARRAY_A);
		$last_qorder=$results[0]['pollq_order'];
		$last_qorder++;
		//Add question to database
		$sql="INSERT INTO ".$questions_table." ( pollq_pid, pollq_order, pollq_title, pollq_desc, pollq_optional) VALUES ('".$raddq."','".$last_qorder."','".$_POST['title']."', '".$_POST['desc']."', '1' )";
		$wpdb->query($sql);
		
		$html='';
		$check_voted = wpcvp_check_voted($raddq);
		$html.=wpcvp_display_poll_results($raddq, $check_voted);
		print($html);
	}

//For 'Let me Vote' button
	$vid='0';
	
	$vid=$_POST['vid'];
	
	if($vid and $vid>0 ) {
		$html='';
		$html.=wpcvp_display_poll( $vid );
		print($html);
	}
?>