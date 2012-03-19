<?php // Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'wpcvp_menu');
  add_action( 'admin_init', 'register_wpcvp_settings' ); 
} 

// function for adding settings page to wp-admin
function wpcvp_menu() {
    // Add a new submenu under Options:
	add_menu_page( __('Colored Votes','wpcvp'), __('Colored Votes','wpcvp'), 'manage_options','wpcvp-admin', 'wpcvp_admin_polls' );
	add_submenu_page('wpcvp-admin', __('Colored Vote Polls','wpcvp'), __('Polls','wpcvp'), 'manage_options', 'wpcvp-admin', 'wpcvp_admin_polls');
	$edit=$_GET['edit'];
	
	if( ( empty($edit) or !$edit or !isset($edit) ) and !isset($_POST['wpcvp_add_submit']) ) add_submenu_page('wpcvp-admin', __('Add Colored Poll','wpcvp'), __('Add New','wpcvp'), 'manage_options', 'wpcvp-add-poll', 'wpcvp_admin_add_poll');
	else add_submenu_page('wpcvp-admin', __('Edit Colored Poll','wpcvp'), __('Add New','wpcvp'), 'manage_options', 'wpcvp-add-poll', 'wpcvp_admin_add_poll');
	add_submenu_page('wpcvp-admin', __('Colored Vote Polls Settings','wpcvp'), __('Settings','wpcvp'), 'manage_options', 'wpcvp-settings', 'wpcvp_admin_settings');
}

include('polls.php');
include('add.php');
include('settings.php');

//admin settings
function wpcvp_admin_scripts() {
global $wpcvp_options;
$nonce= wp_create_nonce('colorvotepoll');
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('wpcvp-add-poll' == $_GET['page'] or 'wpcvp-settings' == $_GET['page'] or 'wpcvp-admin' == $_GET['page'])  ) {
		
		wp_enqueue_script( 'formtips', wpcvp_plugin_url( 'js/formtips.js' ),array('jquery'), WPCVP_VER, false);
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'jquery.datepicker', wpcvp_plugin_url( 'js/datepicker.js' ),array('jquery'), WPCVP_VER, false);
		wp_enqueue_script( 'jquery.validate', wpcvp_plugin_url( 'js/jquery.validate.js' ),array('jquery'), WPCVP_VER, false);
		//wp_enqueue_script( 'validate.loc.fr', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_fr.js',array('jquery'), WPCVP_VER, false);
		wp_deregister_script( 'farbtastic' );
		wp_enqueue_script( 'farbtastic', wpcvp_plugin_url( 'js/farbtastic.js' ),array('jquery'), WPCVP_VER, false);
		wp_enqueue_script( 'wpcvp_admin_js', wpcvp_plugin_url( 'admin/wpcvp.js' ),
			array('jquery'), WPCVP_VER, 'all');
		wp_enqueue_style( 'wpcvp_admin_head_css', wpcvp_plugin_url( 'admin/css/admin.css' ),
			false, WPCVP_VER, 'all');
			
		wp_localize_script('wpcvp_admin_js', 'wpcvpadminL10n', array(
			'delete_url' => wpcvp_plugin_url( 'admin/delete-poll.php' ),
			'export_url'=> wpcvp_plugin_url( 'admin/export-poll.php' ),
			'delete_poll_confirm' => __('Delete the Poll with ID ','wpcvp'),
			'ajn'=> $nonce
		));
	}
  }
}
add_action( 'admin_init', 'wpcvp_admin_scripts' );

function wpcvp_admin_head() {
global $wpcvp_options;
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('wpcvp-add-poll' == $_GET['page'] or 'wpcvp-settings' == $_GET['page'] or 'wpcvp-admin' == $_GET['page'] )  ) {
		//wp_print_scripts( 'farbtastic' );
		wp_print_styles( 'farbtastic' );
	?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#pollq_add_table tbody").sortable({ axis: 'y' });
				jQuery("#polla_add_table tbody").sortable({ axis: 'y' });
				
				jQuery('form#add_poll').bind('form-pre-serialize', function(e) {
					tinyMCE.triggerSave();
				});
				
				jQuery('.qoptck').change(function () {
						if ( jQuery(this).is(":checked") ) {
							var qoptid=jQuery(this).attr('id');
							qoptno=qoptid.split('-');
							jQuery('#qopt-'+qoptno[1]).val('0');
						} 
						else{
							var qoptid=jQuery(this).attr('id');
							qoptno=qoptid.split('-');
							jQuery('#qopt-'+qoptno[1]).val('1');
						}
				})
				
				<?php if( isset($_POST['wpcvp_add_submit']) ) {?>
					jQuery('#edit_bottom').scrollView();		
				<?php } ?>
				
				jQuery('#add_poll').validate();
				
				jQuery('form#add_poll input.pollq_title, form#add_poll .pollq_desc ').formtips({
					tippedClass: 'defaultValue'
				});
				
				if(jQuery("#pollp_expiry").is(":checked")){jQuery("#expiry_date").hide()}else{jQuery("#expiry_date").show()}
				jQuery( "#pollp_expiry_date" ).not('.hasDatePicker').datepicker({
					showOn: "both",
					dateFormat: 'yy/dd/mm',
					altField: '#pollp_actual_expiry_date',
					altFormat: 'yy-mm-dd',
					buttonText: '<?php echo htmlentities(__("Poll expiry date",'wpcvp'),ENT_QUOTES); ?>',
					buttonImage: "<?php echo wpcvp_plugin_url('images/calendar.gif'); ?>",
					buttonImageOnly: true
				});
				
				jQuery('#poll-settings').submit(function() { 
					jQuery(this).ajaxSubmit({
						success: function(){
							jQuery('#saveResult').html("<div id='saveMessage' class='successModal'></div>");
							jQuery('#saveMessage').append("<p><?php echo htmlentities(__('Colored Vote Polls - Default settings saved !!','wpcvp'),ENT_QUOTES); ?></p>").show();
							jQuery('#poll_shortcode').scrollView();
						},  
						timeout:   5000
					}); 
					setTimeout("jQuery('#saveMessage').hide('slow');", 5000);
					return false; 
				});
	
			});

			jQuery.fn.scrollView = function () {
				return this.each(function () {
					jQuery('html, body').animate({
						scrollTop: jQuery(this).offset().top
					}, 1600);
				});
			}
			
			var pollqcount=0;
			function wpcvp_add_poll_question_js(countq){if(pollqcount==0){pollqcount=countq}var count=pollqcount;pollqcount++;jQuery('<tr valign="top" style="cursor:move;" id="rq'+count+'"><td style="" ><input style="width:100%;" type="text" size="50" name="pollq[title][]" class="pollq_title" value="" title="Title..." autocomplete="off" /> <br /><textarea style="width:100%;" rows="2" cols="50" name="pollq[desc][]" class="pollq_desc"  title="Description..." autocomplete="off"></textarea><br /><input type="hidden" id="qopt-'+count+'" name="qopt[]" value="0" /><input class="qoptck" id="qoptck-'+count+'" type="checkbox" name="pollq[optional][]" value="0" checked /> <label for="qoptck-'+count+'"><?php _e('Mandatory','wpcvp'); ?></label></td><td class="dragHandle" title="Drag to reorder" >&nbsp;<input type="hidden" name="qorder[]" value="'+count+'" /></td><td style="padding-left:2px;"><input type="button" id="q'+count+'" class="deleteRow" value="0" style="text-indent:-9999px;" onclick="wpcvp_delete_question_js(this.id);" /><input type="hidden" value="0" name="qid[]" /></td></tr>').hide().appendTo("#pollq_add_tbody").fadeIn(1000);
				jQuery('form#add_poll input.pollq_title, form#add_poll .pollq_desc ').not('.defaultValue').formtips({
					tippedClass: 'defaultValue'
				});
				jQuery('.qoptck').change(function () {
						if ( jQuery(this).is(":checked") ) {
							var qoptid=jQuery(this).attr('id');
							qoptno=qoptid.split('-');
							jQuery('#qopt-'+qoptno[1]).val('0');
						} 
						else{
							var qoptid=jQuery(this).attr('id');
							qoptno=qoptid.split('-');
							jQuery('#qopt-'+qoptno[1]).val('1');
						}
				})
			}
			
			var pollacount=0;
			function wpcvp_add_poll_answer_js(counta){if(pollacount==0){pollacount=counta}var count=pollacount;pollacount++;jQuery('<tr valign="top" style="padding:0px;display:table-row;" id="rdel'+count+'"><td style="padding:0px;padding-bottom:5px;"><input type="button" class="colorbox color_bg'+count+' colorbox_bg'+count+'" id="colorbox_bg'+count+'" style="background-color:#b6d7a8;width:20px;height:20px;" /> <input name="polla[bg][]" type="hidden" id="bg'+count+'" value="#b6d7a8" /> &nbsp;	<input type="button" class="colorbox color_fg'+count+' colorbox_fg'+count+'" id="colorbox_fg'+count+'" style="background-color:#000000;width:20px;height:20px;" /> <input name="polla[fg][]" type="hidden" id="fg'+count+'" value="#000000" /> &nbsp;<input name="polla[text][]" size="25" type="text" class="color_bg'+count+' color_t'+count+' atext" style="background-color:#b6d7a8;color:#000000;" value="<?php echo htmlentities(__("I love it",'wpcvp'),ENT_QUOTES); ?>" />	<div class="color-picker-wrap" id="picker_bg'+count+'"><input type="text" size=7 name="bgcval'+count+'" id="bgcval'+count+'" value="#b6d7a8;" /></div><div class="color-picker-wrap" id="picker_fg'+count+'"><input type="text" size=7 name="fgcval'+count+'" id="fgcval'+count+'" value="#000000" /></div><script type="text/javascript">jQuery(document).ready(function() {jQuery("#picker_bg'+count+'").farbtastic(function(color) { jQuery(".color_bg'+count+'").css("backgroundColor",color); jQuery("#bg'+count+'").val(color); jQuery("#bgcval'+count+'").val(color);});	jQuery.farbtastic("#picker_bg'+count+'").setColor("#b6d7a8"); jQuery("#bgcval'+count+'").keyup(function() {jQuery.farbtastic("#picker_bg'+count+'").setColor( jQuery("#bgcval'+count+'").val() );}); jQuery("#colorbox_bg'+count+'").click(function () {if (jQuery("#picker_bg'+count+'").css("display") == "block") {jQuery("#picker_bg'+count+'").fadeOut("slow"); } else { jQuery("#picker_bg'+count+'").fadeIn("slow"); }});var bg'+count+' = false;	jQuery(document).mousedown(function(){ jQuery("#bgcval'+count+'").mousedown(function() {	bg'+count+'=true;}); if (bg'+count+' == true) {return; }	jQuery("#picker_bg'+count+'").fadeOut("slow");});jQuery(document).mouseup(function(){bg'+count+' = false;});jQuery("#picker_fg'+count+'").farbtastic(function(color) { jQuery(".color_fg'+count+'").css("backgroundColor",color); jQuery(".color_t'+count+'").css("color",color);jQuery("#fg'+count+'").val(color); jQuery("#fgcval'+count+'").val(color);});jQuery.farbtastic("#picker_fg'+count+'").setColor("#000000"); jQuery("#fgcval'+count+'").keyup(function() {jQuery.farbtastic("#picker_fg'+count+'").setColor( jQuery("#fgcval'+count+'").val() );}); jQuery("#colorbox_fg'+count+'").click(function () { if (jQuery("#picker_fg'+count+'").css("display") == "block") { jQuery("#picker_fg'+count+'").fadeOut("slow"); }else {jQuery("#picker_fg'+count+'").fadeIn("slow"); }});var fg'+count+' = false;	jQuery(document).mousedown(function(){ jQuery("#fgcval'+count+'").mousedown(function() {fg'+count+'=true;}); if (fg'+count+' == true) {return; }jQuery("#picker_fg'+count+'").fadeOut("slow");});jQuery(document).mouseup(function(){fg'+count+' = false;});	});</scr'+'ipt></td>	<td class="dragHandle" style="width:16px;padding:0px;padding-bottom:5px;" title="Drag to reorder" >&nbsp; <input type="hidden" name="aorder[]" value="'+count+'"</td>	<td style="padding:0px;padding-left: 2px;"><input type="button" id="del'+count+'" class="deleteRow" onclick="wpcvp_delete_answer_js(this.id);" /><input type="hidden" value="0" name="aid[]" /></td></tr>').appendTo("#polla_add_tbody");}
			

			function wpcvp_delete_question_js(cid){	jQuery('#r'+cid).fadeOut('normal', function() {jQuery(this).remove();}); }
			var qdel_arr=[];
			function wpcvp_edit_delete_question_js(cid){	var qid=jQuery('#'+cid).val(); qdel_arr.push(qid);jQuery('#qid_del').val(qdel_arr);
			jQuery('#r'+cid).fadeOut('normal', function() {jQuery(this).remove();}); }
			
			function wpcvp_delete_answer_js(cid){ jQuery('#r'+cid).fadeOut('normal', function() {jQuery(this).remove();}); }
			var adel_arr=[];
			function wpcvp_edit_delete_answer_js(cid){	var aid=jQuery('#'+cid).val(); adel_arr.push(aid);jQuery('#aid_del').val(adel_arr);
			jQuery('#r'+cid).fadeOut('normal', function() {jQuery(this).remove();}); }
			
			var wpcvp_default_colors = jQuery.parseJSON(<?php print json_encode(json_encode($wpcvp_options['colors'])); ?>);  

			function wpcvp_reset_colors_js(counta) {
				if(pollacount>0){
					counta=pollacount;
				}
				$countd=jQuery(wpcvp_default_colors).size();
				for(var j=0;j<counta;) {
					for(var i=0;i<$countd;i++) {
						jQuery.farbtastic("#picker_bg"+j).setColor(wpcvp_default_colors[i]['bg']);
						jQuery.farbtastic("#picker_fg"+j).setColor(wpcvp_default_colors[i]['fg']);	
						j++;
						if(j==counta) { break };
					}
				}
			}
			
			function wpcvp_expiry_date_js(){if(jQuery("#pollp_expiry").is(":checked")){jQuery("#expiry_date").slideUp('fast')}else{jQuery("#expiry_date").slideDown('fast')}}

		</script>
	<?php
	}
  }
}
add_action('admin_head', 'wpcvp_admin_head');
?>