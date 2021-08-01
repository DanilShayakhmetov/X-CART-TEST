/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping methods list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Shipping methods list widget
 */

function ShippingMethodsView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  if (!base) {
    args[0] = 'form.shipping-methods';
  }

  this.bind('local.postprocess', _.bind(this.assignHandlers, this))
    .bind('local.loaded', _.bind(this.triggerChange, this));

  core.bind('updateCart', _.bind(this.handleUpdateCart, this))
    .bind('lockCheckout', _.bind(this.shade, this))
    .bind('unlockCheckout', _.bind(this.unshade, this))
    .bind('createShippingAddress', _.bind(this.handleCreateAddress, this))
    .bind('checkout.common.readyCheck', _.bind(this.handleCheckoutReadyCheck, this));

  ShippingMethodsView.superclass.constructor.apply(this, args);

  if (this.base.data('deferred')) {
    this.load();
    this.bind('local.loaded', function () {
      core.trigger('reloadPlaceOrder');
    });
  }
}

extend(ShippingMethodsView, ALoadable);

// Shade widget
ShippingMethodsView.prototype.shadeWidget = true;

// Update page title
ShippingMethodsView.prototype.updatePageTitle = false;

// Widget target
ShippingMethodsView.prototype.widgetTarget = 'checkout';

// Widget class name
ShippingMethodsView.prototype.widgetClass = '\\XLite\\View\\Checkout\\ShippingMethodsList';

// Postprocess widget
ShippingMethodsView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {

    // Check and save shipping methods
    this.base
      .commonController('enableBackgroundSubmit')
      .find('ul.shipping-rates input')
      .change(_.bind(this.handleMethodChange, this));

    this.base
      .find('.shipping-selector-box select')
      .change(_.bind(this.handleMethodChange, this));

    this.base
      .find('.shipping-selector-box select')
      .on('blur', _.bind(this.handleMethodChange, this));

    this.base.get(0).commonController
      .bind('local.submit.preprocess', _.bind(this.triggerChange, this))
      .bind('local.submit.success', _.bind(this.triggerChange, this))
      .bind('local.submit.success', _.bind(this.unshadeDelayed, this))
      .bind('local.submit.error', _.bind(this.unshade, this));

  }
};

ShippingMethodsView.prototype.getLoaderOptions = function()
{
  var list = ALoadable.prototype.getLoaderOptions.apply(this, arguments);
  list.timeout = 45000;

  return list;
};

ShippingMethodsView.prototype.handleUpdateCart = function(event, data)
{
  if ('undefined' != typeof(data.shippingMethodsHash)) {
    this.load();
  }
};

ShippingMethodsView.prototype.handleCreateAddress = function()
{
  this.load();
};

ShippingMethodsView.prototype.handleMethodChange = function()
{
  this.shade();

  return this.base.submit();
};

ShippingMethodsView.prototype.assignWaitOverlay = function(base)
{
  ShippingMethodsView.superclass.assignWaitOverlay.apply(this, arguments);

  assignShadeOverlay(jQuery('.step-payment-methods'));
};

ShippingMethodsView.prototype.unassignWaitOverlay = function(base)
{
  ShippingMethodsView.superclass.unassignWaitOverlay.apply(this, arguments);

  unassignShadeOverlay(jQuery('.step-payment-methods'), true);
};

// Get base element for shade / unshade operation
ShippingMethodsView.prototype.getShadeBase = function() {
  return this.base.closest('.step-shipping-methods');
};

ShippingMethodsView.prototype.handleCheckoutReadyCheck = function(event, state)
{
  if (0 < this.base.find('ul.shipping-rates input').length) {
    state.result = (0 < this.base.find('ul.shipping-rates input:checked').length)
      && state.result;

  } else if (0 < this.base.find('select[id|="methodid"]').length) {
    state.result = (0 <= this.base.find('select[id|="methodid"]').get(0).selectedIndex)
      && state.result;

  } else {
    state.result = false;
  }

  if (!state.result) {
    core.trigger(
      this.getEventNamespace() + '.error',
      {
        errorMsg: this.base.find('.shipping-methods-not-available-wrapper').data('error-msg')
      }
    );
  };

  state.blocked = this.base.get(0).isBgSubmitting
    || this.base.get(0).commonController.isChanged()
    || this.isLoading
    || state.blocked;
};

ShippingMethodsView.prototype.unshadeDelayed = function()
{
  setTimeout(
    _.bind(this.unshade, this),
    500
  );
};

ShippingMethodsView.prototype.triggerChange = function()
{
  core.trigger('checkout.common.anyChange', this);
};

// Get event namespace (prefix)
ShippingMethodsView.prototype.getEventNamespace = function()
{
  return 'checkout.shippingMethods';
};

// Load after page load
core.autoload(ShippingMethodsView);
