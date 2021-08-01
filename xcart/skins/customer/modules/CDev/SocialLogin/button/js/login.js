/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Login link
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    const loginPopupButtonSelector = '.popup-button.popup-login';
    const urlParams = core.getCommentedData(loginPopupButtonSelector, 'url_params');

    if (urlParams.autoloadPopup) {
      jQuery(loginPopupButtonSelector).trigger('click');
    }
  }
);

