/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping estimator box widget
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Shipping estimator box widget
 */

function ShippingEstimateBox(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  if (!base) {
    args[0] = '.estimator';
  }

  this.bind('local.loaded', _.bind(this.triggerNeedCart, this));

  this.bind('local.preload', _.bind(this.collectShippingCost, this));
  this.bind('local.loaded', _.bind(this.checkShippingCost, this));

  this.callSupermethod('constructor', args);

  if (this.base.data('deferred')) {
    this.load();
  }
}

extend(ShippingEstimateBox, ALoadable);

// Shade widget
ShippingEstimateBox.prototype.shadeWidget = true;

// Update page title
ShippingEstimateBox.prototype.updatePageTitle = false;

// Widget target
ShippingEstimateBox.prototype.widgetTarget = 'cart';

// Widget class name
ShippingEstimateBox.prototype.widgetClass = 'XLite\\View\\ShippingEstimator\\ShippingEstimateBox';


ShippingEstimateBox.prototype.triggerNeedCart = function()
{
  core.trigger('reassignEstimator', this);
};

// Get event namespace (prefix)
ShippingEstimateBox.prototype.getEventNamespace = function()
{
  return 'cart.shippingestimate';
};

ShippingEstimateBox.prototype.preloadShippingCost = 0;

ShippingEstimateBox.prototype.collectShippingCost = function()
{
  if (this.base) {
      this.preloadShippingCost = this.base.data('shipping-cost');
  }
};

ShippingEstimateBox.prototype.checkShippingCost = function()
{
  if (this.base) {
      if (this.preloadShippingCost !== this.base.data('shipping-cost')) {
          core.trigger('updateCart', this);
      }

      this.collectShippingCost();
  }
};

// Load after page load
core.autoload(ShippingEstimateBox);