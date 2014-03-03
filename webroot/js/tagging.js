/**
 * Content tagging module
 * @author Patrick McFern <mcferno AT gmail.com>
 */
Backstage['Tagging'] = (function($, env, config) {

	var options = {},
		tagSelection,
		taggableContainer,
		tagSave;

	// attach behavior to the DOM elements
	var init = function(optionOverrides) {

		// configurable DOM selectors
		$.extend(options, optionOverrides, {
			tagId: '#TaggingForeignId',
			tagList: '#TaggingTags',
			tagType: '#TaggingModel',

			quickTagInput: '.quick-tagger',
			quickTagTargets: '[data-role="taggable"]',
			quickTagSaveBtn: '.save-quick-tags',

			formTags: '.content-tags'
		});

		initForm();

		if($(options.quickTagInput).length !== 0) {
			initQuickTag();
		}
	};

	// configure tagging within a form submission
	var initForm = function() {
		var inputField = $(options.formTags);
		if(inputField.length === 0) {
			return false;
		}

		var placeholder_str = '';
		if(config.selectTags.length === 0) {
			placeholder_str = '';
		} else {
			// randomly select
			var sampleTags = config.selectTags
				.slice()
				.filter(function(n){ return n })
				.sort(function() { return 0.5 - Math.random();})
				.slice(0,3);

			placeholder_str = 'Example: ' + sampleTags.join(', ');
		}

		inputField.select2({
			tags : config.selectTags,
			tokenSeparators : [",", "  "],
			width : '100%',
			placeholder: placeholder_str
		});

		if(config['taggingMode'] == 'live') {
			inputField.on('change', save);
		}
	};

	// save the tags concerning a single target object
	var save = function() {
		var payload =  {
			'Tag' : {
				'id' : $(options.tagId).val(),
				'tags' : $(options.tagList).val(),
				'model' : $(options.tagType).val()
			}
		};

		$.ajax({
			url : env.backendURL + 'tags/update',
			data : payload,
			type : 'POST'
		});
	};

	// initalizes a quick tagging mode to tag multiple objects at once
	var initQuickTag = function() {

		var req = $.ajax({
			url : env.backendURL + 'tags/list',
			type : 'GET'
		});

		tagSelection = $(options.quickTagInput);
		taggableContainer = $(options.quickTagTargets);
		tagSave = $(options.quickTagSaveBtn);

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
			tagSave.click(saveQuickTag);
		});

	};

	// saves multiple tags assigned to multiple target objects
	var saveQuickTag = function() {
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
	};

	return {
		init: init,
		save: save,
		initQuickTag: initQuickTag,
		saveQuickTag: saveQuickTag
	};

})(jQuery, AppEnv, AppEnv['Config']['Tagging'] || {});
