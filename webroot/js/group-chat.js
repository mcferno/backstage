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
		})
		.on('click','.slideout .close',function(e){
			e.preventDefault(); // disable all buttons's defaults
			$('.slideout').animate({left:-1*($('.slideout').width() + 75)},650);
		})
		.on('click','.online-count',function(e){
			e.preventDefault(); // disable all buttons's defaults
			if($('.slideout').offset().left < 0 || !$('.slideout').is(':visible')) {
				$('.slideout').css('left',-1*($('.slideout').width() + 75)).show().animate({left:'0',easing:'easeOutExpo'},650);
			} else {
				$('.slideout').show().animate({left:-1*($('.slideout').width() + 75),easing:'easeOutExpo'},650);
			}
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
		
		var postData = {
			'Message':{
				'text':text
			}
		};
		if(ns.lastAck != 0) {
			postData.ack = ns.lastAck;
		}
		
		$.ajax({
			type: 'POST',
  			url: AppBaseURL+'backstage/messages/add',
  			data: postData,
  			dataType: 'json',
  			success : function(data) {
  				ns.processHeartbeat(data);
  			}
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
		var allUsers = '';
		for(var i=0;i<data.online.length;i++) {
			allUsers += data.online[i].User.username;
			if(i != data.online.length - 1) {
				allUsers += ', ';
			}
		}
		$('.online-count')
			.data('title',allUsers)
			.text(data.online.length);
		$('.slideout .names').text(allUsers);
		
		if(data.new_messages == 0) {
			ns.msgNotifier.addClass('badge-off');
			document.title = ns.originalTitle;
			ns.msgNotifier.text(data.new_messages);
		} else {
			ns.msgNotifier.removeClass('badge-off');
			ns.msgNotifier.text(data.new_messages);
			document.title = '(' + data.new_messages + ') ' + ns.originalTitle;
		}

		// on chat, process new messages
		if(typeof ns.chatWindow !== 'undefined') {
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
				id : message.Message.id,
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
		ns.msgNotifier = $('.navbar .message-count');
		ns.userCount = $('.navbar .online-count');
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
		ns.originalTitle = document.title;
		ns.init();
		if($('body').hasClass('route-action-admin-group-chat')) {
			ns.initChat();
			ns.heartbeat = setInterval(ns.sendHeartbeat,3000);
		} else {
			ns.heartbeat = setInterval(ns.sendHeartbeat,45000);
		}
		
		ns.sendHeartbeat();
	});
	
})(jQuery, GroupChat);