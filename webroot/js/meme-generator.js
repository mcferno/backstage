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
	fontStrokeWidthScale : (7 / 800),
	
	// series of coordinates within the canvas
	coords : {}
};

(function($, ns) {
	"use strict";
	
	$(document)
		.on('click','.meme-generator button', function(e) {
			e.preventDefault(); // disable all buttons's defaults
		})
		.on('click','.save-image',function(e) {
			ns.render();
			ns.canvasToImage();
		})
		.on('keyup','#first-line, #last-line',function() {
			if(ns.liveMode === true) {
				ns.render();
				ns.canvasToImage();
			}
		})
		.on('click','.live-mode',function(e) {
			ns.toggleLiveMode();
			ns.render();
			if(ns.liveMode === true) {
				ns.canvasToImage();
			}
		})
		.on('change','.canvasSize',function() {
			ns.adaptToScale();
			ns.render();
			ns.canvasToImage();
		})
		.on('click','.reset',function(e) {
			window.location.reload();
		})
		.on('click','.choose-background',function(e) {
			ns.swapImages();
			ns.render();
			ns.canvasToImage();
		});
	
	/**
	 * Cycles to the next backdrop option in the series (wraps at end)
	 */
	ns.swapImages = function() {
		ns.imageOffset = (ns.imageOffset + 1) % ns.images.length;
		var obj = ns.images[ns.imageOffset];
		if(!obj.image) {
			obj = {
				href : obj
			}
			obj.image = new Image();
			obj.image.onload = function() {
				ns.matchOrientationToImage();				
				ns.render();
				ns.canvasToImage();
			};
			obj.image.src = obj.href;
		}
		ns.currentImage = obj.image;
		ns.matchOrientationToImage();
	};
	
	/**
	 * Selects the Image Size dropdown which matches the orientation of the
	 * current image.
	 */
	ns.matchOrientationToImage = function() {
		if(ns.currentImage.height != 0 && ns.currentImage.width != 0) {
			// verify we have the correct aspect-ratio
			var sizing = $('.canvasSize option[data-width="'+ns.currentImage.width+'"][data-height="'+ns.currentImage.height+'"]');
			if(sizing.length === 0) {
				if($('.canvasSize optgroup[label="custom"]').length === 0) {
					var group = $('<optgroup label="Custom" class="custom">');
					$('.canvasSize').append(group);
				}
				
				$('.canvasSize option:selected').removeAttr('selected');
				var newSize = $('<option>');
				newSize.data('height',ns.currentImage.height)
					.data('width',ns.currentImage.width)
					.attr('selected','selected')
					.text(ns.currentImage.width + ' x ' + ns.currentImage.height);
				$('.canvasSize .custom').append(newSize);
				ns.adaptToScale();
				
				return;
			}
		}		
		
		var imageOrientation = (ns.currentImage.height > ns.currentImage.width)?'vertical':'horizontal';
		var currentSize = $('.canvasSize option:selected');
		var canvasOrientation = currentSize.closest('optgroup').data('orientation');
		
		if(canvasOrientation != imageOrientation) {
			currentSize.removeAttr('selected');
			var selection = '.'+imageOrientation+' option[data-height="'+currentSize.data('width')+'"]';
			currentSize.closest('select').find(selection).attr('selected','selected');
			ns.adaptToScale();
		}
	};
	
	/**
	 * Converts the canvas to an inline downloadable JPEG. Expensive operation.
	 */
	ns.canvasToImage = function() {
		var image = $('#rendered');
		
		if(image.length == 0) {
			var image = $('<img id="rendered" />');
			$(ns.canvas).parent().append(image);
		}
		
		image.get(0).src = ns.canvas.toDataURL('image/jpeg');
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
		var lineWidth = parseInt(ns.canvas.width * 0.97); // slight width padding
		var textWidth = ns.context.measureText(text).width;

		ns.context.font = parseInt(ns.fontSize) + "pt " + ns.fontFamily;
		var lines = ns.breakTextIntoLines(text, lineWidth);
		
		if(lines.length > 1) {	
			// determine the optimal font-size	
			for(var i=1;i<3;i++) {
				ns.context.font = parseInt(ns.fontSize * (1 - 0.25*i)) + "pt " + ns.fontFamily;
				lines = ns.breakTextIntoLines(text, lineWidth);
				
				if(lines.length < 2) {
					break;
				}
			}
		}
		
		var emWidth = parseInt(ns.context.measureText('M').width*1.4);
		var offsetY = (top)?emWidth:parseInt(ns.canvas.height * 0.97 - ((lines.length - 1) * emWidth));
		
		// write out each line, respecting inner spacing
		for(var i = 0;i<lines.length;i++) {
			var line = lines[i].join(' ');
			ns.context.strokeText(line, ns.coords.center.x,offsetY + i*emWidth);
			ns.context.fillText(line, ns.coords.center.x, offsetY + i*emWidth);
		}
		
		ns.context.restore(); 
	};
	
	/**
	 * Breaks a string into an array of substrings, all of which fit within
	 * the provided width maximum. The font size of the current canvas is used 
	 * in text width calculations.
	 *
	 * @param {String} text: Text to break into smaller substrings
	 * @param {Integer} lineWidth: Maximum width in pixels any line can span
	 * @return {Array} Substrings of the text, respecting the width
	 */
	ns.breakTextIntoLines = function(text, lineWidth) {
		var lines = new Array();
		var words = text.replace(/^\s+|\s+$/,'').replace(/\s\s*/g,' ').split(' '); //'
		var row = 0;
		
		while(words.length != 0) {
			var word = words.shift();
			
			if(typeof lines[row] == 'undefined') {
				lines[row] = new Array();
				lines[row].push(word);
				continue;
			}
			
			// text is too long with this word, push to next line
			if(ns.context.measureText(lines[row].join(' ')+ ' ' + word).width > lineWidth) {
				words.unshift(word);
				row++;
				
			// text + this word fits, add it to this line
			} else {
				lines[row].push(word);
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
		ns.images = memeBaseImages;
		ns.firstLineText = $('#first-line');
		ns.lastLineText = $('#last-line');
		
		ns.adaptToScale();
		
		if(ns.images.length < 2) {
			$('.choose-background').hide();
		}
	};
	
	/**
	 * Calculates all sizing & placements which are relative to the canvas
	 * dimensions. Must be ran after every change in canvas size.
	 */
	ns.adaptToScale = function() {
		var sizing = $('.canvasSize option:selected');
		
		// verify that the canvas size matches the selected option
		if(sizing.length != 0 && ns.canvas.height != sizing.data('height') || ns.canvas.width != sizing.data('width')) {
			ns.canvas.height = sizing.data('height');
			ns.canvas.width = sizing.data('width');
		}
		
		ns.context.textAlign = "center";
		ns.context.fillStyle = "#FFF";
		ns.context.lineStyle = "#000";
		
		// scale font relative to canvas, avoiding sub-pixel
		var fontScale = navigator.userAgent.match(/(iPhone|iPod)/i)?ns.fontToHeightScaleiOS:ns.fontToHeightScale;
		var canvasHeight = (ns.canvas.width > ns.canvas.height)?ns.canvas.height:ns.canvas.width;
		var canvasWidth = (ns.canvas.width > ns.canvas.height)?ns.canvas.width:ns.canvas.height;
		ns.fontSize = parseInt(fontScale * canvasHeight);
		ns.context.font = ns.fontSize + "pt " + ns.fontFamily;
		ns.context.lineWidth = parseInt(ns.fontStrokeWidthScale * canvasWidth);
		
		// calculate the canvas centerpoint
		ns.coords.center = {
			x : ns.canvas.width/2,
			y : ns.canvas.height/2
		};
	};
	
	function isCanvasSupported(){
		var elem = document.createElement('canvas');
		return !!(elem.getContext && elem.getContext('2d'));
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
			var larger = $('.canvasSize [data-width=800]');
			larger.attr('selected','selected');
		}
		
		ns.init();
		
		// randomly shuffle the available backdrops
		ns.images.sort(function() { return 0.5 - Math.random(); });
		
		// choose the first image (random) to display
		ns.swapImages();
		
		// first paint of the interface
		ns.render();
		ns.canvasToImage();
	});
	
})(jQuery, MemeGenerator);