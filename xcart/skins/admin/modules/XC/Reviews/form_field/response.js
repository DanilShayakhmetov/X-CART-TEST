/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'response-field',
  '.response-field-block',
  function() {
    var input = jQuery(this).find('textarea');
    var link = jQuery(this).find('.open-link a');
    var input_block = jQuery(this).find('.field-block');
    var link_block = jQuery(this).find('.open-link');

    link.click(function (e) {
      e.preventDefault();
      input_block.show();
      link_block.hide();
    });

    input_block.toggle(input.val() !== '');
    link_block.toggle(input.val() === '');
  }
);
