/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details override controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
   // Form AJAX-based submit

  jQuery(this).on('click', 'form.product-details [type="submit"]', function () {
    var form = jQuery('form.product-details');
    var files = form.find('.customer-attachments-values input[type=file]').get(0).files;

    if (0 < files.length) {
      var formData = new FormData();
      var totalSize = 0;
      var MB = 1048576;

      for (var i = 0; i < files.length; i++) {
        if (i >= maxFileUploads) {
          formData.append('max_file_uploads_warning', true);
          break;
        }

        var file = files[i];
        totalSize += file.size;

        // Add the file to the request.
        formData.append('customer_attachments[]', file, file.name);
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

      var xhr = new XMLHttpRequest();

      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
          var response = jQuery.parseJSON(xhr.responseText);
          var ids = response.ids;

          for (var i = 0; i < ids.length; i++) {
              form.append('<input type="hidden" name="attachments_ids[]" value="' + ids[i] + '">');
          }

          var msg = response.msg;
          jQuery.each(msg, function(key, value) {
              core.trigger('message', {'type':value.type, 'message':value.text});
          });

          form.submit();
        }
      };

      xhr.open('POST', URLHandler.buildURL({target: 'customer_attachments', action: 'ajax_upload'}), true);
      xhr.send(formData);

    } else {
      form.submit();
    }

    return false;
  });
});
