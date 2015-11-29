jQuery(function($) {
	$('.modal.aside').ace_aside();
	
	$('#aside-inside-modal').addClass('aside').ace_aside({container: '#my-modal > .modal-dialog'});
	
	$(document).one('ajaxloadstart.page', function(e) {
		//in ajax mode, remove before leaving page
		$('.modal.aside').remove();
		$(window).off('.aside')
	});
})