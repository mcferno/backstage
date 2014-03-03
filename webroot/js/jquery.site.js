/**
 * Site-wide tools
 */
/*global _gaq */
application = {};
(function($) {
	"use strict";

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

