/**
 * Site-wide tools
 */
/*global _gaq */
application = {};
(function($) {
	"use strict";
	
	// ajax quote generator refresh
	$('article.generator').live('click',function() {
		var quote = $(this).find('.quote');
		
		if(quote) {
			$.getJSON('generator',function(data) {
				_gaq.push(['_trackPageview', '/generator/ajax-refresh']);
				quote.html(data['quote']);
			});
		}
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
				});
		});
		
		// js detection for css tweaks
		$('body').removeClass('no-js').addClass('js');
	});

})(jQuery);

