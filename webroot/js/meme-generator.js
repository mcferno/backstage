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
	fontFamily : 'Impact, Futura-CondensedExtraBold, sans-serif',
	
	// font size coefficient to scale accordingly to height
	fontToHeightScale    : (60 / 450), // 0.0711+, from 32pt @ 450px
	fontToHeightScaleiOS : (57 / 450), // to adjust for a different font on iOS
	
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
			if(ns.liveMode === true) {
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
		.on('click','.choose-background',function() {
			ns.swapImages();
		})
		.on('click','.meme-generator .save',function() {
			$(this).button('loading');
			ns.sendImageToServer(ns.config.type, false);
		})
		.on('click','.meme-generator .save-jump',function() {
			$(this).button('loading');
			ns.sendImageToServer(ns.config.type, true);
		});
		

	/**
	 * Cycles to the next backdrop option in the series (wraps at end)
	 */
	ns.swapImages = function() {
		ns.imageOffset = (ns.imageOffset + 1) % ns.images.length;
		var obj = ns.images[ns.imageOffset];
		
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
			.find('i')
				.toggleClass('icon-remove')
				.toggleClass('icon-ok');
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
		
		var firstLineText = ns.firstLineText.attr('value').toUpperCase();
		var lastLineText = ns.lastLineText.attr('value').toUpperCase();
		
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
		
		var emWidth = parseInt(ns.context.measureText('M').width * 1.4, 10);
		var offsetY = (top) ? emWidth : parseInt(ns.canvas.height * 0.97 - ((lines.length - 1) * emWidth), 10);
		
		// write out each line, respecting inner spacing
		for(var iter = 0; iter < lines.length; iter++) {
			var line = lines[iter].join(' ');
			ns.context.strokeText(line, ns.coords.center.x,offsetY + iter * emWidth);
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
		ns.canvas = $('#workspace').get(0); // dom object
		ns.context = ns.canvas.getContext('2d');
		ns.images = ns.config.baseImages;
		ns.firstLineText = $('#first-line');
		ns.lastLineText = $('#last-line');
		
		if(ns.images.length < 2) {
			$('.choose-background').hide();
		}

		$('.meme-generator .save').tooltip({
			animation : true,
			placement : 'top',
			trigger : 'manual',
			title : 'Image saved successfully!'
		});
	};
	
	/**
	 * Calculates all sizing & placements which are relative to the canvas
	 * dimensions. Must be ran after every change in canvas size.
	 */
	ns.adaptToScale = function() {
		ns.context.textAlign = "center";
		ns.context.fillStyle = "#FFF";
		ns.context.lineStyle = "#000";
		
		// scale font relative to canvas, avoiding sub-pixel
		var fontScale = navigator.userAgent.match(/(iPhone|iPod)/i)?ns.fontToHeightScaleiOS:ns.fontToHeightScale;
		var canvasHeight = (ns.canvas.width > ns.canvas.height)?ns.canvas.height:ns.canvas.width;
		var canvasWidth = (ns.canvas.width > ns.canvas.height)?ns.canvas.width:ns.canvas.height;
		ns.fontSize = parseInt(fontScale * canvasHeight, 10);
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
		
		// randomly shuffle the available backdrops
		ns.images.sort(function() { return 0.5 - Math.random(); });
		
		// choose the first image (random) to display
		ns.swapImages();

		// toggle live-mode on by default
		ns.toggleLiveMode();
	});
	
})(jQuery, MemeGenerator);