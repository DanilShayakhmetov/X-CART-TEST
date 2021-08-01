/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Admin Welcome block js-controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var WelcomeBlock = Object.extend({
  constructor: function WelcomeBlock(base) {
    this.base = base;
    this.closeButton = $('.close-button', base);
    this.foreverCheckbox = $('.hide-welcome-block', base);
    this.assignCloseHandler();
  },
  assignCloseHandler: function() {
    var self = this;
    var action = this.base.data('close-action');
    var target = this.base.data('close-target');

    this.closeButton.click(function() {
      var data = {
        'forever': self.foreverCheckbox.is(':checked') ? '1' : '0',
        'block': self.base.data('block-name')
      };

      core.post({ target: target, action: action }, null, data);

      self.base.hide();

      if (!jQuery('.admin-welcome:visible').length) {
        jQuery('.dashboard.welcome-visible').removeClass('welcome-visible');
      }
    });
  }
});

core.autoload(WelcomeBlock, '.admin-welcome');
