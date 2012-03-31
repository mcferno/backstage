/**
 * Site-wide tools
 */
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
	
	var collapseEmptyNav = function() {
		var obj = $(this);
		if(obj.find('li :not(.nav-headers)').length === 0) {
			obj.hide();
			obj.closest('[class*="span"]').each(function() {
				$(this).attr('class').match('/span([0-9]+)/');
				var span = $(this).attr('class').match(/span([0-9]+)/);
				var next = $(this).next();
				if(next) {
					var old_span = next.attr('class').match(/span([0-9]+)/);
					var new_span = parseInt(span[1]) + parseInt(old_span[1]);
					next.addClass('span'+ new_span).removeClass(old_span[0]);
				}
			});
		}
	}
	
	$(document).ready(function() {
		// auto-collapse empty navs
		$('ul.nav-list').each(collapseEmptyNav);
		
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

})(jQuery);

