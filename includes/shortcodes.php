<?php 
function wpcvp_shortcode($atts,$content){
  global $wpcvp_message;
  extract(shortcode_atts(array(
	        'type' => 'poll',
			'id' => '',
			'limit'=>'10',
			'category'=>'',
			'sort'=>'votes',
			'style'=>'wpcvp-poll'
	), $atts));
	
	if( is_page('wpcvp-preview') ){
		$pollid=$_GET['pollid'];
		if($pollid and !empty($pollid) and $pollid > 0 ){
			$id=$pollid;
		}
	}
	
	if( $type=='poll' and !empty($id) and $id>0 ) {
		$poll=wpcvp_get_poll_id( $id );
		
		$display='<div class="'.$style.' wpcvp-wrap" id="wpcvp-'.$id.'">';
		
		$poll_closed='0';
		$poll_closed=wpcvp_poll_is_closed( $poll[0] );
		$check_voted = wpcvp_check_voted($id);
		
		if($poll_closed=='1' or $check_voted != 0) {
			if($poll_closed=='1') $display.='<div class="wpcvp-message">'.$wpcvp_message['poll_closed'].'</div>';
			if($check_voted != 0 ) $display.='<div class="wpcvp-message">'.$wpcvp_message['already_voted'].'</div>';
			$display.=wpcvp_display_poll_results( $id, $check_voted );
		}
		else {
			$display.=wpcvp_display_poll( $id );
		}
		$display.='</div>';	
	}	
  
  return $display;
}
add_shortcode('colorvote', 'wpcvp_shortcode');

function wpcvp_display_poll( $id ){
	global $wpcvp_options,$wpcvp_message;
	$poll=wpcvp_get_poll_id( $id );
	$questions=wpcvp_get_questions_poll_id($id);
	$answers=wpcvp_get_answers_poll_id($id);
	$count=count($answers);
	if($count=='0') $count='1';
	
	$tdwidth=floor( 100 / $count );
	
	$html='<form class="wpcvp" name="wpcvp-'.$poll[0]['pollp_id'].' action="" method="post">';
	$html.='<h2>'.$poll[0]['pollp_title'].'</h2>';
	$html.='<div class="pdesc">'.$poll[0]['pollp_desc'].'</div>';
	
	$html.='<div class="poll-wrap">';
	$i=0;
	foreach( $questions as $question ){
		if($question['pollq_optional']=='0'){
			$mandatory='1';
			$mhtml=' <em title="'.__('This Question is Mandatory','wpcvp').'">&#42;</em>';
		}
		else{
			$mandatory='0';
			$mhtml='';
		}
		$html.='<div class="qdesc'.$question['pollq_qid'].'"><h3>'.$question['pollq_title'].$mhtml.'</h3>';
		$html.='<span class="qdesc">'.$question['pollq_desc'].'</span></div>';
		$html.='<table><tr>';
		$j=0;
		foreach($answers as $answer ){
			$aqid_validate='';
			//if($mandatory=='1' and $j==0){
			if($mandatory=='1'){
				$aqid_validate=' rel="required" ';
			}
			$html.='<td style="width:'.$tdwidth.'%;background-color:'.$answer['polla_bg'].';color:'.$answer['polla_fg'].';"><input name="aqid'.$question['pollq_qid'].'" class="aqid" '.$aqid_validate.' id="radio-'.$question['pollq_qid'].'-'.$answer['polla_aid'].'" alt="'.$question['pollq_qid'].'" type="radio" value="'.$answer['polla_aid'].'" /> <small> &nbsp;<label style="color:'.$answer['polla_fg'].';" class="wpcvp-label" for="radio-'.$question['pollq_qid'].'-'.$answer['polla_aid'].'">'.$answer['polla_answer'].'</label></small></td>';
			$j++;
		}
		$html.='</tr></table><input type="hidden" id="qid'.$i.'" class="qid" value="'.$question['pollq_qid'].'" />';
		$i++;
		
	}	
	$html.='<input type="hidden" class="cvpcount" id="cvpcount-'.$poll[0]['pollp_id'].'" value="'.$i.'-'.$j.'" />';
	$html.='<input type="hidden" class="wpcvpqi" value="'.$i.'" /></div>';
	
	$aqbutton='';$aqform='';
	if( $poll[0]['pollp_aq']=='1' ){
		$aqbutton='<input type="button" id="showaq-'.$poll[0]['pollp_id'].'" class="showaq button" value="'.__('Add your own question','wpcvp').'" />';
		$aqform='<div class="wpcvp-addq">
			<h4 class="addqh4">'.__('Add your question','wpcvp').'</h4>
			<p><input type="text" size="50" class="pollq_title" value="" title="'.__('Your question','wpcvp').'" autocomplete="off" /><br />
			<textarea rows="2" cols="50" class="pollq_desc" title="'.__('Description...','wpcvp').'" autocomplete="off"></textarea><br />
			<input type="button" class="addq" id="addq-'.$poll[0]['pollp_id'].'" value="'.__('Submit','wpcvp').'" /></p>
		</div>';
	}
	
	$vbutton='';
	if( $wpcvp_options['permission'] == '1' ){
		if( is_user_logged_in() ) $vbutton='<input type="button" class="button wpcvp_vote" id="v-'.$poll[0]['pollp_id'].'" value="'.__('Vote!','wpcvp').'" />';
		else $vbutton='<div class="wpcvp-message">'.$wpcvp_message['reg_user_vote'].'</div>';
	}
	else{
		$vbutton='<input type="button" class="button wpcvp_vote" id="v-'.$poll[0]['pollp_id'].'" value="'.__('Vote!','wpcvp').'" />';
	}
	
	$html.=$aqbutton.$vbutton.'<a class="wpcvp_results" id="a-'.$poll[0]['pollp_id'].'" onclick="wpcvp_results_click(this);return false;">'.__('Show me the results','wpcvp').'</a>
	'.$aqform.'
	</form>
	<div id="saveResult"></div>';
	return $html;
}

function wpcvp_display_poll_results( $id, $check_voted=0 ){
	
	global $wpdb, $table_prefix;
	$log_table = $table_prefix.WPCVP_LOGS_TABLE;
	
	$poll=wpcvp_get_poll_id( $id );
	$questions=wpcvp_get_questions_poll_id($id);
	$answers=wpcvp_get_answers_poll_id($id);
	
	if(  ($poll[0]['pollp_aorder']=='0' and $poll[0]['pollp_adorder']=='0') or ($poll[0]['pollp_aorder']=='1' and $poll[0]['pollp_adorder']=='1') ){
		$answers=array_reverse($answers);
	}
	
	$counta=count($answers);
	
	if($counta=='0') $counta='1';
	//+1 for unanswered question
	if( !wpcvp_all_mandatory_questions($id) ){
		$tdwidth=floor( 100 / ($counta + 1) );
	}
	else{
		$tdwidth=floor( 100 / $counta );
	}
	
	$html='';
	$html='<form class="wpcvp" name="wpcvp-'.$poll[0]['pollp_id'].' action="" method="post">';
	$html.='<h2>'.$poll[0]['pollp_title'].'</h2>';
	$html.='<div class="pdesc">'.$poll[0]['pollp_desc'].'</div>';
	$html.='<div class="poll-wrap">';
	$html.='<table><tr>';
		foreach($answers as $answer ){
			$html.='<td style="width:'.$tdwidth.'%;background-color:'.$answer['polla_bg'].';color:'.$answer['polla_fg'].';"><small> &nbsp;'.$answer['polla_answer'].'</small></td>';
		}
	if( !wpcvp_all_mandatory_questions($id) ){
		$html.='<td style="width:'.$tdwidth.'%;background-color:'.$poll[0]['pollp_unans_color'].';color:#000;"><small> &nbsp;'.__('No vote','wpcvp').'</small></td>';
	}
	$html.='</tr></table>';
	
	$html.='<h3 class="totalh3">'.__('Total voters: ','wpcvp').$poll[0]['pollp_totalvoters'].'</h3>';

	$results=array();
	$q=0;
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
		if($check_voted != 0){
			$result['votedaid']=$check_voted[$q];
		}
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
			
			$result['results'][$i]['aid']=$answer['polla_aid'];
			$result['results'][$i]['avotes']=$aqvotes;
			$result['results'][$i]['bg']=$answer['polla_bg'];
			$result['results'][$i]['fg']=$answer['polla_fg'];
			$result['results'][$i]['tdstr']=$tdwidth_str;
			$result['results'][$i]['percent']=$tdwidth;

			$i++;	
		}
		
		//for non-mandatory question
		if($question['pollq_optional']!='0'){
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
		}
		
		array_push($results,$result);
		$q++;
	}
	unset($result);
	
	//sort if necessary
	if( $poll[0]['pollp_adorder']=='0' or $poll[0]['pollp_adorder']=='1' ){
		usort($results, 'wpcvp_results_cmp') ;
	}
	//echo count($results);	print_r($results);		die('tset');
	
	foreach($results as $result){
		$html.='<h3>'.$result['qtitle'].'</h3>';
		$html.='<span class="qdesc">'.$result['qdesc'].'</span>';
		$html.='<table '.$result['twidth'].'><tr>';
		
		$acount=$result['acount'];
		for($j=0;$j<$acount;$j++){
			if( $result['votedaid'] == $result['results'][$j]['aid'] ) $votedanswercss='font-weight:bold;';
			else $votedanswercss='';
			$html.='<td title="'.$result['results'][$j]['avotes'].__(' votes','wpcvp').'" style="width:'.$result['results'][$j]['percent'].'%;background-color:'.$result['results'][$j]['bg'].';color:'.$result['results'][$j]['fg'].';'.$votedanswercss.'"><small>&nbsp;&nbsp;'.$result['results'][$j]['tdstr'].'&nbsp;&nbsp;</small></td>';
		}
		$html.='</tr></table>';
	}
	$html.='<input type="hidden" class="wpcvpqi" value="'.$q.'" /></div>';
	$aqbutton='';$aqform='';
	if( $poll[0]['pollp_aq']=='1' ){
		$aqbutton='<input type="button" id="showaq-'.$poll[0]['pollp_id'].'" class="showaq button" value="'.__('Add your own question','wpcvp').'" />';
		$aqform='<div class="wpcvp-addq">
			<h4 class="addqh4">'.__('Add your question','wpcvp').'</h4>
			<p><input type="text" size="50" class="pollq_title" value="" title="'.__('Your question','wpcvp').'" autocomplete="off" /><br />
			<textarea rows="2" cols="50" class="pollq_desc" title="'.__('Description...','wpcvp').'" autocomplete="off"></textarea><br />
			<input type="button" class="addq" id="addq-'.$poll[0]['pollp_id'].'" value="'.__('Submit','wpcvp').'" /></p>
		</div>';
	}
	
	$poll_closed=wpcvp_poll_is_closed( $poll[0] );
	if($poll_closed == '0' and $check_voted == 0 ){
		$html.=$aqbutton.'<a class="wpcvp_vote_button" id="vb-'.$poll[0]['pollp_id'].'" onclick="wpcvp_vote_button_click(this);return false;">'.__('Let me Vote!','wpcvp').'</a>'.$aqform.'</form>';
	}
	
	return $html;

}
?>