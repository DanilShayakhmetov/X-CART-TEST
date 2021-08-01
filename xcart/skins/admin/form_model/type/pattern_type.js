/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xlitePattern', {
    params: ['inputmask-pattern'],
    bind: function () {
      var inputmaskPattern = this.params.inputmaskPattern;
      Inputmask(JSON.parse(inputmaskPattern)).mask(this.el);
    }
  });

})();
