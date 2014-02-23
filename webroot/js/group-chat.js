/**
 * Group Chat App
 * @author Patrick McFern <mcferno AT gmail.com>
 */
Backstage['GroupChat'] = {
	dateFormat : 'mmm-dd, h:MM:ss TT',
	templates : {
		chatRowTemplate : false
	},
	lastAck : 0,
	windowFocus : true,
	config : {},
	idleTimeMax : 300,
	userIcon : '<i class="glyphicon glyphicon-user"></i>',
	playNotifications : null,
	playMentions : null,
	users : []
};
(function($, ns, env, document, window) {
	"use strict";

	ns.config = env['Config']['GroupChat'] || {};

	var doc = $(document),
		win = $(window);

	doc
		.on('click','.chat-bar button', function(e) {
			e.preventDefault(); // disable all buttons's defaults
		})
		.on('click','.chat-bar .msg button[type="submit"]', function(e) {
			e.preventDefault(); // disable all buttons's defaults
			ns.submitMessage();
		})
		.on('click','.online-count',function(e){
			e.preventDefault(); // disable all buttons's defaults
			$('.slideout').slideToggle();

		})
		.on('click', '.highlight', function() {
			$(this).addClass('seen');
		})
		.on('message-sent', function() {
			$('.highlight').addClass('seen');
		});
	win
		// track window focus for notifications
		.on('focus',function(){
			ns.windowFocus = true;
		})
		.on('blur',function() {
			ns.windowFocus = false;
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
		tagName : 'div',
		className : 'row',
		initialize : function() {
			this.render();
			if(this.$el.find('.active-user').length) {
				this.el.className = this.el.className + ' highlight';
			}
		},
		render : function() {
			var timestamp = this.model.get('timestamp');
			var date = new Date(timestamp * 1000);

			var msg = ns.nl2br(this.model.get('text'));
			msg = ns.autolinkUrls(msg);
			msg = ns.highlightCallouts(msg);

			if(!ns.config.mobile) {
				msg = ns.autoViewImages(msg, this.model.get('handle'));
				msg = ns.autoEmbedVideos(msg);
			}

			var rendered = _.template(ns.templates.chatRowTemplate, {
				date : ns.formatDate(date),
				username : this.model.get('handle'),
				message : msg
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
			return ns.chatOrder * parseInt(chatMsgModel.get('timestamp'), 10);
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
				if(idx === 0) {
					this.$el.prepend(chatMsgModel.view.el);

				// new view belongs at the end
				} else if(idx == ns.chatLog.length - 1) {
					this.$el.append(chatMsgModel.view.el);

				// new view has inner placement (out of order from server?)
				} else {
					var ideal = this.$el.children('.'+chatMsgModel.view.className).get(idx);
					$(ideal).before(chatMsgModel.view.el);
				}
			} else {
				this.$el.append(chatMsgModel.view.el);
			}

			if(
				ns.playMentions === true
				&& $(chatMsgModel.view.el).hasClass('highlight')
				&& chatMsgModel.get('handle') != ns.config.self
			) {

				ns.sounds.attention.play();

			} else if(
				ns.playNotifications === true
				&& chatMsgModel.get('handle') != ns.config.self
			) {

				ns.sounds.notify.play();

			}
		}
	});

	// submits a message to the server
	ns.submitMessage = function() {
		var text = ns.msgBar.val();
		if(text.length === 0 || $.trim(text).length === 0) {
			ns.msgBar.val('');
			return;
		}

		var now = new Date();
		var date = ns.formatDate(now);

		var postData = {
			Message : {
				text: text,
				model: ns.config.scope
			}
		};
		if($.type(ns.config.scopeId) === 'string') {
			postData.Message.foreign_id = ns.config.scopeId;
		}
		if(ns.lastAck !== 0) {
			postData.ack = ns.lastAck;
		}

		$.ajax({
			type: 'POST',
			url: env.backendURL + 'messages/add',
			data: postData,
			dataType: 'json',
			success : ns.processHeartbeat
		});

		ns.msgBar.val('');
		doc.triggerHandler('message-sent');
	};

	/**
	 * Deeper inspection on keypresses to enrich the messaging options
	 */
	ns.messageInput = function(event) {
		var msg = ns.msgBar.val();
		switch(event.keyCode) {

			// ESC to erase the line
			case 27:
				ns.msgBar.val('');
				break;

			// Backspace
			case 8:
				if(ns.config.scope != 'Chat') {	break; }
				// erase the @mention block, keeping only the @
				if(msg.toLowerCase().match(/@[_a-z0-9-]+$/)) {
					event.preventDefault();
					ns.msgBar.val(msg.replace(/@[_a-zA-Z0-9-]+$/, '@'));
				}
				break;

			// TAB for autocompletion
			case 9 :
				if(ns.config.scope != 'Chat') {	break; }
				// match a partial @ callout
				var user_callout = msg.toLowerCase().match(/@([_a-z0-9-]*)$/);
				if(user_callout) {
					event.preventDefault();

					var match = false;

					if(user_callout[0] == '@') {
						// '@' with only one user online
						if(ns.users.length === 2) {
							match = (ns.users[0] == ns.config.self) ? ns.users[1] : ns.users[0];
						} else {
							break;
						}

					} else {
						for(var i = 0; i < ns.users.length; i++) {
							if(ns.users[i].toLowerCase().indexOf(user_callout[1]) === 0) {
								match = ns.users[i];
								break;
							}
						}
					}

					// shortcut for @all
					if(!match && user_callout[1].indexOf('a') === 0) {
						match = 'all';
					}

					// set the autocompletion
					if(match !== false) {
						ns.msgBar.val(msg.replace(/@[_a-zA-Z0-9-]*$/, '@' + match + ' '));
					}
				}
				break;

			// Return key
			case 13:
				// Shift-return inserts a new line
				if(event.shiftKey !== true) {
					event.preventDefault();
					ns.submitMessage();
				}
				break;

			default: break;
		}
	};

	// testing function which injects messages directly into the chat, bypassing the server
	ns.addMessage = function(date,timestamp,text,handle) {
		var rowData = _.template(ns.templates.chatRowTemplate, {
			date : date,
			timestamp : timestamp,
			username : handle,
			message : text
		});
		var row = $('<tr>').addClass('chat-row').html(rowData);
		if(ns.messageCount() % 2 === ns.chatRowStripe) {
			row.addClass('alt-row');
		}
		row.hide();
		ns.chatWindow.prepend(row);
		row.show().css('display','table-row');
	};

	// converts new-line characters to HTML breaks
	ns.nl2br = function(text) {
		return (text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
	};

	// convert URLs to active hyperlinks
	ns.autolinkUrls = function(text) {
		var url_regex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&$@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
		return text.replace(url_regex, '<a href="$1" target="_blank">$1</a>');
	};

	// highlight @mentions within a given text
	ns.highlightCallouts = function(text) {

		// mark other mentions
		var markup = text.replace(/(^|\W)(@[A-Za-z0-9_-]+)(\W|$)/g, '$1<span class="user-mention">$2</span>$3');

		// highlight personal @mentions
		if(ns.config.self) {
			var username_regex = new RegExp("user-mention\"\>@(" + ns.config.self + "|all)\<", 'gi');
			markup = markup.replace(username_regex, 'active-user">@' + ns.config.self + '<');
		}

		return markup;
	};

	// parses text for image links and converts them to embedded images
	ns.autoViewImages = function(text, handle) {
		// depends on being previously hyperlinked
		var imgRegex = "<a[^>]*>([^<]+\.(jpeg|jpg|jpe|png|gif|bmp|webp)[^<]*)<\/a>";
		var linkToImageURL = text.match(new RegExp(imgRegex, 'g'));

		if(linkToImageURL) {
			// process each image url found
			$.each(linkToImageURL, function(idx, anchor) {
				var parts = anchor.match(new RegExp(imgRegex));
				var imageTag = _.template(ns.templates.embeddedImage, { username : handle, url : parts[1] });
				text = text.replace(parts[0], imageTag);
			});
		}
		return text;
	};

	// parses text for video site links, converting them to embed codes
	ns.autoEmbedVideos = function(text) {
		var youtubeLink = text.match(/<a.*>(.*(youtube\.com\/watch.*v=|youtu\.be\/)([\-\_a-zA-Z0-9]*).*)<\/a>/);
		var embedTag = false;
		if(youtubeLink) {
			embedTag = _.template(ns.templates.embeddedYoutube, { video_id : youtubeLink[3], url : youtubeLink[1] });
			text = text.replace(youtubeLink[0], embedTag);
		}

		var vimeoLink = text.match(/<a.*>(.*vimeo\.com\/([\-\_a-zA-Z0-9]*).*)<\/a>/);
		if(vimeoLink) {
			embedTag = _.template(ns.templates.embeddedVimeo, { video_id : vimeoLink[2], url : vimeoLink[1] });
			text = text.replace(vimeoLink[0], embedTag);
		}

		return text;
	};

	// removes an enriched message content, reverting it back to a simple hyperlink
	ns.collapseMessage = function(event) {
		event.preventDefault();
		var link = $(this);
		link.prevUntil('.close','.posted-content').remove();
		link.prevUntil('.close','.original-link').show();
		link.remove();
	};

	// returns the number of messages currently in the chat window
	ns.messageCount = function() {
		if(!ns.chatWindow) {
			return 0;
		}
		return ns.chatLog.length;
	};

	// polls the server for new status
	ns.sendHeartbeat = function() {
		var data = {
			scope : ns.config.scope
		};

		if(typeof ns.chatWindow != 'undefined') {
			data.ack = ns.lastAck;
		}
		if($.type(ns.config.scopeId) === 'string') {
			data.key = ns.config.scopeId;
		}
		$.ajax({
			data : data,
			url : env.backendURL + 'users/heartbeat',
			success : ns.processHeartbeat
		});
	};

	// processes the results from a server status update
	ns.processHeartbeat = function(data) {
		var allUsers = '';
		var idleUsers = [];
		var activeUsers = [];
		for(var i=0;i<data.online.length;i++) {
			allUsers += data.online[i].User.username;
			if(i != data.online.length - 1) {
				allUsers += ', ';
			}

			if(data.online[i].User.last_ack_delta < ns.idleTimeMax) {
				activeUsers.push(data.online[i].User.username);
			} else {
				idleUsers.push(data.online[i].User.username);
			}
		}

		// track all online users
		ns.users = activeUsers.concat(idleUsers);

		var notificationCount = data.new_messages + data.new_updates;

		ns.userCount
			.data('title',allUsers)
			.text(data.online.length);
		$('.slideout .names').text(allUsers);

		if(activeUsers.length) {
			ns.activeList.empty().show().html(
				'<li>' + ns.userIcon + ' ' + activeUsers.join('</li><li>' + ns.userIcon + ' ') + '</li>'
			);
		} else {
			ns.activeList.hide();
		}
		ns.activeCount.text(activeUsers.length);

		if(idleUsers.length) {
			ns.idleList.empty().show().html('<li>' + idleUsers.join('</li><li>') + '</li>');
		} else {
			ns.idleList.hide();
		}
		ns.idleCount.text(idleUsers.length);

		ns.updateNotifier.text(data.new_updates);
		ns.msgNotifier.text(data.new_messages);

		if(data.new_messages === 0) {
			ns.msgNotifier.addClass('badge-off');
		} else {
			ns.msgNotifier.removeClass('badge-off');
		}

		if(data.new_updates === 0) {
			ns.updateNotifier.addClass('badge-off');
		} else {
			ns.updateNotifier.removeClass('badge-off');
		}

		if(notificationCount !== 0) {
			var title = '';
			if(data.new_messages > 0) {
				title += '(' + data.new_messages + ') ';
			}
			if(data.new_updates > 0) {
				title += '[' + data.new_updates + '] ';
			}
			document.title = title + ns.originalTitle;
		} else {
			document.title = ns.originalTitle;
		}

		// on chat, process new messages
		if(typeof ns.chatWindow !== 'undefined') {

			if(ns.lastAck > 0) {
				if(ns.playMentions !== false) {
					ns.playMentions = true;
				}
				if(ns.playNotifications !== false) {
					ns.playNotifications = true;
				}
			}

			if(data.ack > ns.lastAck && ns.windowFocus) {
				ns.lastAck = data.ack;
			}

			if(data.messages.length > 0) {
				ns.processMessages(data.messages);
			}
		}
	};

	// parses a series of new messages
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
	};

	// converts a message date to a human-friendly format
	ns.formatDate = function(dateObj) {
		var date = ns.padNumber((dateObj.getMonth() + 1))
			+ '/' + ns.padNumber(dateObj.getDate())
			+ ' ' + ns.padNumber(dateObj.getHours())
			+ ':' + ns.padNumber(dateObj.getMinutes())
			+ ':' + ns.padNumber(dateObj.getSeconds());
		return date;
	};

	ns.padNumber = function(str) {
		return String('00'+str).match(/[0-9]{2}$/);
	};

	// wrapper for a multi-format audio element, with browser support detection
	ns.sound = function(config, volumeLevel) {
		var obj = this;
		var audio = false;

		var supportsFormat = function(mimetype) {
			var elem = document.createElement('audio');
			if(typeof elem.canPlayType == 'function'){
				var playable = elem.canPlayType(mimetype);
				if((playable.toLowerCase() == 'maybe')||(playable.toLowerCase() == 'probably')){
					return true;
				}
			}
			return false;
		};

		// find and load a supported audio file
		for(var type in config) {
			if(supportsFormat(config[type]['format'])) {
				audio = document.createElement('audio');
				audio.src = config[type]['file'];
				audio.load();
				audio.volume = (typeof volumeLevel !== "undefined") ? volumeLevel : 1.0;
				break;
			}
		}

		obj.play = function() {
			if(audio) {
				audio.play();
				return true;
			}
			return false;
		};
	};

	// initializes the bare-bones chat status functionality
	ns.init = function() {
		ns.msgNotifier = $('.navbar .message-count');
		ns.updateNotifier = $('.navbar .updates-count');
		ns.userCount = $('.online-count');
		ns.activeList = $('.online-users');
		ns.activeCount = $('.active-count');
		ns.idleList = $('.idle-users');
		ns.idleCount = $('.idle-count');
	};


	// initializes the full chat messaging interface
	ns.initChat = function() {
		ns.msgBar = $('.chat-bar .msg textarea');
		ns.chatWindow = $('.chat-window .chat');
		ns.handle = $('.chat-bar .handle').text();
		ns.loadingIndicator = $('#loaderAnim');

		ns.templates.chatRowTemplate = $('#chatRowTemplate').html();
		ns.templates.embeddedImage = $('#embeddedImageTemplate').html();
		ns.templates.embeddedYoutube = $('#embeddedYouTubeTemplate').html();
		ns.templates.embeddedVimeo = $('#embeddedVimeoTemplate').html();

		ns.chatWindow.on('click', '.chat-row .close', ns.collapseMessage);

		ns.chatOrder = ($.type(ns.config.order) === "string" && ns.config.order === 'asc') ? 1 : -1;
		ns.chatRowStripe = (ns.chatOrder === 1) ? 1 : 0;
		ns.chatLogView = new ns.ChatLogView();

		// Chat specific configurations
		if(ns.config.scope == 'Chat') {
			ns.msgBar.focus();

			// initialize for non-mobile users
			if(ns.config.mobile === false) {
				ns.initChatSounds();
			}
		}

		if(ns.config.mobile === false) {
			ns.msgBar.on('keydown', ns.messageInput);
		}

		$('.loading').hide();
	};

	// configures Chat notification sounds
	ns.initChatSounds = function() {
		ns.soundConfig = {
			notifications : $('.notification-setting'),
			mentions : $('.mention-setting')
		};

		ns.sounds = {
			attention : new ns.sound(ns.config.tones['alert'], 0.5),
			notify : new ns.sound(ns.config.tones['notify'], 0.3)
		};

		var cookieConfig = {
			expires: 30, path: ns.config.url
		};

		// read audio configuration from cookie
		if($.cookie('play_notifications') == 'no') {
			ns.playNotifications = false;
		}
		$.cookie('play_notifications', (ns.playNotifications === false) ? 'no' : 'yes', cookieConfig);

		if($.cookie('play_mentions') == 'no') {
			ns.playMentions = false;
		}
		$.cookie('play_mentions', (ns.playMentions === false) ? 'no' : 'yes', cookieConfig);

		// update interface to match preference
		if(ns.playNotifications === false) {
			ns.soundConfig.notifications.find('.btn').toggle();
		}
		if(ns.playMentions === false) {
			ns.soundConfig.mentions.find('.btn').toggle();
		}

		// notification tone settings
		ns.soundConfig.notifications.on('click', '.state-off', function() {
			ns.soundConfig.notifications.find('.btn').toggle();
			ns.playNotifications = true;
			$.cookie('play_notifications', 'yes', cookieConfig);
		});
		ns.soundConfig.notifications.on('click', '.state-on', function() {
			ns.soundConfig.notifications.find('.btn').toggle();
			ns.playNotifications = false;
			$.cookie('play_notifications', 'no', cookieConfig);
		});

		// alert tone settings
		ns.soundConfig.mentions.on('click', '.state-off', function() {
			ns.soundConfig.mentions.find('.btn').toggle();
			ns.playMentions = true;
			$.cookie('play_mentions', 'yes', cookieConfig);
		});
		ns.soundConfig.mentions.on('click', '.state-on', function() {
			ns.soundConfig.mentions.find('.btn').toggle();
			ns.playMentions = false;
			$.cookie('play_mentions', 'no', cookieConfig);
		});
	};

	doc.ready(function() {
		ns.originalTitle = document.title;
		ns.init();

		if(typeof ns.config.scope != 'undefined' && ns.config.scope == 'Chat') {
			ns.heartbeat = setInterval(ns.sendHeartbeat,3000);
		} else {
			ns.heartbeat = setInterval(ns.sendHeartbeat,45000);
		}

		if($('.chat-window').length) {
			ns.initChat();
		}

		ns.sendHeartbeat();
	});

})(jQuery, Backstage['GroupChat'], AppEnv, document, window);
