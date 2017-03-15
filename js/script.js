
jQuery(document).ready( function() {

	jQuery("#dgcat-holder li input.parent").click( function() {
		
		// update all the sub 
		var newState = jQuery(this).is('input:checked');
			
		jQuery( "ol input:checkbox", jQuery(this).parent().parent() ).each(function () {
			jQuery(this).attr('checked', newState);
		});
		
	});
});
