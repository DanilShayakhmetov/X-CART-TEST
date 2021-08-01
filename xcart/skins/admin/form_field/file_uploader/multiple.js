/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * file uploader controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('multiple_file_uploader', [
  'vue/vue',
  'js/vue/vue',
  'file_uploader'
], function (Vue, XLiteVue) {
  XLiteVue.component('xlite-multiple-file-uploader', {
    ready: function () {
      this.processSortable();
      this.repositionFiles(true);
    },

    events: {
      'new-file-uploaded': function (index, data, fileUploader) {
        var element = jQuery(data).find('xlite-file-uploader');

        if (element.length) {
          var anchorElement = jQuery(fileUploader.getFileUploaderElement());
          anchorElement = anchorElement.siblings().toArray().reduce(function (carry, item) {
            var current = jQuery(item);
            var next = current.next('div.file-uploader.item');

            if (!next.length) {
              return carry;
            }

            var currentIndex = parseInt(current.attr('data-position-index'));
            var nextIndex = parseInt(next.attr('data-position-index'));

            if (isNaN(currentIndex) && isNaN(nextIndex)) {
              return carry;
            }

            if (isNaN(currentIndex) && nextIndex > index) {
              return next;
            }

            if (currentIndex < index && nextIndex > index) {
              return next;
            }

            return carry;
          }, anchorElement);

          element.insertBefore(anchorElement);
          var v = new Vue();
          v.el = element.get(0);
          element.attr('data-position-index', index);
          v.$compile(element.get(0), this);
          this.repositionFiles();
        }
        fileUploader.$reload();
      },
      'before-new-files-uploaded': function (fileUploader) {
        jQuery(fileUploader.getFileUploaderElement()).siblings().removeAttr('data-position-index');
      }
    },

    methods: {
      getMultipleFileUploaderElement: function () {
        return $(this.$el).closest('.multiple-files');
      },
      processSortable: function () {
        var self = this;
        this.getMultipleFileUploaderElement().sortable({
          placeholder:          'ui-state-highlight',
          forcePlaceholderSize: false,
          distance:             10,
          items:                '> div.item',
          update:               function(event, ui)
          {
            self.repositionFiles();
          },
          activate: function(event, ui) {
            if (ui.item.hasClass('open')) {
              ui.item.find('.link').dropdown('toggle');
            }
          }
        });
      },
      repositionFiles: function (saveAsInitial) {
        base = jQuery(this.getMultipleFileUploaderElement());

        var min = 10;
        base.find('input.input-position').each(function () {
          min = parseInt(10 == min ? min : Math.min(this.value, min));
        });

        base.find('input.input-position').each(function () {
          jQuery(this).val(min);
          if (saveAsInitial) {
            if (this.commonController) {
              this.commonController.saveValue();
            }

          } else {
            jQuery(this).change();
          }
          min += 10;
        });
      }
    }
  });
});
