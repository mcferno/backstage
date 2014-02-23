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

		if(ns.CropTool) {
			ns.CropTool.init();
		}

		if($('.content-tags').length){

			var placeholder_str = '';
			if(ns.selectTags.length === 0) {
				placeholder_str = '';
			} else {
				// randomly select
				var sampleTags = ns.selectTags
					.slice()
					.sort(function() { return 0.5 - Math.random();})
					.slice(0,3);

				placeholder_str = 'Example: ' + sampleTags.join(', ');
			}

			$('.content-tags').select2({
				tags : ns.selectTags,
				tokenSeparators : [",", "  "],
				width : '100%',
				placeholder: placeholder_str
			});

			if(ns.taggingMode == 'live') {
				$('.content-tags').on('change', ns.saveTags);
			}
		}

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

					// halt if the dropped content is not an image
					if(typeof event.originalEvent.dataTransfer.files[0] == 'undefined' ||
						event.originalEvent.dataTransfer.files[0].type.indexOf('image') === -1
					) {
						return false;
					}

					var data = new FormData();
					var submitForm = $('.asset-upload-popin form');
					var formData = submitForm.serializeArray();
					$.each(formData, function() {
						data.append(this.name, this.value);
					});
					data.append(submitForm.find('input[type=file]').attr('name'), event.originalEvent.dataTransfer.files[0]);

					var uploadStatus = $('#dropzone-upload');
					var info = uploadStatus.find('.info');
					info.text('Uploading ...');
					uploadStatus.modal('show');

					var progressBar = uploadStatus.find('.bar');
					progressBar.css('width', '0');

					$.ajax({
						url : submitForm.attr('action'),
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST',
						error: function(request, status, error) {
							info.text('Error during file upload! Please try again');
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
								info.text('Upload Complete! Redirecting ...');
								window.location = response.redirect;
							} else {
								if(response.message) {
									info.text(response.message);
								} else {
									info.text('Error during upload! Please try again');
								}
							}
						}
					});
				}
			});

			$('.asset-upload-popin form').submit(ns.ajaxFileUpload);

			if($('.quick-tagger').length !== 0) {
				ns.configureQuickTagging();
			}
		}
	});

	ns.supportsFileApi = function() {
		return $("<input type='file'/>").get(0).files !== undefined && window.FormData !== undefined;
	};

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

	// Live update the database of tags
	ns.saveTags = function() {
		var payload =  {
			'Tag' : {
				'id' : $('#TaggingForeignId').val(),
				'tags' : $('#TaggingTags').val(),
				'model' : $('#TaggingModel').val()
			}
		};

		$.ajax({
			url : env.backendURL + 'tags/update',
			data : payload,
			type : 'POST'
		});
	};

	ns.configureQuickTagging = function() {

		var req = $.ajax({
			url : env.backendURL + 'tags/list',
			type : 'GET'
		});

		var tagSelection = $('.quick-tagger');
		var taggableContainer = $('[data-role="taggable"]');
		var tagSave = $('.save-quick-tags');

		req.done(function(tags) {
			tagSelection.select2({
				tags : tags
			});

			// toggle selected objects as 'tagged'
			taggableContainer.on('click', '[data-id]', function(e) {
				e.preventDefault();
				var obj = $(this);

				if(obj.hasClass('tagged')) {
					obj.removeClass('tagged');
				} else {
					obj.addClass('tagged');
				}
			});

			// persist tags
			tagSave.click(function() {
				var tags = tagSelection.select2('val');
				var tagged = [];
				taggableContainer.find('.tagged').each(function() {
					tagged.push($(this).data('id'));
				});

				if(tagged.length < 1) {
					return;
				}

				var payload = {
					data : {
						tags : tags,
						model : taggableContainer.data('model'),
						tagged : tagged
					}
				};

				$.ajax({
					url : env.backendURL + 'tags/add_tags',
					type : 'POST',
					cache : false,
					data : payload,
					success : function() {
						tagSelection.select2('val', '');
						taggableContainer.find('.tagged').removeClass('tagged');
						tagSave.tooltip({ title : 'Tagging Saved!'}).tooltip('show');
						setTimeout(function() { tagSave.tooltip('destroy'); }, 2000);
					},
					error : function() {
						tagSave.tooltip({ title : 'Tags could not be saved.'}).tooltip('show');
						setTimeout(function() { tagSave.tooltip('destroy'); }, 2000);
					}
				});
			});
		});

	};

})(jQuery, Backstage, AppEnv, document, window);
