/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Select file button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: 'button.select-file-button',
    canApply: function () {
      return 0 < this.$element.filter('button.select-file-button').length;
    },
    handler: function () {
      var button = this.$element;
      var data = core.getCommentedData(button);

      var form = $('<form enctype="multipart/form-data" style="display: none;" method="post"></form>');
      form.append($('<input type="file">').prop('name', data['name']));
      $('body').append(form);

      button.click(function () {
        form.find('input[type=file]').get(0).click();
      });

      form.find('input[type=file]').change(function () {
        if ($(this).val()) {
          assignShadeOverlay(button);

          [
            'target',
            'action',
            'object',
            'objectId',
            'fileObject',
            'fileObjectId'
          ].forEach(function (a) {
            form.find('input[name="' + a + '"]').remove();
            form.append($('<input type="hidden">').prop('name', a).val(data[a]));
          });

          form.append($('<input type="hidden">').prop('name', 'xcart_form_id').val(xliteConfig.form_id));
          form.append($('<input type="hidden">').prop('name', 'file_select').val('upload'));

          form.submit();
        }
      });
    }
  }
);