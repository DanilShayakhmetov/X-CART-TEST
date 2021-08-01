/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * TinyMCE-based textarea controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var FroalaEditor = CommonElement.extend({
  constructor: function FroalaEditor(base) {
    if (base.length < 1) {
      console.error('[FroalaEditor] got empty element in constructor');
      return;
    }

    var element = base.get(0);
    element.commonController = undefined;

    FroalaEditor.superclass.constructor.apply(this, [element]);

    this.initialize();
  },

  initialize: function () {
    var froala = this.$element.froalaEditor(this.getEditorOptions());

    this.$element.on('froalaEditor.contentChanged', _.bind(this.onContentChange, this));

    this.$element.on('froalaEditor.image.error', _.bind(this.imageUploadErrorHandler, this));
    this.$element.on('froalaEditor.video.error', _.bind(this.videoUploadErrorHandler, this));

    this.bind('local.validate', _.bind(this.specialValidate, this));

    core.trigger('froala.initialized', {sender: this, element: this.element});
  },

  getEditorOptions: function () {
    var options = core.getCommentedData(this.$element.parent());

    $.map(options.appendToDefault, function (value, index) {
      if (typeof options[index] == 'undefined') {
        if (value instanceof Array && $.FroalaEditor.DEFAULTS[index] instanceof Array) {
          options[index] = value.concat($.FroalaEditor.DEFAULTS[index]);
        }
      }
    });

    options.appendToDefault = null;
    return options;
  },

  onContentChange: _.throttle(function (e, editor) {
    jQuery(e.target).trigger('change');
  }, 200),

  imageUploadErrorHandler: function (e, editor, error, response) {
    var errorMessage = '';

    try {
      var responseData = JSON.parse(response);
      errorMessage = responseData['message'] || '';
    } catch (e) {
    }

    if (error.code === 5) {
      errorMessage = core.t('File uploading error 1');
    }

    if (errorMessage !== '') {
      var $popup = editor.popups.get('image.insert');
      var $layer = $popup.find('.fr-image-progress-bar-layer');
      $layer.find('h3').text(errorMessage);
    }
  },

  videoUploadErrorHandler: function (e, editor, error, response) {
    var errorMessage = '';

    try {
      var responseData = JSON.parse(response);
      errorMessage = responseData['message'] || '';
    } catch (e) {
    }

    if (error.code === 5) {
      errorMessage = core.t('File uploading error 1');
    }

    if (errorMessage !== '') {
      var $popup = editor.popups.get('video.insert');
      var $layer = $popup.find('.fr-video-progress-bar-layer');
      $layer.find('h3').text(errorMessage);
    }
  },

  isVisible: function () {
    return this.$element.parent().is(':visible');
  },

  isVueControlled: function () {
    return typeof(this.element.form.__vue__) !== 'undefined';
  },

  handleChange: function () {
    if (this.isVueControlled()) {
      // stub unneccessary function
      return true;
    } else {
      FroalaEditor.superclass.handleChange.apply(this, arguments);
    }
  },

  specialValidate: function (event, state) {
    if (!this.isRequired()) {
      return;
    }

    if (
      this.$element.length
      && (
        this.$element.froalaEditor('core.isEmpty')
        || (
          this.$element.froalaEditor('html.get').trim() === ''
          && this.$element.froalaEditor('codeView.get').trim() === ''
        )
      )
    ) {
      var name = '';
      var label = jQuery('label[for=' + this.element.id + ']');
      if (label && label.length > 0) {
        name = label.attr('title');
      } else {
        name = id;
      }

      if (_.isUndefined(state)) {
        state = {};
      }

      if (!state.silent) {
        core.trigger(
          'message',
          {
            type: 'error',
            message: core.t('The X field is empty', {name: name})
          }
        );

        this.markAsInvalid();
        this.$element.froalaEditor('events.focus');
      }

      state.result = false;
    } else {
      this.unmarkAsInvalid();
    }
  },

  isRequired: function () {
    var rulesParsing = this.$element.attr('class');
    var getRules = /validate\[(.*)\]/.exec(rulesParsing);

    if (!getRules) {
      return false;
    }

    var str = getRules[1];
    var rules = str.split(/\[|,|\]/);
    return -1 !== rules.indexOf('required');
  }
});

core.bind(['load', 'popup.postprocess'], function() {
  core.autoload('FroalaEditor', 'textarea.fr-instance');
});
