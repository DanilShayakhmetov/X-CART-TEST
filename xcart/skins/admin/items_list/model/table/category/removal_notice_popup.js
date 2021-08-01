/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Categories removal notice popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'removal-notice-popup',
  'body.target-categories',
  function () {
    popup.load(
      URLHandler.buildURL({
        target: 'categories',
        mode:   'removal_notice_popup',
        widget: '\\XLite\\View\\Page\\Admin\\CategoriesRemovalNotice'
      })
    );
  }
);
