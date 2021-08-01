/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Language labels items list javascript controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

ItemsList.prototype.listeners.labels = function(handler)
{
  // Rollover
  jQuery('.label-name').click(
    function () {
      var main = jQuery(this).parents('.language-labels').eq(0);
      var info = main.find('.label-edit');
      if (0 < info.filter(':visible').length) {
        info.hide();

      } else {
        info.show();
      }

      return false;
    }
  );

  jQuery(function() {
    var width = Math.max(jQuery('.translations-header').outerWidth(), jQuery('.lng-marks').outerWidth());
    jQuery('.translations-header').width(width);
    jQuery('.lng-marks').width(width);
  });
}
