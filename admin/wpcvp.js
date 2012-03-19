jQuery('.wpcvp_pdel').click(function(){
	var pollid=jQuery(this).attr('alt');
	var r=confirm(wpcvpadminL10n.delete_poll_confirm+pollid+' ?');
	if(r==true) {
		jQuery.post( wpcvpadminL10n.delete_url , {'pollid' : pollid, '_ajax_nonce':wpcvpadminL10n.ajn } , function(data){
			jQuery('#poll-'+pollid).remove();
		});	
	}
	return false;
});
jQuery('.wpcvp_export').click(function(){
	var pollid=jQuery(this).attr('alt');
	jQuery.get( wpcvpadminL10n.export_url , {'pollid' : pollid, '_ajax_nonce':wpcvpadminL10n.ajn } , function(data){
		document.location.href = wpcvpadminL10n.export_url+'?pollid='+pollid;
	});	
		
	return false;
});