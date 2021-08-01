/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Reloadable layout block widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function InlineEditableController()
{
  this.init();

  core.bind('loader.loaded', _.bind(this.reset, this));
  core.bind('inline_editor.disable', _.bind(this.destroy, this));
  core.bind('inline_editor.enable', _.bind(this.reset, this));
}

InlineEditableController.prototype.selector = '[data-inline-editable]';

InlineEditableController.prototype.buildChangeRecord = function (element) {
  element = $(element);
  return {
    model: element.data('model'),
    identifier: element.data('identifier'),
    property: element.data('property'),
    value: element.froalaEditor('html.get')
  };
};

InlineEditableController.prototype.init = function () {
  $(this.selector).froalaEditor(this.getEditorOptions());
  $(this.selector).on('froalaEditor.contentChanged', _.bind(this.onContentChanged, this));
  $(this.selector).on('froalaEditor.image.inserted froalaEditor.image.replaced', _.bind(this.onImageInserted, this));
  $(this.selector).on('froalaEditor.video.inserted froalaEditor.video.replaced', _.bind(this.onVideoInserted, this));

  $(this.selector).on('froalaEditor.image.error', _.bind(this.imageUploadErrorHandler, this));
  $(this.selector).on('froalaEditor.video.error', _.bind(this.videoUploadErrorHandler, this));
};

InlineEditableController.prototype.getEditorOptions = function () {
  return $(this.selector).data('inline-editor-config');
};

InlineEditableController.prototype.destroy = function () {
  if ($(this.selector).data('froala.editor')) {
    $(this.selector).froalaEditor('destroy');
  }
};

InlineEditableController.prototype.reset = function () {
  this.destroy();
  this.init();
};

InlineEditableController.prototype.onContentChanged = function (event, editor) {
  core.trigger('inline_editor.changed', {
    event: event,
    sender: this,
    change: this.buildChangeRecord(event.currentTarget),
    fieldId: $(event.currentTarget).data('property')
  });
};

InlineEditableController.prototype.onImageInserted = function (event, editor, element, response) {
  core.trigger('inline_editor.image.inserted', {
    event: event,
    sender: this,
    imageId: JSON.parse(response).id,
    imageElement: element
  });
};

InlineEditableController.prototype.onVideoInserted = function (event, editor, element, response) {
  core.trigger('inline_editor.video.inserted', {
    event: event,
    sender: this,
    videoId: JSON.parse(response).id,
    videoElement: element
  });
};

InlineEditableController.prototype.imageUploadErrorHandler = function (e, editor, error, response) {
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
};

InlineEditableController.prototype.videoUploadErrorHandler = function (e, editor, error, response) {
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
};

InlineEditableController.prototype.getFullChangeset = function() {
  var changeset = {};
  if ($(this.selector).data('froala.editor')) {
    $(this.selector).each(function() {
      changeset[$(this).data('property')] = $(this).froalaEditor('html.get');
    })
  }

  return changeset;
}

core.autoload(InlineEditableController);

