<?php
function wpcvp_admin_add_poll() {
	global $wpcvp_options;

	if( isset($_POST['wpcvp_add_submit']) ) {
	
		global $wpdb,$table_prefix,$wpcvp_options;

		$poll_title=$_POST['pollp_title'];
		$poll_desc=$_POST['polldescedit'];
		
		$expiry=$_POST['pollp_expiry'];
		if( ( empty($expiry) or !isset($expiry) ) and  $expiry != '0' ){
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
			$qtitle=$_POST['pollq']['title'][$i];
			if( !empty($qtitle) ){
				$sql="INSERT INTO ".$table_prefix.WPCVP_QUESTIONS_TABLE." ( pollq_pid, pollq_order, pollq_title, pollq_desc, pollq_optional) VALUES ('".$pollid."','".$i."' ,'".$_POST['pollq']['title'][$i]."', '".$_POST['pollq']['desc'][$i]."', '".$_POST['qopt'][$i]."')";
				$wpdb->query($sql);
			}
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
		$edit=$pollid;
	}
	elseif( isset($_POST['wpcvp_edit_submit']) ){
		global $wpdb,$table_prefix,$wpcvp_options;
	
		$poll_table = $table_prefix.WPCVP_POLLS_TABLE;
		$questions_table = $table_prefix.WPCVP_QUESTIONS_TABLE;
		$answers_table = $table_prefix.WPCVP_ANSWERS_TABLE;
		
		$pollid=$_POST['pollid'];
		
		$expiry=$_POST['pollp_expiry'];
		if( ( empty($expiry) or !isset($expiry) ) and $expiry != '0' ){
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
		$edit=$pollid;
	}
	else{
		$edit=$_GET['edit'];
	}
	if( empty($edit) or !$edit or !isset($edit) ) :
?>
<div class="wrap" id="addform">
	<h2><?php _e('Add Colored Poll','wpcvp'); ?></h2>
	<!--<form name="add_poll" id="add_poll" action="<?php echo wpcvp_plugin_url('admin/addSubmit.php');?>" method="post">-->
	
		<div id="poststuff" class="metabox-holder has-right-sidebar" style="float:right;width:28%;"> 
	
			<div class="postbox"> 
			  <h3 class="hndle"><span><?php _e('About this Plugin:','wpcvp'); ?></span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://www.webfanzine.com/colored-vote-polls/" title="<?php _e('Colored Vote Polls Homepage','wpcvp'); ?>" ><?php _e('Plugin Homepage','wpcvp'); ?></a></li>
                <li><a href="http://www.lafabriquedeblogs.com/" title="<?php _e('Designed by Lafabrique De Blogs','wpcvp'); ?>
" ><?php _e('Designed by Lafabrique De Blogs','wpcvp'); ?></a></li>
				<li><a href="http://www.clickonf5.org/" title="<?php _e('Developed by Internet Techies','wpcvp'); ?>
" ><?php _e('Developed by Internet Techies','wpcvp'); ?></a></li>
                <li><a href="http://www.clickonf5.org/go/donate-wp-plugins/" title="<?php _e('Donate to support Color Vote Polls and our other free plugins','wpcvp'); ?>" ><?php _e('Donate with Paypal','wpcvp'); ?></a></li>
                </ul> 
              </div> 
			</div> 
			
			<div class="postbox"> 
			<h3 class="hndle"><span></span><?php _e('SliderVilla: WordPress Slideshow plugins','wpcvp'); ?></h3> 
     		  <div class="inside">
				<div style="margin:10px auto;">
							<a href="http://slidervilla.com/" title="Premium WordPress Slider Plugins" target="_blank"><img src="<?php echo wpcvp_plugin_url('images/slidervilla-ad1.jpg');?>" alt="Premium WordPress Slider Plugins" /></a>
				</div>
            </div></div>
			
			<div class="postbox"> 
			  <h3 class="hndle"><span></span><?php _e('Wysija: a Newsletter plugin for WordPress','wpcvp'); ?></h3> 
			  <div class="inside">
                     <div style="margin:10px auto">
                        <a href="http://www.wysija.com/" title="Free Newsletter plugin for WordPress" target="_blank"><img src="<?php echo wpcvp_plugin_url('images/wysija.jpg');?>" alt="Free Newsletter plugin for WordPress" /></a>
                     </div>
              </div></div>
			
			<div class="postbox"> 
			  <h3 class="hndle"><span></span><?php _e('Recommended Themes','wpcvp'); ?></h3> 
			  <div class="inside">
                     <div style="margin:10px 5px">
                        <a href="http://www.clickonf5.org/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank"><img src="<?php echo wpcvp_plugin_url('images/elegantthemes.gif');?>" alt="Recommended WordPress Themes" /></a>
                        <p><a href="http://www.clickonf5.org/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">Elegant Themes</a> are attractive, compatible, affordable, SEO optimized WordPress Themes and have best support in community.</p>
                        <p><strong>Beautiful themes, Great support!</strong></p>
                        <p><a href="http://www.clickonf5.org/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">For more info visit ElegantThemes</a></p>
                     </div>
              </div></div>
			  			  
	</div>

<div style="float:left;width:70%;">	
	<form name="add_poll" id="add_poll" action="<?php menu_page_url('wpcvp-add-poll');?>" method="post">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row" style="font-weight:bold;width:100px;"><label for="pollp_title"><?php _e('Title','wpcvp');?> <em>*</em></label></th>
			<td><input type="text" size="50" id="pollp_title" name="pollp_title" value="" class="required" placeholder="<?php _e('Your Title...','wpcvp');?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:40px;font-weight:bold;width:100px;"><label for="polldescedit"><?php _e('Description','wpcvp');?></label></th>
			<td>
				<div id="poststuff" style="width:500px;">
				<?php the_editor('' ,$id = 'polldescedit', $prev_id = 'title', $media_buttons = false, $tab_index = 2); ?>
				</div>
			</td>
		</tr>
	</tbody></table>
	
	<table class="wpcvpblock"></table>
	<h3><?php _e('Questions','wpcvp'); ?></h3>
	
	<table class="form-table" id="pollq_add_table" style="width:380px;">
		<tbody id="pollq_add_tbody">
		
		<?php for($i=0;$i<3;$i++) { ?>
		<tr valign="top" style="cursor:move;" id="rq<?php echo $i;?>"><td style="" ><input style="width:100%;" type="text" size="50" name="pollq[title][]" class="pollq_title" value="" title="Title..." autocomplete="off" /> <br /><textarea style="width:100%;" rows="2" cols="50" name="pollq[desc][]" class="pollq_desc"  title="Description..." autocomplete="off"></textarea> <br /><input type="hidden" id="qopt-<?php echo $i;?>" name="qopt[]" value="0" /><input class="qoptck" id="qoptck-<?php echo $i;?>" type="checkbox" name="pollq[optional][]" value="0" checked /> <label for="qoptck-<?php echo $i;?>"><?php _e('Mandatory','wpcvp'); ?></label></td><td class="dragHandle" title="Drag to reorder" >&nbsp;<input type="hidden" name="qorder[]" value="<?php echo $i;?>" /></td><td style="padding-left:2px;"><input style="text-indent:-9999px;" type="button" id="q<?php echo $i;?>" value="0" class="deleteRow" onclick="wpcvp_edit_delete_question_js(this.id);" /> <input type="hidden" value="0" name="qid[]" /></td></tr>
		<?php } ?>
	</tbody></table>
	
	<p>
			<input type="button" class="button" onclick="wpcvp_add_poll_question_js('3');" value="<?php _e('Add question','wpcvp'); ?>" /> &nbsp; </p>
	
	
	<table class="wpcvpblock"></table>
	<h3><?php _e('Answers','wpcvp'); ?></h3>
	
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_aorder"><?php _e('My answers start by','wpcvp'); ?></label></th> 
			<td><input id="pollp_aorder0" name="pollp_aorder" type="radio" value="0" <?php checked('0', '0'); ?>  /> <label for="pollp_aorder0"><?php _e('negatives and end with positives','wpcvp'); ?></label><br />
			<input id="pollp_aorder1" name="pollp_aorder" type="radio" value="1" <?php checked('1', '0'); ?>  /> <label for="pollp_aorder1"><?php _e('positives and end with negatives','wpcvp'); ?></label>
			</td> 
		</tr>
	</table>
	
	<table class="wpcvpblock"></table>
	
	<table class="form-table" id="polla_add_table" style="width:320px;margin-left:10px;">
		<tbody id="polla_add_tbody">
			<!-- Default Colors-->
			<?php for($i=0;$i<5;$i++) { ?>
			<tr valign="top" style="padding:0px;" id="ra<?php echo $i;?>" >
			<td style="padding:0px;padding-bottom:5px;">
			<input type="button" class="colorbox color_bg<?php echo $i;?> colorbox_bg<?php echo $i;?>" id="colorbox_bg<?php echo $i;?>" style="background-color:<?php echo $wpcvp_options['colors'][$i]['bg'];?>;width:20px;height:20px;" /> <input name="polla[bg][]" type="hidden" id="bg<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['bg'];?>" /> &nbsp;
			
			<input type="button" class="colorbox color_fg<?php echo $i;?> colorbox_fg<?php echo $i;?>" id="colorbox_fg<?php echo $i;?>" style="background-color:<?php echo $wpcvp_options['colors'][$i]['fg'];?>;width:20px;height:20px;" /> <input name="polla[fg][]" type="hidden" id="fg<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['fg'];?>" /> &nbsp;
			
			<input name="polla[text][]" size="25" type="text" class="color_bg<?php echo $i;?> color_t<?php echo $i;?> atext" style="background-color:<?php echo $wpcvp_options['colors'][$i]['bg'];?>;color:<?php echo $wpcvp_options['colors'][$i]['fg'];?>;" value="<?php echo $wpcvp_options['colors'][$i]['text'];?>" autocomplete="off" />
			<div class="color-picker-wrap" id="picker_bg<?php echo $i;?>"><input type="text" size=7 name="bgcval<?php echo $i;?>" id="bgcval<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['bg'];?>" /></div>
			<div class="color-picker-wrap" id="picker_fg<?php echo $i;?>"><input type="text" size=7 name="fgcval<?php echo $i;?>" id="fgcval<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['fg'];?>" /></div>

			<script type="text/javascript">
				jQuery(document).ready(function() {
					//bg
					jQuery("#picker_bg<?php echo $i;?>").farbtastic(function(color) { jQuery(".color_bg<?php echo $i;?>").css("backgroundColor",color); jQuery("#bg<?php echo $i;?>").val(color); jQuery("#bgcval<?php echo $i;?>").val(color);});
					jQuery.farbtastic("#picker_bg<?php echo $i;?>").setColor("<?php echo $wpcvp_options['colors'][$i]['bg'];?>");
					jQuery('#bgcval<?php echo $i;?>').keyup(function() {jQuery.farbtastic("#picker_bg<?php echo $i;?>").setColor( jQuery('#bgcval<?php echo $i;?>').val() );});
					jQuery("#colorbox_bg<?php echo $i;?>").click(function () {if (jQuery("#picker_bg<?php echo $i;?>").css("display") == "block") {jQuery("#picker_bg<?php echo $i;?>").fadeOut("slow"); } else { jQuery("#picker_bg<?php echo $i;?>").fadeIn("slow"); }});				
					var bg<?php echo $i;?> = false;
					jQuery(document).mousedown(function(){ jQuery('#bgcval<?php echo $i;?>').mousedown(function() {	bg<?php echo $i;?>=true;});if (bg<?php echo $i;?> == true) {return; }	jQuery("#picker_bg<?php echo $i;?>").fadeOut("slow");});
					jQuery(document).mouseup(function(){bg<?php echo $i;?> = false;});
					//fg
					jQuery("#picker_fg<?php echo $i;?>").farbtastic(function(color) { jQuery(".color_fg<?php echo $i;?>").css("backgroundColor",color); jQuery(".color_t<?php echo $i;?>").css("color",color);jQuery("#fg<?php echo $i;?>").val(color); jQuery("#fgcval<?php echo $i;?>").val(color);});
					jQuery.farbtastic("#picker_fg<?php echo $i;?>").setColor("<?php echo $wpcvp_options["colors"][$i]["fg"];?>");
					jQuery('#fgcval<?php echo $i;?>').keyup(function() {jQuery.farbtastic("#picker_fg<?php echo $i;?>").setColor( jQuery('#fgcval<?php echo $i;?>').val() );});
					jQuery("#colorbox_fg<?php echo $i;?>").click(function () { if (jQuery("#picker_fg<?php echo $i;?>").css("display") == "block") { jQuery("#picker_fg<?php echo $i;?>").fadeOut("slow"); }else {jQuery("#picker_fg<?php echo $i;?>").fadeIn("slow"); }});				
					var fg<?php echo $i;?> = false;
					jQuery(document).mousedown(function(){jQuery('#fgcval<?php echo $i;?>').mousedown(function() {fg<?php echo $i;?>=true;});if (fg<?php echo $i;?> == true) {return; }jQuery("#picker_fg<?php echo $i;?>").fadeOut("slow");});
					jQuery(document).mouseup(function(){fg<?php echo $i;?> = false;});
				});
			</script>
			</td>
			<td class="dragHandle" style="width:16px;padding:0px;padding-bottom:5px;" title="Drag to reorder" >&nbsp;<input type="hidden" name="aorder[]" value="<?php echo $i;?>" /></td>
			<td style="padding:0px;padding-left: 2px;"><input type="button" id="a<?php echo $i;?>" class="deleteRow" onclick="wpcvp_delete_answer_js(this.id);" /></td>
			</tr>
			<?php } ?>	
		</tbody>
	</table>
	
	<table class="wpcvpblock"></table>
	
	<p>	
			<input type="button" class="button" name="add_answer" onclick="wpcvp_add_poll_answer_js('5');" value="<?php _e('Add answer','wpcvp') ?>" /> 
	</p>
    <p>	<input type="button" class="button" name="reset_colors" value="<?php _e('Reset Colors','wpcvp') ?>" onclick="wpcvp_reset_colors_js(<?php echo $i;?>);" /> 
	</p>
	
	<table class="wpcvpblock"></table>
	
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:20px;"><input type="button" class="colorbox color_bgun colorbox_bgun" id="colorbox_bgun" style="background-color:#eeeeee;width:20px;height:20px;" /> <input name="pollp_unans_color" type="hidden" id="bgun" value="#eeeeee" /></th> 
			<td valign="middle"><?php _e('Color of unanswered question','wpcvp'); ?>
			</td> 
		</tr>
	</table>
	<div class="color-picker-wrap" id="picker_bgun"><input type="text" size=7 name="cval_uans" id="cval_uans" value="#eeeeee" /></div>
	
	<script type="text/javascript">
				jQuery(document).ready(function() {
					//bg
					jQuery("#picker_bgun").farbtastic(function(color) { jQuery(".color_bgun").css("backgroundColor",color); jQuery("#bgun").val(color); jQuery("#cval_uans").val(color);});
					jQuery.farbtastic("#picker_bgun").setColor("#eeeeee");
					jQuery('#cval_uans').keyup(function() {	jQuery.farbtastic("#picker_bgun").setColor( jQuery('#cval_uans').val() );});
					jQuery("#colorbox_bgun").click(function () {if (jQuery("#picker_bgun").css("display") == "block") {jQuery("#picker_bgun").fadeOut("slow"); } else { jQuery("#picker_bgun").fadeIn("slow"); }});				
					var bgun = false;
					jQuery(document).mousedown(function(){jQuery('#cval_uans').mousedown(function() {bgun=true;}); if (bgun == true) {return; }	jQuery("#picker_bgun").fadeOut("slow");});
					jQuery(document).mouseup(function(){bgun = false;});
				});
	</script>
	
	<table class="wpcvpblock"></table>
	
	<h3><?php _e('Settings','wpcvp'); ?></h3>
	
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_status"><?php _e('Status','wpcvp'); ?></label></th> 
			<td><input id="pollp_status0" name="pollp_status" type="radio" value="0" <?php checked('0', '0'); ?>  /> <label for="pollp_status0"><?php _e('open','wpcvp'); ?></label><br />
			<input id="pollp_status1" name="pollp_status" type="radio" value="1" <?php checked('1', '0'); ?>  /> <label for="pollp_status1"><?php _e('closed','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_expiry_date"><?php _e('Closing vote date','wpcvp'); ?></label></th> 
			<td><input id="pollp_expiry" name="pollp_expiry" type="checkbox" value="0" <?php checked('0', '0'); ?>  onclick="wpcvp_expiry_date_js()" /> <label for="pollp_expiry"><?php _e('Never','wpcvp'); ?></label><br />
			<div id="expiry_date"><input size="12" name="pollp_expiry_date" type="text" id="pollp_expiry_date" placeholder="<?php _e('yyyy/dd/mm','wpcvp'); ?>" style="margin-right:5px;" /> <input name="pollp_actual_expiry_date" type="hidden" id="pollp_actual_expiry_date" value="<?php echo date('Y/m/d');?>" /></div>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_adorder"><?php _e('Display results by...','wpcvp'); ?></label></th> 
			<td><input id="pollp_adorder0" name="pollp_adorder" type="radio" value="0" <?php checked('0', '0'); ?>  /> <label for="pollp_adorder0"><?php _e('questions with most positive answers first','wpcvp'); ?></label><br />
			<input id="pollp_adorder1" name="pollp_adorder" type="radio" value="1" <?php checked('1', '0'); ?>  /> <label for="pollp_adorder1"><?php _e('questions with most negative answers first','wpcvp'); ?></label><br />
			<input id="pollp_adorder2" name="pollp_adorder" type="radio" value="2" <?php checked('2', '0'); ?>  /> <label for="pollp_adorder2"><?php _e('original order of questions','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><?php _e('Allow to view results without voting','wpcvp'); ?></th> 
			<td><input id="pollp_vresult" name="pollp_vresult" type="checkbox" value="1" <?php checked('1', '0'); ?>  /> <label for="pollp_vresult"><?php _e('Yes','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<!--<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_url"><?php _e('Poll\'s URL','wpcvp'); ?></label></th> 
			<td><input id="pollp_url" size="70" name="pollp_url" class="url" type="text" value="" /></td> 
		</tr>-->
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><?php _e('Allow visitors to add questions?','wpcvp'); ?></th> 
			<td><input id="pollp_aq" name="pollp_aq" type="checkbox" value="1" <?php checked('1', '0'); ?>  /> <label for="pollp_aq"><?php _e('Yes','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<?php //$categories = get_categories(); ?>		
		<!--<tr valign="top">
			<th scope="row"><?php _e('Category','listic-slider'); ?></th>
			<td><select name="pollp_cat" >
			<option value=""><?php _e('Select a category','listic-slider'); ?></option>
			<?php if($categories) { 
			foreach($categories as $category) {?>
				<option value="<?php echo $category->slug;?>"><?php echo $category->slug;?></option>
			<?php } } ?>
			</select>
			</td>
		</tr>-->
		
	</table>
	
	<table class="wpcvpblock"></table>
	
	<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
	</p>
	<input type='hidden' name='wpcvp_add_submit' />
	
	<!--<div id="saveResult"></div>
	
	<?php if( $pollid and !empty($pollid) ) { $displaysc='style="display:block;"'; }
		  else {$displaysc='style="display:none;"';}?>

	<div id="edit_bottom"></div>
	<div id="poll_shortcode" <?php echo $displaysc;?>>
		<h3><?php _e('Add this poll to your article','wpcvp'); ?></h3>
		<p><?php _e('Copy and paste the "shortcode" below into any page or article you  wish','wpcvp'); ?></p>
		<code>[colorvote id="<span id="pollid"><?php echo $pollid;?></span>" style="wpcvp-poll"]</code>
	</div>-->
	
	<table class="wpcvpblock"></table>
	
	</form>
	</div>
</div>
<?php 
else:
$pollid=$edit;
$poll=wpcvp_get_poll_id($pollid);
$questions=wpcvp_get_questions_poll_id($pollid);
$answers=wpcvp_get_answers_poll_id($pollid);
 ?>
<div class="wrap" id="editform">
	<h2><?php _e('Edit Colored Poll - ID '.$pollid,'wpcvp'); ?></h2>
	
		<div id="poststuff" class="metabox-holder has-right-sidebar" style="float:right;width:28%;"> 
	
			<div class="postbox"> 
			  <h3 class="hndle"><span><?php _e('About this Plugin:','wpcvp'); ?></span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://www.webfanzine.com/colored-vote-polls/" title="<?php _e('Colored Vote Polls Homepage','wpcvp'); ?>" ><?php _e('Plugin Homepage','wpcvp'); ?></a></li>
                <li><a href="http://www.lafabriquedeblogs.com/" title="<?php _e('Designed by Lafabrique De Blogs','wpcvp'); ?>
" ><?php _e('Designed by Lafabrique De Blogs','wpcvp'); ?></a></li>
				<li><a href="http://www.clickonf5.org/" title="<?php _e('Developed by Internet Techies','wpcvp'); ?>
" ><?php _e('Developed by Internet Techies','wpcvp'); ?></a></li>
                <li><a href="http://www.clickonf5.org/go/donate-wp-plugins/" title="<?php _e('Donate to support Color Vote Polls and our other free plugins','wpcvp'); ?>" ><?php _e('Donate with Paypal','wpcvp'); ?></a></li>
                </ul> 
              </div> 
			</div> 
			
			<div class="postbox"> 
			<h3 class="hndle"><span></span><?php _e('SliderVilla: WordPress Slideshow plugins','wpcvp'); ?></h3> 
     		  <div class="inside">
				<div style="margin:10px auto;">
							<a href="http://slidervilla.com/" title="Premium WordPress Slider Plugins" target="_blank"><img src="<?php echo wpcvp_plugin_url('images/slidervilla-ad1.jpg');?>" alt="Premium WordPress Slider Plugins" /></a>
				</div>
            </div></div>
			
			<div class="postbox"> 
			  <h3 class="hndle"><span></span><?php _e('Wysija: a Newsletter plugin for WordPress','wpcvp'); ?></h3> 
			  <div class="inside">
                     <div style="margin:10px auto">
                        <a href="http://www.wysija.com/" title="Free Newsletter plugin for WordPress" target="_blank"><img src="<?php echo wpcvp_plugin_url('images/wysija.jpg');?>" alt="Free Newsletter plugin for WordPress" /></a>
                     </div>
              </div></div>
			
			<div class="postbox"> 
			  <h3 class="hndle"><span></span><?php _e('Recommended Themes','wpcvp'); ?></h3> 
			  <div class="inside">
                     <div style="margin:10px 5px">
                        <a href="http://www.clickonf5.org/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank"><img src="<?php echo wpcvp_plugin_url('images/elegantthemes.gif');?>" alt="Recommended WordPress Themes" /></a>
                        <p><a href="http://www.clickonf5.org/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">Elegant Themes</a> are attractive, compatible, affordable, SEO optimized WordPress Themes and have best support in community.</p>
                        <p><strong>Beautiful themes, Great support!</strong></p>
                        <p><a href="http://www.clickonf5.org/go/elegantthemes/" title="Recommended WordPress Themes" target="_blank">For more info visit ElegantThemes</a></p>
                     </div>
              </div></div>
			  			  
	</div>

<div style="float:left;width:70%;">	
	<form name="add_poll" id="add_poll" action="<?php menu_page_url('wpcvp-add-poll');?>&edit=<?php echo $pollid;?>" method="post">	
	<input type="hidden" name="pollid" value="<?php echo $pollid; ?>" />
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row" style="font-weight:bold;width:100px;"><label for="pollp_title"><?php _e('Title','wpcvp');?> <em>*</em></label></th>
			<td><input type="text" size="50" id="pollp_title" name="pollp_title" value="<?php echo $poll[0]['pollp_title'];?>" class="required" placeholder="<?php _e('Your Title...','wpcvp');?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row" style="padding-top:40px;font-weight:bold;width:100px;"><label for="polldescedit"><?php _e('Description','wpcvp');?></label></th>
			<td>
				<div id="poststuff" style="width:500px;">
				<?php the_editor( $poll[0]['pollp_desc'] ,$id = 'polldescedit', $prev_id = 'title', $media_buttons = false, $tab_index = 2); ?>
				</div>
			</td>
		</tr>
	</tbody></table>
	
	<table class="wpcvpblock"></table>
	<h3><?php _e('Questions','wpcvp'); ?></h3>
	
	<table class="form-table" id="pollq_add_table" style="width:380px;">
		<tbody id="pollq_add_tbody">
		
		<?php $i=0;foreach($questions as $question) { ?>
		
		<tr valign="top" style="cursor:move;" id="rq<?php echo $i;?>"><td><input style="width:100%;" type="text" size="50" name="pollq[title][]" class="pollq_title" value="<?php echo $question['pollq_title'];?>" title="Title..." autocomplete="off" /> <br /><textarea style="width:100%;" rows="2" cols="50" name="pollq[desc][]" class="pollq_desc"  title="Description..." autocomplete="off"><?php echo $question['pollq_desc'];?></textarea><br /><input type="hidden" id="qopt-<?php echo $i;?>" name="qopt[]" value="<?php echo $question['pollq_optional'];?>" /><input class="qoptck" id="qoptck-<?php echo $i;?>" type="checkbox" name="pollq[optional][]" value="0" <?php checked('0', $question['pollq_optional'] ); ?> /> <label for="qoptck-<?php echo $i;?>"><?php _e('Mandatory','wpcvp'); ?></label></td><td class="dragHandle" title="Drag to reorder" >&nbsp;<input type="hidden" name="qorder[]" value="<?php echo $i;?>" /></td><td style="padding-left:2px;"><input style="text-indent:-99999px;" type="button" id="q<?php echo $i;?>" value="<?php echo $question['pollq_qid'];?>" class="deleteRow" onclick="wpcvp_edit_delete_question_js(this.id);" /> <input type="hidden" value="<?php echo $question['pollq_qid'];?>" name="qid[]" /></td></tr>
		
		<?php $i++; } ?>
	</tbody></table>
	
	<p>
			<input type="hidden" id="qid_del" name="qid_del" value="" /><input type="button" class="button" onclick="wpcvp_add_poll_question_js(<?php echo $i;?>);" value="<?php _e('Add question','wpcvp'); ?>" />
	</p>
	
	
	<table class="wpcvpblock"></table>
	<h3><?php _e('Answers','wpcvp'); ?></h3>
	
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_aorder"><?php _e('My answers start by','wpcvp'); ?></label></th> 
			<td><input id="pollp_aorder0" name="pollp_aorder" type="radio" value="0" <?php checked('0', $poll[0]['pollp_aorder'] ); ?>  /> <label for="pollp_aorder0"><?php _e('negatives and end with positives','wpcvp'); ?></label><br />
			<input id="pollp_aorder1" name="pollp_aorder" type="radio" value="1" <?php checked('1', $poll[0]['pollp_aorder'] ); ?>  /> <label for="pollp_aorder1"><?php _e('positives and end with negatives','wpcvp'); ?></label>
			</td> 
		</tr>
	</table>
	
	<table class="wpcvpblock"></table>
	
	<table class="form-table" id="polla_add_table" style="width:320px;margin-left:10px;">
		<tbody id="polla_add_tbody">
			<!-- Default Colors-->
			<?php $i=0;foreach($answers as $answer) { ?>
			<tr valign="top" style="padding:0px;" id="ra<?php echo $i;?>" >
			<td style="padding:0px;padding-bottom:5px;">
			<input type="button" class="colorbox color_bg<?php echo $i;?> colorbox_bg<?php echo $i;?>" id="colorbox_bg<?php echo $i;?>" style="background-color:<?php echo $answer['polla_bg'];?>;width:20px;height:20px;" /> <input name="polla[bg][]" type="hidden" id="bg<?php echo $i;?>" value="<?php echo $answer['polla_bg'];?>" /> &nbsp;
			
			<input type="button" class="colorbox color_fg<?php echo $i;?> colorbox_fg<?php echo $i;?>" id="colorbox_fg<?php echo $i;?>" style="background-color:<?php echo $answer['polla_fg'];?>;width:20px;height:20px;" /> <input name="polla[fg][]" type="hidden" id="fg<?php echo $i;?>" value="<?php echo $answer['polla_fg'];?>" /> &nbsp;
			
			<input name="polla[text][]" size="25" type="text" class="color_bg<?php echo $i;?> color_t<?php echo $i;?> atext" style="background-color:<?php echo $answer['polla_bg'];?>;color:<?php echo $answer['polla_fg'];?>;" value="<?php echo $answer['polla_answer'];?>" autocomplete="off" />
			<div class="color-picker-wrap" id="picker_bg<?php echo $i;?>"><input type="text" size=7 name="bgcval<?php echo $i;?>" id="bgcval<?php echo $i;?>" value="<?php echo $answer['polla_bg'];?>" /></div>
			<div class="color-picker-wrap" id="picker_fg<?php echo $i;?>"><input type="text" size=7 name="fgcval<?php echo $i;?>" id="fgcval<?php echo $i;?>" value="<?php echo $answer['polla_fg'];?>" /></div>

			<script type="text/javascript">
				jQuery(document).ready(function() {
					//bg
					jQuery("#picker_bg<?php echo $i;?>").farbtastic(function(color) { jQuery(".color_bg<?php echo $i;?>").css("backgroundColor",color); jQuery("#bg<?php echo $i;?>").val(color); jQuery("#bgcval<?php echo $i;?>").val(color);});
					jQuery.farbtastic("#picker_bg<?php echo $i;?>").setColor("<?php echo $answer['polla_bg'];?>");
					jQuery('#bgcval<?php echo $i;?>').keyup(function() {jQuery.farbtastic("#picker_bg<?php echo $i;?>").setColor( jQuery('#bgcval<?php echo $i;?>').val() );});
					jQuery("#colorbox_bg<?php echo $i;?>").click(function () {if (jQuery("#picker_bg<?php echo $i;?>").css("display") == "block") {jQuery("#picker_bg<?php echo $i;?>").fadeOut("slow"); } else { jQuery("#picker_bg<?php echo $i;?>").fadeIn("slow"); }});				
					var bg<?php echo $i;?> = false;
					jQuery(document).mousedown(function(){ jQuery('#bgcval<?php echo $i;?>').mousedown(function() {	bg<?php echo $i;?>=true;	}); if (bg<?php echo $i;?> == true) {return; }	jQuery("#picker_bg<?php echo $i;?>").fadeOut("slow");});
					jQuery(document).mouseup(function(){bg<?php echo $i;?> = false;});
					//fg
					jQuery("#picker_fg<?php echo $i;?>").farbtastic(function(color) { jQuery(".color_fg<?php echo $i;?>").css("backgroundColor",color); jQuery(".color_t<?php echo $i;?>").css("color",color);jQuery("#fg<?php echo $i;?>").val(color); jQuery("#fgcval<?php echo $i;?>").val(color);});
					jQuery.farbtastic("#picker_fg<?php echo $i;?>").setColor("<?php echo $answer['polla_fg'];?>");
					jQuery('#fgcval<?php echo $i;?>').keyup(function() {jQuery.farbtastic("#picker_fg<?php echo $i;?>").setColor( jQuery('#fgcval<?php echo $i;?>').val() );});
					jQuery("#colorbox_fg<?php echo $i;?>").click(function () { if (jQuery("#picker_fg<?php echo $i;?>").css("display") == "block") { jQuery("#picker_fg<?php echo $i;?>").fadeOut("slow"); }else {jQuery("#picker_fg<?php echo $i;?>").fadeIn("slow"); }});				
					var fg<?php echo $i;?> = false;
					jQuery(document).mousedown(function(){jQuery('#fgcval<?php echo $i;?>').mousedown(function() {fg<?php echo $i;?>=true;});if (fg<?php echo $i;?> == true) {return; }jQuery("#picker_fg<?php echo $i;?>").fadeOut("slow");});
					jQuery(document).mouseup(function(){fg<?php echo $i;?> = false;});
				});
			</script>
			</td>
			<td class="dragHandle" style="width:16px;padding:0px;padding-bottom:5px;" title="Drag to reorder" >&nbsp;<input type="hidden" name="aorder[]" value="<?php echo $i;?>" /></td>
			<td style="padding:0px;padding-left: 2px;"><input type="button" id="a<?php echo $i;?>" class="deleteRow" value="<?php echo $answer['polla_aid'];?>" style="text-indent:-99999px;" onclick="wpcvp_edit_delete_answer_js(this.id);" /><input type="hidden" value="<?php echo $answer['polla_aid'];?>" name="aid[]" /></td>
			</tr>
			<?php $i++; } ?>	
		</tbody>
	</table>
	
	<table class="wpcvpblock"></table>
	
	<p>	
			<input type="hidden" id="aid_del" name="aid_del" value="" /><input type="button" class="button" name="add_answer" onclick="wpcvp_add_poll_answer_js(<?php echo $i;?>);" value="<?php _e('Add answer') ?>" /> </p>
	<p><input type="button" class="button" name="reset_colors" value="<?php _e('Reset Colors') ?>" onclick="wpcvp_reset_colors_js(<?php echo $i;?>);" /> 
	</p>
	
	<table class="wpcvpblock"></table>
	
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:20px;"><input type="button" class="colorbox color_bgun colorbox_bgun" id="colorbox_bgun" style="background-color:<?php echo $poll[0]['pollp_unans_color'];?>;width:20px;height:20px;" /> <input name="pollp_unans_color" type="hidden" id="bgun" value="<?php echo $poll[0]['pollp_unans_color'];?>" /></th> 
			<td valign="middle"><?php _e('Color of unanswered question','wpcvp'); ?> 
			</td> 
		</tr>
	</table>
	<div class="color-picker-wrap" id="picker_bgun"><input type="text" size=7 name="cval_uans" id="cval_uans" value="<?php echo $poll[0]['pollp_unans_color'];?>" /></div>
	
	<script type="text/javascript">
				jQuery(document).ready(function() {
					//bg
					jQuery("#picker_bgun").farbtastic(function(color) {jQuery(".color_bgun").css("backgroundColor",color); jQuery("#bgun").val(color); jQuery("#cval_uans").val(color); });
					jQuery.farbtastic("#picker_bgun").setColor("<?php echo $poll[0]['pollp_unans_color'];?>");
					jQuery('#cval_uans').keyup(function() {	jQuery.farbtastic("#picker_bgun").setColor( jQuery('#cval_uans').val() );});
					jQuery("#colorbox_bgun").click(function () {if (jQuery("#picker_bgun").css("display") == "block") {jQuery("#picker_bgun").fadeOut("slow"); } else { jQuery("#picker_bgun").fadeIn("slow"); }});	
					var bgun = false;
					jQuery(document).mousedown(function(){ jQuery('#cval_uans').mousedown(function() {	bgun=true;	});	if (bgun == true) {return; } jQuery("#picker_bgun").fadeOut("slow");});
					jQuery(document).mouseup(function(){bgun = false;});				
				});
	</script>
	
	<table class="wpcvpblock"></table>
	
	<h3><?php _e('Settings','wpcvp'); ?></h3>
	
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_status"><?php _e('Status','wpcvp'); ?></label></th> 
			<td><input id="pollp_status0" name="pollp_status" type="radio" value="0" <?php checked('0', $poll[0]['pollp_status'] ); ?>  /> <label for="pollp_status0"><?php _e('open','wpcvp'); ?></label><br />
			<input id="pollp_status1" name="pollp_status" type="radio" value="1" <?php checked('1', $poll[0]['pollp_status'] ); ?>  /> <label for="pollp_status1"><?php _e('closed','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_expiry_date"><?php _e('Closing vote date','wpcvp'); ?></label></th> 
			<td><input id="pollp_expiry" name="pollp_expiry" type="checkbox" value="0" <?php checked('0', $poll[0]['pollp_expiry'] ); ?>  onclick="wpcvp_expiry_date_js()" /> <label for="pollp_expiry"><?php _e('Never','wpcvp'); ?></label><br />
			<div id="expiry_date"><input size="12" name="pollp_expiry_date" type="text" id="pollp_expiry_date" placeholder="<?php _e('yyyy/dd/mm','wpcvp'); ?>" style="margin-right:5px;" value="<?php echo date('Y/d/m',strtotime($poll[0]['pollp_expiry_date']) );?>" /> <input name="pollp_actual_expiry_date" type="hidden" id="pollp_actual_expiry_date" value="<?php echo $poll[0]['pollp_expiry_date'];?>" /></div>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_adorder"><?php _e('Display results by...','wpcvp'); ?></label></th> 
			<td><input id="pollp_adorder0" name="pollp_adorder" type="radio" value="0" <?php checked('0', $poll[0]['pollp_adorder'] ); ?>  /> <label for="pollp_adorder0"><?php _e('questions with most positive answers first','wpcvp'); ?></label><br />
			<input id="pollp_adorder1" name="pollp_adorder" type="radio" value="1" <?php checked('1', $poll[0]['pollp_adorder']  ); ?>  /> <label for="pollp_adorder1"><?php _e('questions with most negative answers first','wpcvp'); ?></label><br />
			<input id="pollp_adorder2" name="pollp_adorder" type="radio" value="2" <?php checked('2', $poll[0]['pollp_adorder']  ); ?>  /> <label for="pollp_adorder2"><?php _e('original order of questions','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><?php _e('Allow to view results without voting','wpcvp'); ?></th> 
			<td><input id="pollp_vresult" name="pollp_vresult" type="checkbox" value="1" <?php checked('1', $poll[0]['pollp_vresult'] ); ?>  /> <label for="pollp_vresult"><?php _e('Yes','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<!--<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="pollp_url"><?php _e('Poll\'s URL','wpcvp'); ?></label></th> 
			<td><input id="pollp_url" size="70" name="pollp_url" class="url" type="text" value="<?php echo $poll[0]['pollp_url']; ?>" /></td> 
		</tr>-->
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><?php _e('Allow visitors to add questions?','wpcvp'); ?></th> 
			<td><input id="pollp_aq" name="pollp_aq" type="checkbox" value="1" <?php checked('1', $poll[0]['pollp_aq'] ); ?>  /> <label for="pollp_aq"><?php _e('Yes','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<?php //$categories = get_categories(); ?>		
		<!--<tr valign="top">
			<th scope="row"><?php _e('Category','listic-slider'); ?></th>
			<td><select name="pollp_cat" >
			<option value=""><?php _e('Select a category','listic-slider'); ?></option>
			<?php if($categories) {
			foreach($categories as $category) {
				$selected='';
				if( $poll[0]['pollp_cat'] == $category->slug) {
					$selected='selected';
				}
			?>
				<option value="<?php echo $category->slug;?>" <?php echo $selected;?>><?php echo $category->slug;?></option>
			<?php } } ?>
			</select>
			</td>
		</tr>-->
		
	</table>
	
	<table class="wpcvpblock"></table>
	
	<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
	</p>
	<input type='hidden' name='wpcvp_edit_submit' />
	
	<div id="saveResult"></div>
	
	<?php if( $pollid and !empty($pollid) ) { $displaysc='style="display:block;"'; }
		  else {$displaysc='style="display:none;"';}?>

	<div id="edit_bottom"></div>
	<div id="poll_shortcode" <?php echo $displaysc;?>>
		<h3><?php _e('Add this poll to your article','wpcvp'); ?></h3>
		<p><?php _e('Copy and paste the "shortcode" below into any page or article you  wish','wpcvp'); ?></p>[colorvote id="<?php echo $pollid;?>" style="wpcvp-poll"]</div>
	
	<table class="wpcvpblock"></table>
	
	</form>
	</div>
</div>
<?php
endif;
}
?>