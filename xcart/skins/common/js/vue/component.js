/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('js/vue/component', ['js/underscore', 'vue/vue'], function (_, Vue) {

  var XLiteVueComponent = function (name, definition) {
    this.name = name;
    this.definition = Vue.extend(definition);
  };

  XLiteVueComponent.prototype.extend = function (definition) {
    this.definition = this.definition.extend(definition);
    this._provideParentMethods();
  };

  XLiteVueComponent.prototype._provideParentMethods = function () {
    var methods = this.definition.options.methods;
    var parent = this.definition.super.options.methods;

    for (var methodName in methods) if ('undefined' === typeof(methods.hasOwnProperty) || methods.hasOwnProperty(methodName)) {
      if (parent[methodName]) {
        methods[methodName].parent = parent[methodName];
      }
    }
  };

  return XLiteVueComponent;
});
