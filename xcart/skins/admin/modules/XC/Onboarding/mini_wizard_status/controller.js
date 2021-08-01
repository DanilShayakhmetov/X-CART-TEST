/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Mini status widget
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard_mini_status', ['ready'], function() {
  var WizardMiniStatus = Object.extend({
    constructor: function(base) {
      var progress = $(base).find('.progress-circle');
      var widget = progress.circleProgress({
        value: progress.data('value'),
        size: 40,
        startAngle: Math.PI / 2,
        fill: 'white',
        emptyFill: 'rgba(255,255,255,0.1)'
      });
    }
  });

  new WizardMiniStatus('#wizard-mini-status');

  return WizardMiniStatus;
});
