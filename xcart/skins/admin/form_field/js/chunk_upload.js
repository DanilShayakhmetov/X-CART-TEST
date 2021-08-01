/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Upload file by chunks field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ChunkUploadController(base) {
  if (!XMLHttpRequest.prototype.sendAsBinary) {
    XMLHttpRequest.prototype.sendAsBinary = function (sData) {
      var nBytes = sData.length, ui8Data = new Uint8Array(nBytes);
      for (var nIdx = 0; nIdx < nBytes; nIdx++) {
        ui8Data[nIdx] = sData.charCodeAt(nIdx) & 0xff;
      }
      this.send(ui8Data);
    };
  }

  ChunkUploadController.superclass.constructor.apply(this, arguments);
}

extend(ChunkUploadController, AController);

ChunkUploadController.prototype.name = 'ChunkUploadController';

ChunkUploadController.prototype.chunk_size = 8192;

ChunkUploadController.prototype.findPattern = '.chunk-file-upload input[type=file]';

ChunkUploadController.prototype.popupClass = 'upload-popup';

ChunkUploadController.prototype.block = null;

// Initialize controller
ChunkUploadController.prototype.initialize = function () {
  var obj = this;
  $(this.base).each(function () {
    obj.chunk_size = core.getCommentedData($(this).parent(), 'chunk_size');

    $(this).change(function (evt) {
      var file = evt.target.files[0];

      obj.processFile(file, $(this));
    });

    $(this).closest('.chunk-file-upload').removeClass('hidden');
  });
};

ChunkUploadController.prototype.processFile = function (file, input) {
  var obj = this;
  if (file) {
    var reader = new FileReader();
    var aborted = false;
    var done = false;
    var url_params = core.getCommentedData(input.parent(), 'form_params');
    url_params.filename = file.name;

    popup.open(input.parent().find('.popup-content').html(), {
      beforeClose: function () {
        input.val(null);
        if (!done) {
          reader.abort();
          aborted = true;
          url_params.action = 'abort';
          var _xhr = new XMLHttpRequest();
          _xhr.open("POST", URLHandler.buildURL(url_params), true);
          _xhr.send();
        }
        input.change();
      },
      dialogClass: obj.popupClass
    });

    var sent = 0;

    reader.onerror = obj.errorHandler;
    reader.onabort = function (e) {
      aborted = true;
    };

    reader.onloadstart = function (e) {
    };

    reader.onload = function (e) {
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        var response = $.parseJSON(xhr.response);

        if (response.status === 'SUCCESS') {
          sent += obj.chunk_size;

          if (sent < file.size) {
            var percentLoaded = Math.round((sent / file.size) * 100);

            obj.updateProgress(percentLoaded);

            if (!aborted) {
              url_params.basename = response.basename;
              reader.readAsBinaryString(file.slice(sent, sent + obj.chunk_size));
            }
          } else {
            done = true;
            var success_url_params = core.getCommentedData(input.parent(), 'success_form_params');
            success_url_params.basename = response.basename;
            success_url_params.filename = file.name;
            obj.updateProgress(100);

            var _xhr = new XMLHttpRequest();

            _xhr.onload = function () {
              var _response = $.parseJSON(_xhr.response);

              if (_response.status === 'SUCCESS') {
                if (_response.redirectUrl) {
                  core.redirectTo(_response.redirectUrl)
                }

                popup.close();
                core.trigger('message', {type: 'info', message: core.t('File was successfully uploaded')});
              } else {
                popup.close();
                core.trigger('message', {type: 'error', message: core.t('Error of uploading file.')});
                console.log(_response.message);
              }
            };

            _xhr.open("POST", URLHandler.buildURL(success_url_params), true);
            _xhr.send();
          }
        } else {
          popup.close();
          core.trigger('message', {type: 'error', message: core.t('Error of uploading file.')});
          console.log(response.message);
        }
      };

      xhr.open(
        "POST",
        URLHandler.buildURL(url_params),
        true
      );
      xhr.sendAsBinary(e.target.result);
    };

    reader.readAsBinaryString(file.slice(0, obj.chunk_size));
  }
};

ChunkUploadController.prototype.errorHandler = function (evt) {
  switch (evt.target.error.code) {
    case evt.target.error.NOT_FOUND_ERR:
      console.log('File not found');
      break;
    case evt.target.error.NOT_READABLE_ERR:
      console.log('File is not readable');
      break;
    case evt.target.error.ABORT_ERR:
      break;
    default:
      console.log('An error occurred reading this file');
  }
  popup.close();
};

ChunkUploadController.prototype.updateProgress = function (percent) {
  $('.' + this.popupClass).find('.file-upload-progress').attr('title', percent + '%').css('width', percent + '%');
};

core.autoload(ChunkUploadController);