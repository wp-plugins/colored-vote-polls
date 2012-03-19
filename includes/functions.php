<?php
function wpcvp_get_polls(){
	global $wpdb,$table_prefix;
	$polls_table = $table_prefix.WPCVP_POLLS_TABLE; 
	$sql = "SELECT * FROM $polls_table ORDER BY pollp_timestamp DESC";
 	$polls = $wpdb->get_results($sql, ARRAY_A);
	return $polls;
}
function wpcvp_get_poll_id($pollid) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.WPCVP_POLLS_TABLE;
	$poll = $wpdb->get_results("SELECT * FROM $table_name WHERE pollp_id = '$pollid'", ARRAY_A);
	return $poll;
}
function wpcvp_get_questions_poll_id($pollid) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.WPCVP_QUESTIONS_TABLE;
	$questions = $wpdb->get_results("SELECT * FROM $table_name WHERE pollq_pid = '$pollid' ORDER BY pollq_order ASC", ARRAY_A);
	return $questions;
}
function wpcvp_get_answers_poll_id($pollid) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.WPCVP_ANSWERS_TABLE;
	$answers = $wpdb->get_results("SELECT * FROM $table_name WHERE polla_pid = '$pollid' ORDER BY polla_order ASC", ARRAY_A);
	return $answers;
}
function wpcvp_get_questions_qid($qid) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.WPCVP_QUESTIONS_TABLE;
	$questions = $wpdb->get_results("SELECT * FROM $table_name WHERE pollq_qid = '$qid'", ARRAY_A);
	return $questions[0];
}
function wpcvp_get_answers_aid($aid) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.WPCVP_ANSWERS_TABLE;
	$answers = $wpdb->get_results("SELECT * FROM $table_name WHERE polla_aid = '$aid'", ARRAY_A);
	return $answers[0];
}
//check if all questions in the poll are mandatory
function wpcvp_all_mandatory_questions($pollid) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.WPCVP_QUESTIONS_TABLE;
	$questions = $wpdb->get_results("SELECT * FROM $table_name WHERE pollq_pid = '$pollid' ORDER BY pollq_order ASC", ARRAY_A);
	foreach($questions as $question){
		if( $question['pollq_optional'] != '0' ){
			return false;
		}
	}	
	return true;
}
//Code referred from Lester 'GaMerZ' Chan's WP Polls WordPress Plugin
### Function: Check Voted By Cookie Or IP
function wpcvp_check_voted($poll_id) {
	global $wpcvp_options,$wpcvp_message;
	$poll_logging_method = intval($wpcvp_options['track']);
	switch($poll_logging_method) {
	// Logged By User login, Cookies And IP
		case 0:
			$check_voted_username = wpcvp_check_voted_username($poll_id);
			$check_voted_cookie = wpcvp_check_voted_cookie($poll_id);
			if( !empty($check_voted_username) and $check_voted_username != 1 ){
				return $check_voted_username;
			} elseif(!empty($check_voted_cookie)) {
				return $check_voted_cookie;
			} else {
				return wpcvp_check_voted_ip($poll_id);
			}
			break;
	// Logged By Cookie
		case 1:
			return wpcvp_check_voted_cookie($poll_id);
			break;
	// Logged By IP
		case 2:
			return wpcvp_check_voted_ip($poll_id);
			break;
	}
}

### Function: Check Voted By Username
function wpcvp_check_voted_username($poll_id) {
	global $wpdb,$table_prefix, $user_ID;
	$log_table = $table_prefix.WPCVP_LOGS_TABLE;
	// Check IP If User Is Guest
	/*if (!is_user_logged_in()) {
		return 1;
	}*/
	$pollsip_userid = intval($user_ID);
	// Check User ID From IP Logging Database
	$get_voted_aids = $wpdb->get_col("SELECT pollip_aid FROM $log_table WHERE pollip_pid = $poll_id AND pollip_userid = $pollsip_userid");
	if($get_voted_aids) {
		return $get_voted_aids;
	} else {
		return 0;
	}
}

### Function: Check Voted By Cookie
function wpcvp_check_voted_cookie($poll_id) {
	if(!empty($_COOKIE["wpcvp_voted_$poll_id"])) {
		$get_voted_aids = explode(',', $_COOKIE["wpcvp_voted_$poll_id"]);
	} else {
		$get_voted_aids = 0;
	}
	return $get_voted_aids;
}

### Function: Check Voted By IP
function wpcvp_check_voted_ip($poll_id) {
	global $wpdb,$table_prefix;
	$log_table = $table_prefix.WPCVP_LOGS_TABLE;
	// Check IP From IP Logging Database
	$get_voted_aids = $wpdb->get_col("SELECT pollip_aid FROM $log_table WHERE pollip_pid = $poll_id AND pollip_ip = '".wpcvp_get_ipaddress()."' ");
	if($get_voted_aids) {
		return $get_voted_aids;
	} else {
		return 0;
	}
}

### Function: Get IP Address
if(!function_exists('wpcvp_get_ipaddress')) {
	function wpcvp_get_ipaddress() {
		if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if(strpos($ip_address, ',') !== false) {
			$ip_address = explode(',', $ip_address);
			$ip_address = $ip_address[0];
		}
		return esc_attr($ip_address);
	}
}

//Sort the results array
function wpcvp_results_cmp($results1, $results2) {	
/*print_r($results1);
die('test');*/
  for ($i=0;$i < $results1['acount'] ;  $i++) {
    if ($results1['results'][$i]['percent'] != $results2['results'][$i]['percent']) {
      return ($results1['results'][$i]['percent'] > $results2['results'][$i]['percent']) ? -1 : 1;
    }
  }
  return ($results1['qid'] < $results2['qid']) ? -1 : 1;
}

//send email to site admin
function wpcvp_addq_send_email($data){
	$admin_email = get_option('admin_email');
	$message='';
	$message.=__("Question Title:  ","wpcvp").$data['qtitle'].'<br />'.__("Description:  ","wpcvp").$data['qdesc'];
	$url = wpcvp_admin_url( array( 'page' => 'wpcvp-add-poll' ) );
	$message.='<br />'.__("Edit this Poll :  ","wpcvp").'<a href="' . esc_attr( $url ).'&edit='.$data['pollid'].'">'. esc_attr( $url ).'&edit='.$data['pollid'] .'</a>';
	
	add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
	wp_mail($admin_email, __("New Question added to Poll ID ",'wpcvp').$data['pollid'], $message);
}

//get page ID by page slug
function wpcvp_get_page_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

function wpcvp_poll_is_closed($poll){
	if($poll['pollp_status'] == '1' ){
			return '1';
	}
	else{
		if($poll['pollp_expiry'] == '0'){
			return '0';
		}
		else{
			if ( strtotime( $poll['pollp_expiry_date'] ) >= current_time('timestamp') ) 
				return '0';
			else
				return '1';
		}
	}
	return '0';
}

function wpcvp_page_exists_by_slug($pagename)
{
	$pages = get_pages( array('post_status' => 'draft') );
	foreach ($pages as $page) {
		if ($page->post_name == $pagename) return true;
	}
	return false;
}

//Colored Vote Polls Admin URL
function wpcvp_admin_url( $query = array() ) {
	global $plugin_page;
	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;
	$path = 'admin.php';
	if ( $query = build_query( $query ) )
		$path .= '?' . $query;
	$url = admin_url( $path );
	return esc_url_raw( $url );
}
?>