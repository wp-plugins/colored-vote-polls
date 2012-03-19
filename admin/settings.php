<?php
function wpcvp_admin_settings() {
global $wpcvp_options;
?>
<div class="wrap">
	<h2><?php _e('Colored Vote Polls Settings','wpcvp'); ?></h2>
	
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
	<form id="poll-settings" method="post" action="options.php">
	<?php settings_fields('wpcvp-group'); ?>
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="wpcvp_options[permission]"><?php _e('Who can vote?','wpcvp'); ?></label></th> 
			<td><input id="permission0" name="wpcvp_options[permission]" type="radio" value="0" <?php checked('0', $wpcvp_options['permission']); ?>  /> <label for="permission0"><?php _e('Everybody','wpcvp'); ?></label><br />
			<input id="permission1" name="wpcvp_options[permission]" type="radio" value="1" <?php checked('1', $wpcvp_options['permission']); ?>  /> <label for="permission1"><?php _e('Registered users','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="wpcvp_options[track]"><?php _e('Track voters by...','wpcvp'); ?></label></th> 
			<td><input id="track0" name="wpcvp_options[track]" type="radio" value="0" <?php checked('0', $wpcvp_options['permission']); ?>  /> <label for="track0"><?php _e('User login, Cookies & IP','wpcvp'); ?></label><br />
			<input id="track1" name="wpcvp_options[track]" type="radio" value="1" <?php checked('1', $wpcvp_options['permission']); ?>  /> <label for="track1"><?php _e('Cookies only','wpcvp'); ?></label><br />
			<input id="track2" name="wpcvp_options[track]" type="radio" value="2" <?php checked('2', $wpcvp_options['permission']); ?>  /> <label for="track2"><?php _e('IP only (not recommended)','wpcvp'); ?></label>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:130px;"><label for="wpcvp_options[colors]"><?php _e('Default colors','wpcvp'); ?></label></th> 
			<td>
			
			<!-- Default Colors-->
			<?php for($i=0;$i<5;$i++) { ?>
			<input type="button" class="colorbox color_bg<?php echo $i;?> colorbox_bg<?php echo $i;?>" id="colorbox_bg<?php echo $i;?>" style="background-color:<?php echo $wpcvp_options['colors'][$i]['bg'];?>;width:20px;height:20px;" /> <input name="wpcvp_options[colors][<?php echo $i;?>][bg]" type="hidden" id="bg<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['bg'];?>" /> &nbsp;
			
			<input type="button" class="colorbox color_fg<?php echo $i;?> colorbox_fg<?php echo $i;?>" id="colorbox_fg<?php echo $i;?>" style="background-color:<?php echo $wpcvp_options['colors'][$i]['fg'];?>;width:20px;height:20px;" /> <input name="wpcvp_options[colors][<?php echo $i;?>][fg]" type="hidden" id="fg<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['fg'];?>" /> &nbsp;
			
			<input name="wpcvp_options[colors][<?php echo $i;?>][text]" size="25" type="text" class="color_bg<?php echo $i;?> color_t<?php echo $i;?> atext" style="background-color:<?php echo $wpcvp_options['colors'][$i]['bg'];?>;color:<?php echo $wpcvp_options['colors'][$i]['fg'];?>;" value="<?php echo $wpcvp_options['colors'][$i]['text'];?>" />
			<div class="color-picker-wrap" id="picker_bg<?php echo $i;?>"><input type="text" size=7 name="bgcval<?php echo $i;?>" id="bgcval<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['bg'];?>" /></div>
			<div class="color-picker-wrap" id="picker_fg<?php echo $i;?>"><input type="text" size=7 name="fgcval<?php echo $i;?>" id="fgcval<?php echo $i;?>" value="<?php echo $wpcvp_options['colors'][$i]['fg'];?>" /></div>
			
			<script type="text/javascript">
				//bg
				jQuery('#picker_bg<?php echo $i;?>').farbtastic(function(color) { jQuery('.color_bg<?php echo $i;?>').css('backgroundColor',color); jQuery('#bg<?php echo $i;?>').val(color);jQuery("#bgcval<?php echo $i;?>").val(color);});
				jQuery.farbtastic("#picker_bg<?php echo $i;?>").setColor('<?php echo $wpcvp_options['colors'][$i]['bg'];?>');
				jQuery('#bgcval<?php echo $i;?>').keyup(function() {jQuery.farbtastic("#picker_bg<?php echo $i;?>").setColor( jQuery('#bgcval<?php echo $i;?>').val() );});
				jQuery('#colorbox_bg<?php echo $i;?>').click(function () {if (jQuery('#picker_bg<?php echo $i;?>').css('display') == "block") {jQuery('#picker_bg<?php echo $i;?>').fadeOut("slow"); } else { jQuery('#picker_bg<?php echo $i;?>').fadeIn("slow"); }});				
				var bg<?php echo $i;?> = false;
				jQuery(document).mousedown(function(){ jQuery('#bgcval<?php echo $i;?>').mousedown(function() {	bg<?php echo $i;?>=true;});if (bg<?php echo $i;?> == true) {return; }	jQuery('#picker_bg<?php echo $i;?>').fadeOut("slow");});
				jQuery(document).mouseup(function(){bg<?php echo $i;?> = false;});
				//fg
				jQuery('#picker_fg<?php echo $i;?>').farbtastic(function(color) { jQuery('.color_fg<?php echo $i;?>').css('backgroundColor',color); jQuery('.color_t<?php echo $i;?>').css('color',color);jQuery('#fg<?php echo $i;?>').val(color);jQuery("#fgcval<?php echo $i;?>").val(color);});
				jQuery.farbtastic("#picker_fg<?php echo $i;?>").setColor('<?php echo $wpcvp_options['colors'][$i]['fg'];?>');
				jQuery('#colorbox_fg<?php echo $i;?>').click(function () { if (jQuery('#picker_fg<?php echo $i;?>').css('display') == "block") { jQuery('#picker_fg<?php echo $i;?>').fadeOut("slow"); }else {jQuery('#picker_fg<?php echo $i;?>').fadeIn("slow"); }});				
				var fg<?php echo $i;?> = false;
				jQuery(document).mousedown(function(){jQuery('#fgcval<?php echo $i;?>').mousedown(function() {fg<?php echo $i;?>=true;});if (fg<?php echo $i;?> == true) {return; }jQuery('#picker_fg<?php echo $i;?>').fadeOut("slow");});
				jQuery(document).mouseup(function(){fg<?php echo $i;?> = false;});
			</script>
			<br />
			<?php } ?>
			</td> 
		</tr>
		
	</table>
	<table class="form-table">	
		<tr valign="top"> 
			<th scope="row" style="width:200px;"><label for="maxchar"><?php _e('Add "read more" if a question\'s description is more than','wpcvp'); ?></label></th> 
			<td><input id="maxchar" name="wpcvp_options[maxchar]" type="text" class="small-text" value="<?php echo $wpcvp_options['maxchar']; ?>" /> <?php _e('characters','wpcvp'); ?>
			</td> 
		</tr>
		
		<tr valign="top"> 
			<th scope="row" style="width:200px;"><label for="wpcvp_options[notify]"><?php _e('Send email notification when new question is added to a poll','wpcvp'); ?></label></th> 
			<td><input id="notify" name="wpcvp_options[notify]" type="checkbox" value="1" <?php checked('1', $wpcvp_options['notify']); ?> /> <label for="notify"><?php _e('Yes, send to site admin','wpcvp'); ?></label>
			</td> 
		</tr>
		
	</table>
	
	<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
	</p>
	
	</form>
	<div id="saveResult"></div>
	</div>
</div>
<?php 
}

function register_wpcvp_settings() { // whitelist options
  register_setting( 'wpcvp-group', 'wpcvp_options' );
}

?>