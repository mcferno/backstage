;
/**
 * Group Chat App
 * @author Patrick McFern <mcferno AT gmail.com>
 */
var GroupChat = {
	dateFormat : 'mmm-dd, h:MM:ss TT'
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
		ns.addMessage(now.format(ns.dateFormat),text,ns.handle);
		
		$.ajax({
			type: 'POST',
  			url: '/kennyquotemachine.com/backstage/messages/add',
  			data: {
  				'Message':{
  					'text':text
  				}
  			},
  			dataType: 'json'
		});
		
		ns.msgBar.attr('value','');
	}
	
	ns.addMessage = function(date,text,handle) {
		var row = $('<tr><td class="time">'+date+'</td><td class="handle">'+handle+'</td><td class="message">'+text+'</td></tr>')
		row.hide();
		ns.chatWindow.prepend(row);
		row.show(500);
	}
	
	ns.init = function() {
		ns.msgNotifier = $('<span class="badge badge-custom"></span>').text(0).hide();
		$('.chat-link').append(ns.msgNotifier);
	}
	
	ns.initChat = function() {
		ns.msgBar = $('.chat-bar .msg input');
		ns.chatWindow = $('.chat-window table.chat');
		ns.handle = $('.chat-bar .handle').text();
		ns.loadingIndicator = $('#loaderAnim');		
		$('.loading').hide();
		
		$.getJSON('/kennyquotemachine.com/backstage/messages/getLatest',function(response) {
			$(response.messages).each(function(){
				var date = new Date(Date.parse(this.Message.created));
				ns.addMessage(date.format(ns.dateFormat),this.Message.text,this.User.username);
			});
		});
	}
		
	$(document).ready(function() {
		ns.init();
		
		if($('body').hasClass('route-action-admin-group-chat')) {
			ns.initChat();
		}
		
//		$.getJSON('/kennyquotemachine.com/backstage/messages/add',function(data) {
//			console.log(data);
//		});
	});
	
})(jQuery, GroupChat);