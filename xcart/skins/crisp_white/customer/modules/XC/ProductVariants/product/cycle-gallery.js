/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function() {
    decorate(
      'ProductDetailsView',
      'processVariantImageAsGallery',
      function (data) {
        arguments.callee.previousMethod.apply(this, arguments);
        core.trigger('init-cycle-gallery');
      }
    );

    decorate(
      'ProductQuickLookVariantView',
      'processVariantImageAsGallery',
      function (data) {
        arguments.callee.previousMethod.apply(this, arguments);
        core.trigger('init-cycle-gallery');
      }
    );
});