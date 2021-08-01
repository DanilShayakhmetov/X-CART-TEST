/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Abstract widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Abstract controller
function AController(base)
{
  if (!base) {

    // Search base DOM element(s)
    base = this.findBase();
  }

  if (base) {

    if (base.length > 1) {

      // Multiple binding
      for (var i = 0; i < base.length; i++) {
        eval('new ' + this.name + '(jQuery(base[i]))');
      }

    } else {

      // Simple binding
      this.bind(jQuery(base));
    }
  }
}

// Parent class - Base
extend(AController, Base);

// [ABSTRACT] Controller unique name
AController.prototype.name = null;

// Base DOM element
AController.prototype.base = null;

// Find jQuery pattern
AController.prototype.findPattern = null;

// Check - controller is catch base DOM element or not
AController.prototype.isBinded = function()
{
  return !!this.base;
}

// Bind controller to base DOM element
AController.prototype.bind = function(base)
{
  var result = false;

  if (this.name && !this.isBaseCatched(base) && this.detectBase(base)) {
    var o = this;
    base = jQuery(base);
    base.map(
      function() {
        this.controller = o;
      }
    );
    this.base = base;

    this.initialize();

    result = true;
  }

  return result;
}

// Check - controller is binded to base DOM element or not
AController.prototype.isBaseCatched = function(base)
{
  var result = false;

  if ('undefined' != typeof(base.map)) {
    base.map(
      function() {
        if (!result && typeof(this.controller) != 'undefined') {
          result = true;
        }
      }
    );

  } else if (typeof(base.controller) != 'undefined') {
    result = true;
  }

  return result;
}

// [ABSTRACT] Detect base
AController.prototype.detectBase = function()
{
  return !!this.findPattern;
}

// [ABSTRACT] Initialize controller
AController.prototype.initialize = function()
{
}

// Find base if controller create without base DOM element specification
AController.prototype.findBase = function()
{
  return this.findPattern ? jQuery(this.findPattern) : false;
}
