;
Backstage = {};
(function($, ns) {
	"use strict";

	ns.cropTool = false;
	
	$(document).on('click', '.image-upload-btn', function(e) {
		e.preventDefault();
		
		$('.asset-upload-popin').modal();
	});

	$(document).on('click', '.contest-start', function(e) {
		e.preventDefault();
		
		$('.contest-start-popin').modal();
	});

	$(document)
		.on('focus', '.copier', function() {
			this.select();
		})
		.on('mouseup', '.copier', function(e) {
			e.preventDefault();
		})
		.on('mouseover', '.link-exchange li', function() {

			$(this).find('.controls').show();
		})
		.on('mouseout', '.link-exchange li', function() {
			$(this).find('.controls').hide();
		});
	
	$(document).ready(function() {
		// mouseover icon-color inversion
		$('.dropdown-menu i.icon').each(function(){
			$(this).closest('li')
				.mouseover(function() {
					$(this).find('.icon').addClass('icon-white').removeClass('icon');
				})
				.mouseout(function() {
					$(this).find('.icon-white').addClass('icon').removeClass('icon-white');
				});
		});
		
		// js detection for css tweaks
		$('body').removeClass('no-js').addClass('js');

		if($('.content-tags').length){
			$('.content-tags').select2({
				tags : ns.selectTags,
				tokenSeparators : [",", " "]
			});
		}

		// configure image cropper
		var cropable = $('.cropable');
		if(cropable.length) {
			var crop_image = new Image();
			crop_image.src = cropable.attr("src");

			var options = {
				onSelect : function() {
					ns.cropTool.tools.slideDown();
				},
				onChange : function() {
					var sizing = ns.cropTool.tellSelect();
					var scale = 1;
					if(cropable.width() != crop_image.width) {
						scale = crop_image.width / cropable.width();
					}

					ns.cropTool.widthLabel.html(parseInt(sizing.w * scale, 10));
					ns.cropTool.heightLabel.html(parseInt(sizing.h * scale, 10));
				}
			};

			if(cropable.data('crop-aspect')) {
				options.aspectRatio = cropable.data('crop-aspect');
			}

			cropable.Jcrop(options, function() {
				if(!ns.cropTool) {
					ns.cropTool = this;
					ns.cropTool.imageId = cropable.data('image-id');
					ns.cropTool.widthLabel = $('.crop-width');
					ns.cropTool.heightLabel = $('.crop-height');
					ns.cropTool.tools = $('.crop-actions');
				}
			});

			$('.crop-cancel').on('click', function() {
				ns.cropTool.release();
				ns.cropTool.tools.slideUp();
			});
			$('.crop-save').on('click', function() {
				var sizing = ns.cropTool.tellSelect();
				var scale = 1;

				// determine image metric scale (when the image is not displayed as it's actual dimensions)
				if(cropable.width() != crop_image.width) {
					scale = crop_image.width / cropable.width();
				}

				// submit image to be cropped and saved as a new image
				if(sizing.w > 1 && sizing.h > 1) {
					$.ajax({
						'url' : ns.cropUrl,
						'type' : 'POST',
						'data' : {
							// scale out coordinates to match actual image size
							coords : {
								w : parseInt(sizing.w * scale, 10),
								h : parseInt(sizing.h * scale, 10),
								x1 : parseInt(sizing.x * scale, 10),
								x2 : parseInt(sizing.x2 * scale, 10),
								y1 : parseInt(sizing.y * scale, 10),
								y2 : parseInt(sizing.y2 * scale, 10)
							},
							image_id : ns.cropTool.imageId
						},
						'success' : function(response) {
							if(response.status == 'success') {
								window.location = response.redirect;
							}
						}
					});
				}
			});
		}
	});

})(jQuery, Backstage);
