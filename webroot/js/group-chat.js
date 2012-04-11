;
/**
 * Group Chat App
 * @author Patrick McFern <mcferno AT gmail.com>
 */
var GroupChat = {
};

(function($, ns) {
	"use strict";
	
	$(document)
		.on('click','.chat-bar button', function(e) {
			e.preventDefault(); // disable all buttons's defaults
		})
		.on('click','.chat-bar .msg button[type="submit"]', function(e) {
			e.preventDefault(); // disable all buttons's defaults
			ns.submitMessage();
		});
	
	ns.submitMessage = function() {
		var text = ns.msgBar.attr('value');
		var now = new Date();		
		var row = $('<tr><td class="time">'+now.format("mmm-dd, h:MM:ss TT")+'</td><td class="handle">'+ns.handle+'</td><td class="message">'+text+'</td></tr>')
		row.hide();
		ns.chatWindow.prepend(row);
		row.show(500);
		ns.msgBar.attr('value','');
	}
	
	ns.init = function() {
		ns.msgBar = $('.chat-bar .msg input');
		ns.chatWindow = $('.chat-window table.chat');
		ns.handle = $('.chat-bar .handle').text();
	}
		
	$(document).ready(function() {
		ns.init();
	});
	
})(jQuery, GroupChat);