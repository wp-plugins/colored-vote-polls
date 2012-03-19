<?php
		
function wpcvp_admin_polls() {
$polls=wpcvp_get_polls();
$url = wpcvp_admin_url( array( 'page' => 'wpcvp-add-poll' ) );

?>
<div class="wrap">
	<h2><?php _e('Colored Vote Polls','wpcvp'); ?></h2>
	
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
	
	<thead>
	<tr>
		<th scope='col'  class='manage-column wpcvp-title'  style=""><span><?php _e('Poll Title','wpcvp'); ?></span></th>
		<th scope='col' class='manage-column wpcvp-title column-author'  style=""><span><?php _e('Votes','wpcvp'); ?></span></th>
		<th scope='col' class='manage-column wpcvp-title column-author'  style=""><span><?php _e('Date Published','wpcvp'); ?></span></a></th>	
		<th scope='col' class='manage-column wpcvp-title column-author'  style=""><span><?php _e('Status','wpcvp'); ?></span></th>
		<th scope='col' class='manage-column wpcvp-title column-author'  style=""><span><?php _e('Shortcode','wpcvp'); ?></span></th>
	</tr>
	</thead>
	
	<tbody id="the-list">
	<?php foreach($polls as $poll) {
			$status='';
			if($poll['pollp_status'] == '1' ){
				$status=__('closed');
			}
			else{
				if($poll['pollp_expiry'] == '0'){
					$status=__('never expires');
				}
				else{
					if ( strtotime( $poll['pollp_expiry_date'] ) >= current_time('timestamp') ) 
						$status= __('expires in ').human_time_diff( current_time('timestamp') , strtotime( $poll['pollp_expiry_date'] ) );
					else
						$status=__('closed');
				}
			}
	?>	
		<tr id='poll-<?php echo $poll['pollp_id'];?>' class='format-default' valign="top">
			<td class="column-title"><strong><?php echo $poll['pollp_title'];?></strong>
				<div class="row-actions">
					<?php $edit_link = '<a href="' . esc_attr( $url ).'&edit='.$poll['pollp_id'].'">'
		. esc_html( __( 'Edit') ) . '</a>'; ?>
					<span ><?php echo $edit_link;?> | </span>
					<?php $pageID=wpcvp_get_page_ID_by_slug('wpcvp-preview');
						  $wpurl=get_bloginfo('wpurl');?>
					<span ><a href="<?php echo $wpurl; ?>?page_id=<?php echo $pageID;?>&pollid=<?php echo $poll['pollp_id'];?>"><?php _e('Preview','wpcvp'); ?></a> | </span>
					<span ><a class="wpcvp_export" alt="<?php echo $poll['pollp_id']; ?>" href="#"><?php _e('Export to CSV','wpcvp'); ?></a> | </span>
					<span ><a class="wpcvp_pdel" href="#" alt="<?php echo $poll['pollp_id']; ?>"><?php _e('Delete','wpcvp'); ?></a> </span>
				</div>
			</td>
			<td><?php echo $poll['pollp_totalvoters'];?></td>
			<td><?php echo date( 'Y/d/m', strtotime($poll['pollp_timestamp']) ) ;?></td>
			<td><?php echo $status;?></td>
			<td>[colorvote id="<?php echo $poll['pollp_id'];?>"]</td>
		</tr>
	<?php } ?>
	</tbody>
	
	<tfoot>
		<th scope='col'  class='manage-column wpcvp-title'  style=""><span><?php _e('Poll Title','wpcvp'); ?></span></th>
		<th scope='col' class='manage-column wpcvp-title'  style=""><span><?php _e('Votes','wpcvp'); ?></span></th>
		<th scope='col' class='manage-column wpcvp-title'  style=""><span><?php _e('Date Published','wpcvp'); ?></span></a></th>	
		<th scope='col' class='manage-column wpcvp-title'  style=""><span><?php _e('Status','wpcvp'); ?></span></th>
		<th scope='col' class='manage-column wpcvp-title'  style=""><span><?php _e('Shortcode','wpcvp'); ?></span></th>
	</tfoot>
	
	</table>
	
</div>
<?php 
}

?>