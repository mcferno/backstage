;
Backstage = {};
(function($, ns) {
	"use strict";
	
	$('.image-upload-btn').live('click',function(e) {
		e.preventDefault();
		
		$('.asset-upload-popin').modal();
	});

	$('.contest-start').live('click',function(e) {
		e.preventDefault();
		
		$('.contest-start-popin').modal();
	});

	$(document)
		.on('focus', '.copier', function() {
			this.select();
		})
		.on('mouseup', '.copier', function(e) {
			e.preventDefault();
		});
	
	$(document).ready(function() {		
		// mouseover icon-color inversion
		$('.dropdown-menu i.icon').each(function(){
			$(this).closest('li')
				.mouseover(function() {
					$(this).find('.icon').addClass('icon-white').removeClass('icon');
				})
				.mouseout(function() {
					$(this).find('.icon-white').addClass('icon').removeClass('icon-white');
				})
		});
		
		// js detection for css tweaks
		$('body').removeClass('no-js').addClass('js');
	});

})(jQuery, Backstage);
