/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping methods controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Widget target
ShippingMethodsView.prototype.widgetTarget = 'amazon_checkout';

// Widget class name
ShippingMethodsView.prototype.widgetClass = '\\XLite\\Module\\Amazon\\PayWithAmazon\\View\\Checkout\\AmazonShippingMethodsList';

ShippingMethodsView.prototype.getParams = function(params)
{
  params = this.callSupermethod('getParams', arguments);

  if (core.getURLParam('orderReference')) {
    params.orderReference = core.getURLParam('orderReference');
  }

  return params;
};
