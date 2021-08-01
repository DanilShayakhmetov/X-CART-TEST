/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * 'Save search filter' button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('div.save-search-filter .button-label').click(
      function () {
        var boxAction = jQuery(this).parent().find('.button-action').eq(0);
        if (0 < boxAction.length) {
          jQuery(boxAction).toggle();
        }
      }
    );

    jQuery('div.save-search-filter .button-action input').keypress(
      function (event) {
        if (event.keyCode === 13) {
          event.preventDefault();
          jQuery(this).siblings('button').click();
        }
      }
    );
  }
);
