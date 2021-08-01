/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: '#specialservices',
    canApply: function () {
      return 0 < this.$element.filter('#specialservices').length;
    },
    handler: function () {

      this.$element.select2({
        escapeMarkup: function (markup) {
          return markup;
        }
      });

      var handleChange = this.handleChange;
      var checkDependency = function () {
        var value = this.$element.val() || [];
        var CODValue = jQuery('#codvalue').get(0).commonController;
        if (value.indexOf('COD') === -1) {
          CODValue.hideByDependency()
        } else {
          CODValue.showByDependency()
        }

        var insuranceValue = jQuery('#insurancevalue').get(0).commonController;
        if (value.indexOf('Ins') === -1 && value.indexOf('InsRD') === -1) {
          insuranceValue.hideByDependency()
        } else {
          insuranceValue.showByDependency()
        }
      };

      this.handleChange = function (event) {
        handleChange.call(this, event);
        checkDependency.call(this);
      };

      _.debounce(_.bind(checkDependency, this), 100)();
    }
  }
);

