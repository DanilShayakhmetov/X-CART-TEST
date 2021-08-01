/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function SwitchSubscriptionsProcessor () {
  jQuery('#switch-subscriptions-processor').change(function (event)
  {
    event.stopImmediatePropagation();

    const switchWrapper = jQuery('.switch-subscriptions-processor-container');
    if (switchWrapper.length) {
      assignShadeOverlay(switchWrapper);
    }

    core.get(
      URLHandler.buildURL({
        target: 'switch_subscriptions_processor',
        action: 'switch',
      }),
      function () {
        if (switchWrapper) {
          unassignShadeOverlay(switchWrapper);
        }
      }
    );

    return false;
  });
}

core.autoload(SwitchSubscriptionsProcessor);
