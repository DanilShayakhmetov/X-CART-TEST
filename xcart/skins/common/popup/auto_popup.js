/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Auto popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'auto_popup',
  '.auto_popup',
  function () {
    var data = core.getCommentedData(this);
    var options = data.popupParams || null;

    if (!_.isUndefined(data.content)) {
      popup.open($(data.content), options);
    }
  }
);