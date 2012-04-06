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
	
	// font size coefficient to scale accordingly to height
	fontToHeightScale    : (32 / 450), // 0.0711+, from 32pt @ 450px
	fontToHeightScaleiOS : (29 / 450), // to adjust for a different font on iOS
	
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
			obj.image = new Image();
			obj.image.onload = function() {
				ns.render();
				ns.canvasToImage();
			};
			obj.image.src = $(obj).attr('src');
		}
		ns.currentImage = obj.image;
	}
	
	/**
	 * Converts the canvas to an inline downloadable PNG. Expensive operation.
	 */
	ns.canvasToImage = function() {
		var image = $('#rendered');
		
		if(image.length == 0) {
			var image = $('<img id="rendered" />');
			$(ns.canvas).parent().append(image);
		}
		
		image.get(0).src = ns.canvas.toDataURL('image/jpeg');
	}
	
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
	}
	
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
		
		context.strokeText(firstLineText, ns.coords.firstLine.x, ns.coords.firstLine.y);
		context.fillText(firstLineText, ns.coords.firstLine.x, ns.coords.firstLine.y);
		
		context.strokeText(lastLineText, ns.coords.lastLine.x, ns.coords.lastLine.y);
		context.fillText(lastLineText, ns.coords.lastLine.x, ns.coords.lastLine.y);
		
		ns.lastRender = new Date().getTime();
	}
	
	/**
	 * Initialize the app by attaching DOM elements
	 */
	ns.init = function() {
		ns.canvas = $('#workspace').get(0); // dom object
		ns.context = ns.canvas.getContext('2d');
		ns.images = $('#backgrounds img');
		ns.firstLineText = $('#first-line');
		ns.lastLineText = $('#last-line');
		
		ns.adaptToScale();
	}
	
	/**
	 * Calculates all sizing & placements which are relative to the canvas dimensions
	 */
	ns.adaptToScale = function() {
		var sizing = $('.canvasSize option:selected');
		
		// verify that the canvas size matches the selected option
		if(ns.canvas.height != sizing.data('height') || ns.canvas.width != sizing.data('width')) {
			ns.canvas.height = sizing.data('height');
			ns.canvas.width = sizing.data('width');
		}
		
		ns.context.textAlign = "center";
		ns.context.fillStyle = "#FFF";
		ns.context.lineStyle = "#000";
		
		// scale font relative to canvas, avoiding sub-pixel
		var fontScale = navigator.userAgent.match(/(iPhone|iPod)/i)?ns.fontToHeightScaleiOS:ns.fontToHeightScale;
		ns.context.font = parseInt(fontScale * ns.canvas.height) + "pt Impact, Futura-CondensedExtraBold, sans-serif";
		ns.context.lineWidth = parseInt(ns.fontStrokeWidthScale * ns.canvas.width);
		
		// calculate the relative placement of text
		ns.coords.center = {
			x : ns.canvas.width/2,
			y : ns.canvas.height/2
		};
		ns.coords.firstLine = {
			x : ns.coords.center.x,
			y : parseInt(ns.coords.center.y * 0.3)
		};
		ns.coords.lastLine = {
			x : ns.coords.center.x,
			y : parseInt(ns.coords.center.y * 1.85)
		};
	}
	
	function isCanvasSupported(){
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