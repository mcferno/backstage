/**
 * Image cropping module
 */
Backstage['CropTool'] = (function($, env, config) {

	var cropable, imgObj,
		options = {},
		cropTool = false,
		cropUrl = config['cropUrl'] || '/crop/save';

	// attach behavior to the DOM elements
	var init = function(optionOverrides) {

		// configurable DOM selectors
		$.extend(options, optionOverrides, {
			images: '.cropable',
			saveBtn: '.crop-save',
			cancelBtn: '.crop-cancel',
			widthLabel: '.crop-width',
			heightLabel: '.crop-height',
			uiGroup: '.crop-actions'
		});

		// configure image cropper
		cropable = $(options.images);
		if(cropable.length) {
			$(options.saveBtn).on('click', save);
			$(options.cancelBtn).on('click', cancel);
			initJcrop();
		}
	};

	// configures the Jcrop plugin
	var initJcrop = function() {
		imgObj = new Image();
		imgObj.src = cropable.attr("src");

		var jcropOptions = {
			onSelect : function() {
				cropTool.tools.slideDown();
			},
			onChange : function() {
				var sizing = cropTool.tellSelect();
				var scale = 1;
				if(cropable.width() != imgObj.width) {
					scale = imgObj.width / cropable.width();
				}

				cropTool.widthLabel.html(parseInt(sizing.w * scale, 10));
				cropTool.heightLabel.html(parseInt(sizing.h * scale, 10));
			}
		};

		if(cropable.data('crop-aspect')) {
			options.aspectRatio = cropable.data('crop-aspect');
		}

		cropable.Jcrop(jcropOptions, function() {
			if(!cropTool) {
				cropTool = this;
				cropTool.imageId = cropable.data('image-id');
				cropTool.widthLabel = $(options.widthLabel);
				cropTool.heightLabel = $(options.heightLabel);
				cropTool.tools = $(options.uiGroup);
			}
		});
	};

	// save the image crop in its current state
	var save = function() {
		var sizing = cropTool.tellSelect();
		var scale = 1;

		// determine image metric scale (when the image is not displayed as it's actual dimensions)
		if(cropable.width() != imgObj.width) {
			scale = imgObj.width / cropable.width();
		}

		// submit image to be cropped and saved as a new image
		if(sizing.w > 1 && sizing.h > 1) {
			$.ajax({
				'url' : cropUrl,
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
					image_id : cropTool.imageId
				},
				'success' : function(response) {
					if(response.status == 'success') {
						window.location = response.redirect;
					}
				}
			});
		}
	};

	// stop the cropping process, hide the UI
	var cancel = function() {
		cropTool.release();
		cropTool.tools.slideUp();
	};

	return {
		init: init,
		save: save,
		cancel: cancel
	};

})(jQuery, AppEnv, AppEnv['Config']['CropTool'] || {});
