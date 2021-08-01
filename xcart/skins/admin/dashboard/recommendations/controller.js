/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'assignRecommendedModules',
  '.recommended-modules',
  function() {
    const modulesBlock = jQuery(this);
    jQuery(function () {
      const src = modulesBlock.data('src');

      if (src) {
        modulesBlock.append('<iframe src="' + src + '"/>');
      }

      modulesBlock.find('iframe').load(function() {
        jQuery(this).contents().on('recommendedModulesShown', function(event) {
          modulesBlock.removeClass('hidden');
        });
      });
    });
  }
);
