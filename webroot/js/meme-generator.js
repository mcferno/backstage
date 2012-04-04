;
var generator = {
	lastRender : 0
};
(function() {
	"use strict";
	
	$(document)
		.on('click','.save-image',function(e) {
			e.preventDefault();
			
			var dataURL = generator.canvas.toDataURL();
			
			var image = $('#rendered');
			
			if(image.length == 0) {
				var image = $('<img id="rendered" />');
				$(generator.canvas).parent().append(image);
			}
			
			image.get(0).src = dataURL;	
			$(generator.canvas).hide();		
			image.show();
			
			generator.saveButtons.hide();
			generator.resumeButtons.show();
			$('.save-help').show();
			
		})
		.on('click','.trigger-edit',function(e) {
			e.preventDefault();
			
			$(generator.canvas).show();		
			$('#rendered').hide();
			generator.saveButtons.show();
			generator.resumeButtons.hide();
			$('.save-help').hide();
		})
		.on('keyup','#first-line, #last-line',function() {
			generator.render();
		})
	
	generator.render = function() {
		var canvas = generator.canvas;
		var context = generator.context;
		var backdrop = context.createLinearGradient(0, 0, canvas.width, canvas.height);
		
		backdrop.addColorStop(0, "black");
		backdrop.addColorStop(1, "#FC6626");
		context.fillStyle = backdrop;
		context.fillRect(0, 0,canvas.width, canvas.height);
		
		context.textAlign = "center";
		context.fillStyle="#FFF";
		context.lineStyle="#000";
		context.font="bold 32pt Impact";
		context.lineWidth = 5;
		
		var firstLine = {
			x : canvas.width/2,
			y : canvas.height/2
		}
		
		var firstLineText = $('#first-line').attr('value').toUpperCase();
		var lastLineText = $('#last-line').attr('value').toUpperCase();
		
		context.strokeText(firstLineText, firstLine.x, firstLine.y - canvas.height/2 * 0.7);
		context.fillText(firstLineText, firstLine.x, firstLine.y - canvas.height/2 * 0.7);
		
		context.strokeText(lastLineText, firstLine.x, firstLine.y + canvas.height/2 * 0.8);
		context.fillText(lastLineText, firstLine.x, firstLine.y + canvas.height/2 * 0.8);
		
		var canvasCenter = { 
			x : canvas.width/2,
			y : canvas.height/2
		};
		
		generator.lastRender = new Date().getTime();
	}
	
	generator.init = function() {
		generator.canvas = $('#workspace').get(0); // dom object
		generator.context = generator.canvas.getContext('2d');
		generator.saveButtons = $('.save-image');
		generator.resumeButtons = $('.trigger-edit');
	}

	$(document).ready(function() {
		generator.init();
		generator.render();
	});
	
})(jQuery,window,document);