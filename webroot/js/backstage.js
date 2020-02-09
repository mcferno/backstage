/**
 * Backstage User Realm
 * @author Patrick McFern <mcferno AT gmail.com>
 */
(function($, ns, env, document, window) {
	"use strict";

	var doc = $(document),
		win = $(window);

	doc
		.on('click', '.image-upload-btn', function(e) {
			e.preventDefault();

			$('.asset-upload-popin').modal();

			if(env.User.isMobile) {
				$('#AssetImage').focus();
			}
		})
		.on('click', '.album-module-btn', function(e) {
			e.preventDefault();

			$('.album-module').modal();
		})
		.on('click', '.contest-start', function(e) {
			e.preventDefault();

			$('.contest-start-popin').modal();
		})
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

	doc.ready(function() {
		// js detection for css tweaks
		$('body').removeClass('no-js').addClass('js');

		// initialize the image cropping tool if loaded
		if(ns.CropTool) {
			ns.CropTool.init();
		}

		// initialze the content tagging tool if loaded
		if(ns.Tagging) {
			ns.Tagging.init();
		}

		// allow next/prev pagination links to be triggered by keyboard shortcuts
		key('right', function() {
			var nextLink = $('a[rel="next"]:first');
			if(nextLink.length && nextLink.attr('href').length) {
				window.location.href = nextLink.attr('href');
			}
		});
		key('left', function() {
			var prevLink = $('a[rel="prev"]:first');
			if(prevLink.length && prevLink.attr('href').length) {
				window.location.href = prevLink.attr('href');
			}
		});

		if(ns.supportsFileApi()) {
			doc.on({
				dragover : function() {
					$('#dropzone').fadeIn(200);
					return false;
				},
				drop : function(event) {
					$('#dropzone').fadeOut(200);
					if(!$(event.target).is('input[type=file]')) {
						event.preventDefault();
					}
				}
			});

			$('#dropzone').on({
				dragover: function () {
					return false;
				},
				dragleave: function () {
					$(this).fadeOut(200);
					return false;
				},
				dragend: function () {
					$(this).fadeOut(200);
					return false;
				},
				drop: function(event) {
					event.preventDefault();
					$('#dropzone').fadeOut(200);

					for (var i = 0; i < event.originalEvent.dataTransfer.files.length; i++) {
						ns.dragUpload.addImage(event.originalEvent.dataTransfer.files[i]);
					}

					ns.dragUpload.run();
				}
			});

			$('.asset-upload-popin form').submit(ns.ajaxFileUpload);
		}
	});

	ns.supportsFileApi = function() {
		return $("<input type='file'/>").get(0).files !== undefined && window.FormData !== undefined;
	};

	/**
	 * Module controlling file upload UI components
	 */
	ns.dragUpload = (function () {
		var uploadStatus,
			info,
			progressBar,
			submitForm,
			images = [];

		var showProgressBar = function() {
			uploadStatus = $('#dropzone-upload');
			info = uploadStatus.find('.info');
			info.text('Uploading ...');
			uploadStatus.modal('show');

			progressBar = uploadStatus.find('.bar');
			progressBar.css('width', '0');
		};

		var setProgressBarState = function(percentage) {
			var progressBar = $('#dropzone-upload .bar');
			progressBar.css('width', percentage + '%');
		};

		var showNotice = function(newwText) {
			info.text(newwText);
		};

		var addImage = function(newImage) {
			// halt if the dropped content is not an image
			if(typeof newImage == 'undefined' ||
				newImage.type.indexOf('image') === -1
			) {
				return false;
			}
			images.push(newImage);
		};

		/**
		 * @param file File
		 */
		var prepareImageUploadFormData = function (file) {
			var data = new FormData();
			var formData = submitForm.serializeArray();
			$.each(formData, function() {
				data.append(this.name, this.value);
			});
			data.append(submitForm.find('input[type=file]').attr('name'), file);
			return data;
		};

		var runBatch = function() {
			if (images.length == 0) {
				return;
			}

			ns.dragUpload.showProgressBar();
			submitForm = $('.asset-upload-popin form');
			var total = images.length;

			while (images.length > 0) {
				var data = prepareImageUploadFormData(images.shift());
				data.append('batchSize', total);

				$.ajax({
					url : submitForm.attr('action'),
					data: data,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					beforeSend: function() {
						var text = 'Processing image';
						if (total > 1) {
							text += ' (' + (total - images.length) + ' of ' + total + ')';
						}
						ns.dragUpload.showNotice(text);
					},
					error: function(request, status, error) {
						if (error.length) {
							ns.dragUpload.showNotice('Error during file upload! Please try again');
						}
					},
					xhr: function() {
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener("progress", function(e) {
							if (e.lengthComputable) {
								ns.dragUpload.setProgressBarState(Math.round(e.loaded / e.total * 100));
							}
						}, false);
						return xhr;
					},
					success: function(response) {
						if(response.error === false && response.redirect) {
							var text = 'Upload Complete!';
							if (images.length == 0) {
								text += ' Redirecting ..';
							}
							ns.dragUpload.showNotice(text);
							window.location = response.redirect;
						} else {
							if(response.message) {
								ns.dragUpload.showNotice(response.message);
							}
						}
					}
				});
			}
		};

		return {
			'showProgressBar' : showProgressBar,
			'setProgressBarState' : setProgressBarState,
			'showNotice' : showNotice,
			'addImage' : addImage,
			'run' : runBatch
		};

	})();

	ns.ajaxFileUpload = function(e) {
		var obj = $(this);
		var fileInput = obj.find('input[type=file]');
		if(fileInput.get(0).files.length < 1) {
			return true;
		}
		e.preventDefault();

		var data = new FormData();
		var formData = obj.serializeArray();
		$.each(formData, function() {
			data.append(this.name, this.value);
		});
		data.append(fileInput.attr('name'), fileInput.get(0).files[0]);

		var progressBar = obj.find('.bar');
		progressBar.css('width', '0');

		var submitButton = obj.find('.btn-upload');
		submitButton.button('loading');

		obj.find('.progress').show();

		$.ajax({
			url : obj.attr('action'),
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			error: function(request, status, error) {
				alert('Error during file upload! Please try again');
			},
			xhr: function(){
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(e) {
					if (e.lengthComputable) {
						var percent = Math.round(e.loaded / e.total * 100);
						progressBar.css('width', percent + '%');
					}
				}, false);
				return xhr;
			},
			success: function(response) {
				if(response.error === false && response.redirect) {
					window.location = response.redirect;
				} else {
					if(response.message) {
						alert(response.message);
					} else {
						alert('Error during upload! Please try again');
					}
				}
			},
			complete: function() {
				obj.find('.progress').hide();
				submitButton.button('reset');
			}
		});
	};

})(jQuery, Backstage, AppEnv, document, window);
