/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Use smtp script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function () {
  core.microhandlers.add(
    'use_smtp',
    'input[name="use_smtp"]',
    function() {
      $(this).change(function () {
        if ($(this).prop('checked')) {
          $('[name="smtp_server_url"]').closest('li.input').show();
          $('[name="smtp_server_port"]').closest('li.input').show();
          $('[name="use_smtp_auth"]').closest('li.input').show();
          $('[name="smtp_username"]').closest('li.input').show();
          $('[name="smtp_password"]').closest('li.input').show();
          $('[name="smtp_security"]').closest('li.input').show();
          $('.input-checkbox-usesmtp .help-block').show();
        } else {
          $('[name="smtp_server_url"]').closest('li.input').hide();
          $('[name="smtp_server_port"]').closest('li.input').hide();
          $('[name="use_smtp_auth"]').closest('li.input').hide();
          $('[name="smtp_username"]').closest('li.input').hide();
          $('[name="smtp_password"]').closest('li.input').hide();
          $('[name="smtp_security"]').closest('li.input').hide();
          $('.input-checkbox-usesmtp .help-block').hide();
        }
      }).change();
    }
  );
});
