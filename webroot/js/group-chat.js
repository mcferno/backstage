;
/**
 * Group Chat App
 * @author Patrick McFern <mcferno AT gmail.com>
 */
var GroupChat = {
	dateFormat : 'mmm-dd, h:MM:ss TT',
	templates : {
		chatRowTemplate : false
	},
	lastAck : 0
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

	/**
	 * ChatMsg Model. Represents a single chat message from a single user.
	 */
	ns.ChatMsg = Backbone.Model.extend({
		defaults: {
			date : '',
			handle : 'unknown',
			text : ''
		},
		initialize : function() {
			this.view = new ns.ChatMsgView({ model : this });
		}
	});

	/**
	 * ChatMsg View. Visual representation of a single chat message.
	 */
	ns.ChatMsgView = Backbone.View.extend({
		tagName : 'tr',
		className : 'chat-row',
		initialize : function() {
			this.render();
		},
		render : function() {
			var timestamp = this.model.get('timestamp');
			var date = new Date(timestamp * 1000);
			var rendered = _.template(ns.templates.chatRowTemplate.html(), {
				date : ns.formatDate(date),
				username : this.model.get('handle'),
				message : this.model.get('text')
			});
			this.$el.html(rendered);
			return this;
		}
	});
	
	/**
	 * ChatLog Collection. Maintains the relative ordering of individual ChatMsg
	 * models, which in turn orders the views.
	 */
	ns.ChatLog = Backbone.Collection.extend({
		
		model : ns.ChatMsg,
		
		// reverse chronological sort for newest-on-top
		comparator : function(chatMsgModel) {
			return -1 * parseInt(chatMsgModel.get('timestamp'));
		}
	});
	
	/**
	 * ChatLog View, controls the visual presentation of the ChatLog collection.
	 * Handles relative ordering, regardless of insertion time.
	 */
	ns.ChatLogView = Backbone.View.extend({
		initialize : function() {
			this.setElement($('.chat-window .chat'));
			ns.chatLog = new ns.ChatLog();
			ns.chatLog.bind('add', this.addSingle, this);
		},
		addSingle : function(chatMsgModel) {
			if(ns.chatLog.length > 1) {
				var idx = ns.chatLog.sortedIndex(chatMsgModel, ns.chatLog.comparator);
				// new view belongs at the front
				if(idx == 0) {
					this.$el.prepend(chatMsgModel.view.el);
					
				// new view belongs at the end
				} else if(idx == ns.chatLog.length - 1) {
					this.$el.append(chatMsgModel.view.el);
					
				// new view has inner placement (out of order from server?)
				} else {
					var ideal = this.$('.'+chatMsgModel.view.className).get(idx);
					$(ideal).before(chatMsgModel.view.el);
				}
			} else {
				this.$el.append(chatMsgModel.view.el);
			}
		}
	});
	
	ns.submitMessage = function() {
		var text = ns.msgBar.attr('value');
		var now = new Date();
		var date = ns.formatDate(now);		
		ns.addMessage(date,now.getTime(),text,ns.handle);
		
		$.ajax({
			type: 'POST',
  			url: AppBaseURL+'backstage/messages/add',
  			data: {
  				'Message':{
  					'text':text
  				}
  			},
  			dataType: 'json'
		});
		
		ns.msgBar.attr('value','');
	}
	
	ns.addMessage = function(date,timestamp,text,handle) {
		var rowData = _.template(ns.templates.chatRowTemplate.html(), {
			date : date,
			timestamp : timestamp,
			username : handle,
			message : text
		});
		var row = $('<tr>').addClass('chat-row').html(rowData);
		row.hide();
		ns.chatWindow.prepend(row);
		row.show().css('display','table-row');
	}
	
	ns.sendHeartbeat = function() {
		var data = {};
		
		if(typeof ns.chatWindow != 'undefined') {
			data.ack = ns.lastAck;
		}
		$.ajax({
			data : data,
			url : AppBaseURL+'backstage/users/heartbeat',
			success : ns.processHeartbeat
		});
	}
	
	ns.processHeartbeat = function(data) {
		$('.online-count')
			.text(data.online.length);

		// not on chat, alert them of possible new messages
		if(typeof ns.chatWindow == 'undefined') {
			if(data.new_messages == 0) {
				ns.msgNotifier.hide();
			} else {
				ns.msgNotifier.text(data.new_messages).show();
			}
		} else {
			if(data.ack > ns.lastAck) {
				ns.lastAck = data.ack;
			}
			
			if(data.messages.length > 0) {
				ns.processMessages(data.messages);
			}
		}
	}
	
	ns.processMessages = function(data) {
		
		$(data).each(function(){
			var message = this;
			var date = this.Message.created;
			
			ns.chatLog.add(new ns.ChatMsg({
				date : date,
				timestamp :  message.Message.timestamp,
				text : message.Message.text,
				handle : message.User.username
			}));
		});
	}
	
	ns.formatDate = function(dateObj) {
		var date = ns.padNumber((dateObj.getMonth() + 1))
			+ '/' + ns.padNumber(dateObj.getDate())
			+ ' ' + ns.padNumber(dateObj.getHours())
			+ ':' + ns.padNumber(dateObj.getMinutes())
			+ ':' + ns.padNumber(dateObj.getSeconds());
		return date;
	}
	
	ns.padNumber = function(str) {
		return String('00'+str).match(/[0-9]{2}$/);
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
		
		ns.templates.chatRowTemplate = $('#chatRowTemplate');
		
		ns.chatLogView = new ns.ChatLogView();
		
		$('.loading').hide();
	}
		
	$(document).ready(function() {
		ns.init();
		if($('body').hasClass('route-action-admin-group-chat')) {
			ns.initChat();
			ns.heartbeat = setInterval(ns.sendHeartbeat,5000);
		} else {
			ns.heartbeat = setInterval(ns.sendHeartbeat,25000);
		}
		
		ns.sendHeartbeat();
	});
	
})(jQuery, GroupChat);