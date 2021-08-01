/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

decorate(
  'ProductsListView',
  'addToCartRequestParams',
  function (elem) {
    var params = arguments.callee.previousMethod.apply(this, arguments);

    var ecData = jQuery('*[data-ga-ec-action]', elem.closest('.add-to-cart-button'));
    if (ecData.length !== 0) {
      ecData = ecData.data('ga-ec-action').data;

      if (!_.isUndefined(ecData.list)) {
        params.ga_list = ecData.list;
      }
    }

    return params;
  }
);

decorate(
  'ProductsListView',
  'openQuickLookParams',
  function (elem) {
    var params = arguments.callee.previousMethod.apply(this, arguments);

    var ecData = jQuery('*[data-ga-ec-action]', elem);
    if (ecData.length !== 0) {
      ecData = ecData.data('ga-ec-action').data;

      if (!_.isUndefined(ecData.list)) {
        params.ga_list = ecData.list;
      }
    }

    return params;
  }
);
