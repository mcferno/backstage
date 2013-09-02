;
/**
 * Meme Generator App
 * @author Patrick McFern <mcferno AT gmail.com>
 */
var MemeGenerator = {
	
	// timestamp of the last canvas render
	lastRender : 0,
	
	// whether to update the rendering at every keypress
	liveMode : false,
	
	// current backdrop
	currentImage : false,
	
	// image in use based on series of backdrop options
	imageOffset : -1,
	
	// main font + fallbacks for various devices
	fontFamily : 'Impact, Passion One, sans-serif',
	
	// font size coefficient to scale accordingly to height
	fontToHeightScale : {
		base : (60 / 450), // 0.0711+, from 32pt @ 450px
		ios: (57 / 450), // to adjust for a different font on iOS
		android : (60 / 450)
	},

	fontLineHeight : {
		base : 1.4,
		ios : 1.1,
		android : 1.1
	},
	
	// font stroke size coefficient to scale accordingly to width
	fontStrokeWidthScale : 0.01,
	
	// series of coordinates within the canvas
	coords : {},

	// set of external configurations
	config : {}
};

/*global AppBaseURL */
(function($, ns) {
	"use strict";
	
	$(document)
		.on('click','.meme-generator button', function(e) {
			e.preventDefault(); // disable all buttons's defaults
		})
		.on('click','.save-image',function() {
			ns.render();
			ns.canvasToImage();
		})
		.on('keyup','#first-line, #last-line',function() {
			if(ns.liveMode === true && ns.currentImage !== false) {
				ns.render();
				ns.canvasToImage();
			}
		})
		.on('click','.live-mode',function() {
			ns.toggleLiveMode();
			ns.render();
			if(ns.liveMode === true) {
				ns.canvasToImage();
			}
		})
		.on('change','.canvasSize',function() {
			ns.matchCanvasToImage();
			ns.render();
			ns.canvasToImage();
		})
		.on('click','.reset',function() {
			window.location.reload();
		})
		.on('click','.choose-background', function() {
			if(ns.images.length > 1) {
				ns.rotateImage();
			} else {
				ns.workspace.hide();
				ns.imagePicker.show();
			}
		})
		.on('click','.meme-generator .save',function() {
			$(this).button('loading');
			ns.sendImageToServer(ns.config.type, false);
		})
		.on('click','.meme-generator .save-jump',function() {
			$(this).button('loading');
			ns.sendImageToServer(ns.config.type, true);
		});
		

	ns.loadImage = function(obj) {
		
		// image is not yet loaded
		if(!obj.image) {
			$('.choose-background').button('loading');
			obj = {
				href : obj
			};
			obj.image = new Image();
			obj.image.onload = function() {
				ns.matchCanvasToImage();
				ns.render();
				ns.canvasToImage();
				$('.choose-background').button('reset');
			};
			obj.image.src = obj.href;
			ns.currentImage = obj.image;
		
		// set the image and refresh the canvas
		} else {
			ns.currentImage = obj.image;
			ns.matchCanvasToImage();
			ns.render();
			ns.canvasToImage();
		}
	};

	/**
	 * Cycles to the next backdrop option in the series (wraps at end)
	 */
	ns.rotateImage = function() {
		ns.imageOffset = (ns.imageOffset + 1) % ns.images.length;
		var obj = ns.images[ns.imageOffset];

		ns.loadImage(obj);
	};

	ns.imageSelection = function(event) {
		var obj = $(event.target).data('full-image');

		ns.imagePicker.hide();
		ns.loadImage(obj);
		ns.firstLineText.focus();
	};
	
	/**
	 * Selects the Image Size dropdown which matches the orientation of the
	 * current image.
	 */
	ns.matchCanvasToImage = function() {
		var sizing = $('.canvasSize option:selected');
		
		if(ns.currentImage.height !== 0 && ns.currentImage.width !== 0) {
			var max = sizing.data('max');
			var desiredWidth, desiredHeight;
			
			// keep the image in its original form
			if(max == 'full') {
				desiredWidth = ns.currentImage.width;
				desiredHeight = ns.currentImage.height;
			} else if(ns.currentImage.width > ns.currentImage.height) {
				desiredWidth = max;
				desiredHeight = parseInt(max / ns.currentImage.width * ns.currentImage.height, 10);
			} else {
				desiredHeight = max;
				desiredWidth = parseInt(max / ns.currentImage.height * ns.currentImage.width, 10);
			}
			
			// verify that the canvas size matches the selected option
			if(ns.canvas.height != desiredHeight || ns.canvas.width != desiredWidth) {
				ns.canvas.height = desiredHeight;
				ns.canvas.width = desiredWidth;
				
				ns.adaptToScale();
			}
		}
	};
	
	/**
	 * Converts the canvas to an inline downloadable JPEG. Expensive operation.
	 */
	ns.canvasToImage = function() {
		var image = $('#rendered');
		
		if(image.length === 0) {
			image = $('<img id="rendered" />');
			$(ns.canvas).parent().append(image);
		}
		
		image.get(0).src = ns.canvas.toDataURL('image/jpeg');
		ns.workspace.show();
	};
	
	/**
	 * Saves a base64 encoded image on the server
	 *
	 * @param {String} Image type, determines inner behavior tweaks
	 * @param {Boolean} Whether to jump to the image view page after save
	 */
	ns.sendImageToServer = function(type, jumpTo) {
		if(ns.currentImage.height > 0 && ns.currentImage.width > 0) {

			var payload = {
				image : ns.canvas.toDataURL('image/jpeg')
			};

			var contest = (type === 'Contest');

			if(contest) {
				payload.type = 'Contest';
				payload.contestId = ns.config.contestEntryId;
			}

			$.post(
				AppBaseURL + 'backstage/assets/save',
				payload,
				function(data) {
					if(data.image_saved) {

						// jump to the view page
						if(jumpTo && data.view_url) {
							window.location = data.view_url;

						// reset the button to allow more memes
						} else {
							$('.meme-generator .save')
								.button('reset')
								.tooltip('show');

							// hide notification after delay
							setTimeout(function() {
								$('.meme-generator .save').tooltip('hide');
							}, 3500);

							$('.meme-generator .view-last').show().attr('href', data.view_url);
						}
					}
				}
			);
		}
	};
	
	/**
	 * Enable/disable on-the-fly image-generation mode
	 */
	ns.toggleLiveMode = function() {
		ns.liveMode = !ns.liveMode;
		$('button.live-mode')
			.toggleClass('btn-inverse')
			.toggleClass('btn-success')
			.find('.glyphicon')
				.toggleClass('glyphicon-remove')
				.toggleClass('glyphicon-ok');
		$('button.save-image')
			.toggleClass('btn-inverse')
			.toggleClass('btn-primary');
	};
	
	/**
	 * Paints all data to the canvas.
	 */
	ns.render = function() {
		var canvas = ns.canvas;
		var context = ns.context;
		
		if(ns.currentImage === false) {
			var backdrop = context.createLinearGradient(0, 0, canvas.width, canvas.height);
			backdrop.addColorStop(0, "#000");
			backdrop.addColorStop(1, "#FC6626");
			context.fillStyle = backdrop;
			context.fillRect(0, 0,canvas.width, canvas.height);
		} else {
			context.drawImage(ns.currentImage, 0, 0, canvas.width, canvas.height);
		}
		
		var firstLineText = ns.firstLineText.val().toUpperCase();
		var lastLineText = ns.lastLineText.val().toUpperCase();
		
		ns.writeText(firstLineText, true);
		ns.writeText(lastLineText);
		
		ns.lastRender = new Date().getTime();
	};
	
	/**
	 * Writes text to the canvas, auto-adjusting for word-wrap
	 *
	 * @param {String} text: Text to print on the canvas
	 * @param {Boolean} top: Whether this is top or bottom text
	 */
	ns.writeText = function(text, top) {
		ns.context.save();
		
		top = (top === true);
		var lineWidth = parseInt(ns.canvas.width * 0.97, 10); // slight width padding
		var textWidth = ns.context.measureText(text).width;

		var bestFit = parseInt(ns.fontSize, 10) + "pt " + ns.fontFamily;
		ns.context.font = bestFit;
		var lines = ns.breakTextIntoLines(text, lineWidth);
		var originalLines = lines.length;
		
		// determine if a font size change yeilds less rows
		if(lines.length > 1) {
			var idealLines = Math.ceil(lines.length / 2);
			
			// determine the optimal font-size
			for(var i = 1; i < 5; i++) {
				
				ns.context.font = parseInt(ns.fontSize * (1 - 0.1 * i), 10) + "pt " + ns.fontFamily;
				lines = ns.breakTextIntoLines(text, lineWidth);
				
				if(lines.length < originalLines) {
					bestFit = ns.context.font;

					// we've hit the ideal row count, stop here
					if(lines.length === idealLines) {
						break;

					// try smaller font sizes until we hit less rows
					} else {
						originalLines -= 1;
					}
				}
			}
		}

		ns.context.font = bestFit;
		lines = ns.breakTextIntoLines(text, lineWidth);
		
		var emWidth = parseInt(ns.context.measureText('M').width * ns.fontHeightPadding, 10);
		var offsetY = (top) ? emWidth : parseInt(ns.canvas.height * 0.97 - ((lines.length - 1) * emWidth), 10);
		
		// write out each line, respecting inner spacing
		for(var iter = 0; iter < lines.length; iter++) {
			var line = lines[iter].join(' ');
			ns.context.strokeText(line, ns.coords.center.x, offsetY + iter * emWidth);
			ns.context.fillText(line, ns.coords.center.x, offsetY + iter * emWidth);
		}
		
		ns.context.restore();
	};
	
	/**
	 * Breaks a string into an array of substrings, all of which fit within
	 * the provided width maximum. The font size of the current canvas is used
	 * in text width calculations.
	 *
	 * Attempts to balance the widths of the strings by moving words between the
	 * lines, in an attempt to wrap the text evenly (giving the greatest font size)
	 *
	 * @param {String} text: Text to break into smaller substrings
	 * @param {Integer} lineWidth: Maximum width in pixels any line can span
	 * @return {Array} Substrings of the text, respecting the width
	 */
	ns.breakTextIntoLines = function(text, lineWidth) {
		var lines = [];
		var words = text.replace(/^\s+|\s+$/,'').replace(/\s\s*/g,' ').split(' '); //'
		var row = -1;
		var rowSum = 0;
		var gapWidth = ns.context.measureText(' ').width;

		// iterate through the words, collecting length and the greedy-wrap max line count.
		for(var iter = 0; iter < words.length ; iter++) {
			
			var cost = ns.context.measureText(words[iter]).width;

			// single word exceeds line-width, split word onto new line
			if(cost > lineWidth) {
				var mid = Math.floor(words[iter].length / 2);
				var halfWord = words[iter].substr(0, mid);
				var remainingWord = words[iter].substr(mid);
				var previousWords = words.splice(0, iter);
				words[0] = remainingWord;
				words.unshift(halfWord + '-');
				words = previousWords.concat(words);

				// recompute costs of this newly split line
				cost = ns.context.measureText(words[iter]).width;
			}
			
			// word falls on a new line
			if(typeof lines[row] == 'undefined' || rowSum + gapWidth + cost > lineWidth) {
				row++;
				rowSum = 0;
				lines[row] = [];
			}
			
			lines[row].push(words[iter]);
			rowSum = (rowSum === 0) ? cost : rowSum + gapWidth + cost;
		}
		
		// balance rows lengths if there are more than one
		if(row > 0) {
			
			// iterate backwards through the lines, balancing their widths pairwise
			for(var i = lines.length - 1; i > 0; i--) {
				var j = i-1;
				
				for(var k = lines[j].length - 1; k > 0; k--) {
					
					// difference in row lengths before word is moved
					var deltaBefore = Math.abs(ns.context.measureText(lines[j].join(' ')).width - ns.context.measureText(lines[i].join(' ')).width);

					// move a word down
					lines[i].unshift(lines[j].pop());
					
					// difference in row lengths after word is moved
					var deltaAfter = Math.abs(ns.context.measureText(lines[j].join(' ')).width - ns.context.measureText(lines[i].join(' ')).width);
					
					// restore if difference is now worse, skip the remainder of the words on the line
					if(deltaAfter >= deltaBefore) {
						lines[j].push(lines[i].shift());
						break;
					}
				}
			}
		}
		
		return lines;
	};
	
	
	/**
	 * Initialize the app by attaching DOM elements
	 */
	ns.init = function() {
		ns.canvas = $('#rasterizer').get(0); // dom object
		ns.context = ns.canvas.getContext('2d');
		ns.images = ns.config.baseImages;
		ns.firstLineText = $('#first-line');
		ns.lastLineText = $('#last-line');
		ns.imageTemplate = $('#imagePickerTemplate').html();
		ns.imagePicker = $('#backgrounds');
		ns.imageChoices = $('#backgrounds .mini-wall');
		ns.workspace = $('.workspace');

		ns.paging = {
			tag_filter : $('#image_tags'),
			user_filter : $('#image_owners'),
			more : $('.load-more'),
			page : 1,
			max_page : 1,
			filters : false
		};
		ns.paging.tag_filter.on('change', ns.invokePicker);
		ns.paging.user_filter.on('change', ns.invokePicker);
		ns.paging.more.on('click', ns.invokePicker);

		ns.imageChoices.on('click', '.image-option', ns.imageSelection);
		
		if(ns.images.length === 1) {
			$('.choose-background').hide();
		}

		$('.meme-generator .save').tooltip({
			animation : true,
			placement : 'top',
			trigger : 'manual',
			title : 'Image saved successfully!'
		});

		// scale font relative to canvas, avoiding sub-pixel
		ns.fontScale = ns.fontToHeightScale['base'];
		ns.fontHeightPadding = ns.fontLineHeight['base'];
		if(navigator.userAgent.match(/(iPhone|iPod)/i)) {
			ns.fontScale = ns.fontToHeightScale['ios'];
			ns.fontHeightPadding = ns.fontLineHeight['ios'];
		} else if(navigator.userAgent.match(/(Android)/i)) {
			ns.fontScale = ns.fontToHeightScale['android'];
			ns.fontHeightPadding = ns.fontLineHeight['android'];
		}

		// ensure the scale adapting is done at least once
		ns.adaptToScale();
	};
	
	/**
	 * Calculates all sizing & placements which are relative to the canvas
	 * dimensions. Must be ran after every change in canvas size.
	 */
	ns.adaptToScale = function() {
		ns.context.textAlign = "center";
		ns.context.fillStyle = "#FFF";
		ns.context.lineStyle = "#000";

		var canvasHeight = (ns.canvas.width > ns.canvas.height)?ns.canvas.height:ns.canvas.width;
		var canvasWidth = (ns.canvas.width > ns.canvas.height)?ns.canvas.width:ns.canvas.height;
		ns.fontSize = parseInt(ns.fontScale * canvasHeight, 10);
		ns.context.font = ns.fontSize + "pt " + ns.fontFamily;
		ns.context.lineWidth = parseInt(ns.fontStrokeWidthScale * canvasWidth, 10);
		
		// calculate the canvas centerpoint
		ns.coords.center = {
			x : ns.canvas.width/2,
			y : ns.canvas.height/2
		};
	};
	
	function isCanvasSupported() {
		var elem = document.createElement('canvas');
		return !!(elem.getContext && elem.getContext('2d'));
	}

	/**
	 * Initializes the image selector, allowing users to pick a Meme image within
	 * the interface directly.
	 */
	ns.invokePicker = function() {

		ns.imagePicker.show();

		var tag = ns.paging.tag_filter.val();
		var user = ns.paging.user_filter.val();

		var endpoint = AppBaseURL + 'backstage/assets/find';

		if($.trim(tag) != '') {
			endpoint += '/tag:' + tag;
		}
		if($.trim(user) != '') {
			endpoint += '/user:' + user;
		}
		var filter = tag + '/' + user;

		// change in filters, or initial invoke.
		if(ns.paging.filters === false || ns.paging.filters != filter) {
			ns.paging.filters = filter;
			ns.paging.page = 1;
			ns.paging.max = 1;
			ns.imageChoices.html('');

		// existing filters, advance in pagination if possible
		} else if(ns.paging.page < ns.paging.max) {
			ns.paging.page += 1;
		}

		endpoint += '/page:' + ns.paging.page;

		$.ajax({
			url : endpoint,
			success : ns.showImageChoices
		})
	};

	/**
	 * AJAX image feed callback. Parse the results and adds them to the list of
	 * usage image options.
	 */
	ns.showImageChoices = function(payload) {
		var images = '';

		ns.paging.page = parseInt(payload.page, 10);
		ns.paging.max = parseInt(payload.max_page, 10);

		if(ns.paging.page < ns.paging.max) {
			ns.paging.more.show();
		} else {
			ns.paging.more.hide();
		}

		// no results for the current filters
		if(payload.images.length === 0 && ns.paging.page === 1) {
			images += '<p class="alert alert-danger"><strong>Sorry</strong>, no images found. Try removing or changing search filters.</p>';
		}

		// compile image matches
		$(payload.images).each(function(idx, data) {
			images += _.template(ns.imageTemplate, {
				thumb_url : AppBaseURL + 'img/' + data.Asset['image-tiny'],
				full_url : AppBaseURL + 'img/' + data.Asset['image-full']
			});
		});

		ns.imageChoices.append(images);
	};

	$(document).ready(function() {
		// early exit if canvas is not supported
		if(!isCanvasSupported()) {
			$('.no-canvas').show().siblings().hide();
			return;
		}
		
		// pre-select the larger meme size based on available screen real estate
		if(window.outerWidth > 850) {
			$('.canvasSize option:selected').removeAttr('selected');
			var larger = $('.canvasSize [data-max=800]');
			larger.attr('selected','selected');
		}
		
		ns.init();

		if(ns.images.length === 0) {

			ns.invokePicker();

		} else {
		
			// randomly shuffle the available backdrops
			ns.images.sort(function() { return 0.5 - Math.random(); });
			
			// choose the first image (random) to display
			ns.rotateImage();
		}

		// toggle live-mode on by default
		ns.toggleLiveMode();
	});
	
})(jQuery, MemeGenerator);