;
Backstage = {};
(function($, ns) {
	"use strict";

	ns.cropTool = false;
	
	$('.image-upload-btn').live('click',function(e) {
		e.preventDefault();
		
		$('.asset-upload-popin').modal();
	});

	$('.contest-start').live('click',function(e) {
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

		var cropable = $('.cropable');
		var crop_image = new Image();
		crop_image.src = cropable.attr("src");

		// configure image cropper
		if(cropable.length) {
			cropable.Jcrop({
				onSelect : function() {
					ns.cropTool.tools.slideDown();
				},
				onChange : function() {
					var sizing = ns.cropTool.tellSelect();
					var scale = 1;
					if(cropable.width() != crop_image.width) {
						scale = crop_image.width / cropable.width();
					}

					ns.cropTool.widthLabel.html(parseInt(sizing.w * scale));
					ns.cropTool.heightLabel.html(parseInt(sizing.h * scale));
				}
			}, function() {
				if(!ns.cropTool) {
					ns.cropTool = this;
					ns.cropTool.assetId = cropable.data('asset-id');
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
				if(cropable.width() != crop_image.width) {
					scale = crop_image.width / cropable.width();
				}

				// submit image to be cropped and saved as a new image
				if(sizing.w > 1 && sizing.h > 1) {
					$.ajax({
						'url' : Backstage.cropUrl,
						'type' : 'POST',
						'data' : {
							coords : {
								w : parseInt(sizing.w * scale),
								h : parseInt(sizing.h * scale),
								x1 : parseInt(sizing.x * scale),
								x2 : parseInt(sizing.x2 * scale),
								y1 : parseInt(sizing.y * scale),
								y2 : parseInt(sizing.y2 * scale)
							},
							asset_id : ns.cropTool.assetId
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
