/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'with_insert_link',
  '.input.with-insert-link',
  function () {
    var val = core.getCommentedData(this, 'insert_value');
    var input = $('> input', this);

    $('.insert-link a', this).click(function () {
      input.val(val).change();

      return false;
    });
  }
);