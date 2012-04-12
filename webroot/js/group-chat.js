;
/**
 * Group Chat App
 * @author Patrick McFern <mcferno AT gmail.com>
 */
var GroupChat = {
	dateFormat : 'mmm-dd, h:MM:ss TT',
	templates : {
		chatRowTemplate : false
	}
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
			this.set({ timestamp : Date.parse(this.get('date')) });
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
			var rendered = _.template(ns.templates.chatRowTemplate.html(), {
				date : this.model.get('date'),
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
			return -1 * chatMsgModel.get('timestamp');
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
		
		ns.templates.chatRowTemplate = $('#chatRowTemplate');
		
		ns.chatLogView = new ns.ChatLogView();
		
		$('.loading').hide();
		
		$.getJSON('/kennyquotemachine.com/backstage/messages/getLatest',function(response) {
			$(response.messages).each(function(){
				
				var message = this;
				var date = new Date(Date.parse(this.Message.created));
				
				ns.chatLog.add(new ns.ChatMsg({
					date : date.format(ns.dateFormat),
					text : message.Message.text,
					handle : message.User.username
				}));
			});
		});
	}
		
	$(document).ready(function() {
		ns.init();
		if($('body').hasClass('route-action-admin-group-chat')) {
			ns.initChat();
		}
	});
	
})(jQuery, GroupChat);