/**
 * Site-wide tools
 */
(function($) {
	
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
		
		// js detection for css tweaks
		$('body').removeClass('no-js').addClass('js');
	});

})(jQuery);

