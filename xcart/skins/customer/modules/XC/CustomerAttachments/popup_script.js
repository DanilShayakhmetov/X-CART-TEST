/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attachment popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


jQuery(document).ready(
  function() {
    core.microhandlers.add(
      'attachments-popup-upload-files',
      '.attachments-popup .attachment-form [type="submit"]',
      function() {
        jQuery(this).click(
          function(event) {
            var form = jQuery('.attachments-popup .attachment-form');
            var files = form.find('input[type=file]').get(0).files;

            if (0 < files.length) {
              var totalSize = 0;
              var MB = 1048576;

              for (var i = 0; i < files.length; i++) {
                if (i >= maxFileUploads) {
                  var maxFileUploadsWarning = form.find('[name="max_file_uploads_warning"]');
                  maxFileUploadsWarning.val(true);
                  break;
                }

                totalSize += files[i].size;
              }

              if (postSizeLimit < totalSize) {
                var postSizeLimitMB = postSizeLimit / MB;
                var errorMessage = core.t(
                  'Cannot attach the files. Total size of attached files may not exceed X MB',
                  {postSizeLimit: parseFloat(postSizeLimitMB.toPrecision(precision))}
                );

                core.trigger('message', {type: 'error', message: errorMessage});

                return false;
              }
            }
          }
        )
      }
    );
  }
);
