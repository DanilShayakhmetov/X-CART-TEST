/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: '#dataprovider',
    canApply: function () {
      return 0 < this.$element.filter('#dataprovider').length;
    },
    handler: function () {

      var uspsDependentInfo = jQuery('.usps-dependent-info');

      function toggleDataProviderInfo() {
        if (this.$element.val() === 'USPS') {
          uspsDependentInfo.removeClass('hidden');
        } else {
          uspsDependentInfo.addClass('hidden');
        }
      }

      var handleChange = this.handleChange;
      this.handleChange = function (event) {
        handleChange.call(this, event);
        toggleDataProviderInfo.call(this);
      };
    }
  }
);
