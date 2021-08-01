/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * file uploader controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('file_uploader', [
  'vue/vue',
  'js/vue/vue',
  'vue/vue.loadable'
], function (Vue, XLiteVue, VueLoadableMixin) {
  var XLiteFileUploader = {
    mixins: [VueLoadableMixin],

    props: {
      'multiple': false,
      'temp_id': false,
      'position': false,
      'alt': false,
      'delete': false,
      'basePath': '',
      'helpMessage': ''
    },

    watch: {
      'temp_id': function (val) {
        this.$dispatch('form-model-prop-updated', this.basePath + '.temp_id', val, this);
      },
      'position': function (val) {
        this.$dispatch('form-model-prop-updated', this.basePath + '.position', val, this);
      },
      'alt': function (val) {
        this.$dispatch('form-model-prop-updated', this.basePath + '.alt', val, this);
      },
      'delete': function (val) {
        this.$dispatch('form-model-prop-updated', this.basePath + '.delete', val, this);
      }
    },

    data: function () {
      return {
        commonData: {},
        isRemovable: false,
        isTemporary: false,
        isImage: false,
        hasFile: false,
        viaUrlPopup: {},
        reloadFromContent: false,
        reloadContent: null,
        error: null,
        defaultErrorMessage: null,
        realErrorMessage: null,
        showsMessages: true,
        initialAlt: '',
        elementWidth: 0
      };
    },

    computed: {
      isDisplayCamera: function () {
        return !this.errorMessage && !this.hasFile && this.isImage;
      },
      isDisplayPreview: function () {
        return !this.errorMessage && this.hasFile;
      },
      shouldShowMessage: function () {
        return this.showsMessages
          && !!this.message
          && (
            !this.temp_id
            || this.error
          ) && this.elementWidth > 100;
      },
      message: function () {
        return this.error ? this.defaultErrorMessage : this.helpMessage;
      },
      errorMessage: function () {
        return this.error ? this.defaultErrorMessage : '';
      }
    },

    loadable: {
      transferState: false,
      cacheSimultaneous: false,
      loader: function () {
        var self = this;

        if (this.reloadFromContent) {
          this.reloadFromContent = false;

          var html = this.reloadContent || self.getFileUploaderElement()[0].outerHTML;

          return $.Deferred(function (obj) {
            obj.resolve(html);
          }).promise();
        }

        var data = {
          object_id: self.getFileUploaderElement().data('objectId'),
          markAsImage: true
        };
        this.commonData.action = 'refresh';


        this.viaUrlPopup = null;
        return core.post(
          URLHandler.buildURL(this.commonData),
          undefined,
          data,
          {timeout: 45000}
        );
      },
      resolve: function () {
        var form = $(this.getFileUploaderElement()).parents('form').get(0);

        if (!_.isUndefined(CommonForm) && form) {
          CommonForm.autoassign(this.getFileUploaderElement());

          jQuery(form).addClass('changed');
          jQuery(form).trigger('state-changed');

          var temp_id = $(this.getFileUploaderElement()).find('.input-temp-id');
          if (temp_id.length && !_.isUndefined(temp_id.get(0).commonController)) {
            temp_id.get(0).commonController.element.initialValue = null;
          }
        }
      },
      reject: function () {
      }
    },

    ready: function () {
      this.commonData = jQuery(this.getFileUploaderElement()).parent().data();
      this.commonData.target = 'files';

      if (this.isImage) {
        this.commonData.is_image = '1';
      }

      if (xliteConfig.zone === "customer") {
        this.commonData.base = xliteConfig.admin_script;
        this.commonData.interface = 'customer';
      }

      if (this.realErrorMessage) {
        core.trigger('message', {
          type: 'warning',
          message: this.realErrorMessage
        })
      }

      this.prepareWidget();
    },

    methods: {
      getFileUploaderElement: function () {
        return $(this.$el).closest('.file-uploader');
      },
      assignWait: function () {
        this.getFileUploaderElement()
          .addClass('loading')
          .append('<div class="spinner"></div>');
      },
      unassignWait: function () {
        this.getFileUploaderElement()
          .removeClass('loading')
          .find('> div.spinner')
          .remove();
      },
      prepareWidget: function () {
        CommonForm.autoassign(this.getFileUploaderElement());
        var base = this.getFileUploaderElement();

        base.find('div.via-url-popup .copy-to-file').change(function () {
          base.find('div.via-url-popup .not-copy-to-file-warning').toggleClass('hidden', jQuery(this).is(':checked'));
        });

        this.viaUrlPopup = base.find('.via-url-popup').dialog({
          dialogClass: "via-url-popup-dialog",
          classes: {
            "ui-dialog": "via-url-popup-dialog"
          },
          autoOpen: false,
          draggable: false,
          title: base.find('.via-url-popup').data('title'),
          width: 500,
          modal: true,
          resizable: false,
          open: _.bind(
            function (event, ui) {
              jQuery('.ui-widget-overlay').addClass('via-url-popup-overlay')
              jQuery('.overlay-blur-base').addClass('overlay-blur');
            },
            this
          ),
          close: _.bind(
            function (event, ui) {
              jQuery('.ui-widget-overlay').removeClass('via-url-popup-overlay')
              jQuery('.overlay-blur-base').removeClass('overlay-blur');
            },
            this
          )
        });

        var alt = base.find('.alt');

        alt.on('shown.bs.dropdown', function () {
          $(this).find('input').focus();
        });

        alt.find('input').change(function () {
          if ($(this).val().length) {
            alt.addClass('filled');
          } else {
            alt.removeClass('filled');
          }
        }).change();

        this.getFileUploaderElement().addClass('ready').removeClass('loading');

        this.elementWidth = this.getFileUploaderElement().width();
      },
      toggleDelete: function () {
        var base = this.getFileUploaderElement();

        if (base.hasClass('remove-mark')) {
          base.removeClass('remove-mark');

        } else {
          base.addClass('remove-mark');
        }

        base.find('input.input-delete').click();
        base.find('.dropdown').click();
      },
      uploadFromComputer: function () {
        this.getFileUploaderElement().find('input[type=file]').val('').click();
      },
      doUploadFromFile: function (event) {
        var self = this;
        self.$dispatch('before-new-files-uploaded', self);

        var multiple = event.target.files.length > 1;
        this.commonData.action = 'uploadFromFile';
        for (var i = 0; i < event.target.files.length; i++) {
          var formData = new FormData();
          formData.append('file', event.target.files[i]);
          if (multiple) {
            formData.append('index', i);
          }
          if (0 === i && undefined !== this.alt) {
            formData.append('alt', this.alt);
          }
          this.doRequest(formData, this.viaUrlPopup.data('multiple'));
        }
      },
      uploadViaUrl: function () {
        this.viaUrlPopup.dialog('open');
        this.getFileUploaderElement().find('.dropdown').click();
      },
      doUploadViaUrl: function () {
        var self = this;
        this.viaUrlPopup.dialog('close');
        var formData = new FormData();
        this.commonData.action = 'uploadFromURL';
        if (jQuery('input.copy-to-file', this.viaUrlPopup).prop('checked')) {
          formData.append('copy', 1);
        }
        if (this.multiple) {
          var area = jQuery('textarea.urls', this.viaUrlPopup);
          var urls = area.val().split('\n');

          urls.forEach(function (url, index) {
            url = url.replace(/^:?\/\//, '');

            if (!/^https?:\/\//i.test(url)) {
              url = 'http://' + url;
            }

            formData.append('uploadedUrl', url);

            if (0 === index && undefined !== self.alt) {
              formData.append('alt', self.alt);
            } else {
              formData.delete('alt');
            }

            self.doRequest(formData, true);
          });

          area.val('');

        } else if (jQuery('input.url', this.viaUrlPopup).val()) {
          var url = jQuery('input.url', this.viaUrlPopup).val();
          url = url.replace(/^:?\/\//, '');

          if (!/^https?:\/\//i.test(url)) {
            url = 'http://' + url;
          }
          formData.append('uploadedUrl', url);

          if (undefined !== self.alt) {
            formData.append('alt', self.alt);
          }

          self.doRequest(formData, false);
        }
      },
      showAlt: function () {
        var base = this.getFileUploaderElement();

        base.find('li.alt-text .value').hide();
        base.find('li.alt-text .input-group').css('display', 'table');
        base.find('li.alt-text .input-group input').focus();
      },
      doChangeAlt: function (event) {
        var base = this.getFileUploaderElement();

        if (!event.keyCode || 13 === event.keyCode) {
          if (event.keyCode === 13) {
            $('.alt > .dropdown-toggle', base).trigger('click.bs.dropdown');
          }

          //TODO remove
          base.find('li.alt-text .input-group').hide();
          base.find('li.alt-text .value span').text($(event.target).val());
          base.find('li.alt-text .value').show();

          event.preventDefault();
          return false;
        }
      },
      getTemporaryContainer: function () {
        var div = document.createElement('DIV');
        div.style.display = 'none';
        jQuery('body').get(0).appendChild(div);
        return jQuery(div);
      },

      doRequest: function (formData, multiple) {
        var self = this;

        formData.append('object_id', self.getFileUploaderElement().data('objectId'));
        if (self.commonData.name === undefined || !multiple) {
            formData.append('name', self.getFileUploaderElement().data('name'));
        }
        formData.append('max_width', self.getFileUploaderElement().data('maxWidth'));
        formData.append('max_height', self.getFileUploaderElement().data('maxHeight'));
        formData.append('multiple', multiple ? '1' : '0');

        self.assignWait();

        return jQuery.ajax({
          url: URLHandler.buildURL(self.commonData),
          type: 'post',
          xhr: function () {
            return jQuery.ajaxSettings.xhr();
          },
          success: function (data, status, xhr) {
            var handler = function (xhr, s, data) {
              if (false !== data) {
                if (self.multiple) {
                  self.$dispatch('new-file-uploaded', formData.get('index'), data, self);
                } else {
                  self.reloadContent = data;
                  self.reloadFromContent = true;
                  self.$reload();
                }
              }
            };

            handler(xhr, status, data);
          },
          error: function (jqXHR, status, errorThrown) {
            if (jqXHR.status === 413) {
              self.realErrorMessage = core.t('File uploading error 1');
              self.error = true;
              core.trigger('message', {
                message: self.realErrorMessage,
                type: 'warning'
              });
              self.unassignWait();
            } else {
              core.trigger('message', {
                message: errorThrown,
                type: 'warning'
              });
              self.$reload();
            }
          },
          data: formData,
          cache: false,
          contentType: false,
          processData: false
        });
      }
    }
  };

  XLiteVue.component('xlite-file-uploader', XLiteFileUploader);

  core.bind(
    'itemListNewItemCreated',
    function (event, params) {
      var field = jQuery(params.line).find('.inline-file-uploader');
      field = field.last();

      var element = field.find('xlite-file-uploader').get(0);
      var dataElement = jQuery(element).children('.file-uploader').get(0);

      if (element && dataElement) {
        jQuery(dataElement).attr('data-name', jQuery(dataElement).attr('data-name').replace(/\[0\]/, '[' + (-1 * params.idx) + ']'));

        var v = new Vue();
        v.el = element;
        v.$compile(element);
      }
    }
  );

  return XLiteFileUploader;
});
