/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * JSTree initializer
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var TreeView = function (container, jstreeOptions) {
  if (!container) {
    return null;
  }

  this.container = jQuery(container).eq(0);

  if (!this.container.length) {
    return null;
  }

  this.preventEdit = false;

  this.mount(jstreeOptions);
  this.container.get(0).TreeViewController = this;
};

TreeView.prototype.on = function () {
  return this.container.on.apply(this.container, arguments);
}

TreeView.prototype.mount = function (options) {
  options = options || this.lastOptions || {};
  this.lastOptions = options;
  this.container.jstree(options);
  this.container.removeClass('not-processed');
};

TreeView.prototype.destroy = function (clearState) {
  clearState = clearState || false;

  if (clearState) {
    this.container.jstree(true).clear_state();
  }

  this.container.addClass('not-processed');
  this.container.jstree(true).destroy();
};

TreeView.prototype.callMethod = function (methodName, args) {
  if (this.container.length) {
    this.container.jstree.apply(this.container, arguments);
  }
};

TreeView.prototype.selectTemplate = function (id, preventEdit) {
  this.callMethod('deselect_all');
  this.callMethod('close_all');
  this.preventEdit = preventEdit;
  this.callMethod('select_node', 'template_' + id, true);
  this.preventEdit = false;

  var nodeElement = $('.jstree-anchor.jstree-clicked');
  this.scrollToElement(nodeElement.parent());
};

TreeView.prototype.scrollToElement = function (element) {
  var deltaX = this.calculateOffsetDeltaX(element);
  var deltaY = this.calculateOffsetDeltaY(element);

  var currentScrollX = this.container.scrollLeft();
  var currentScrollY = this.container.scrollTop();

  this.container.scrollLeft(currentScrollX + deltaX);
  this.container.scrollTop(currentScrollY + deltaY);
}

TreeView.prototype.calculateOffsetDeltaY = function (element) {
  var halfHeight = this.container.innerHeight() / 2;
  var offset = element.offset().top - this.container.offset().top;
  return offset - halfHeight + (element.height() / 2);
}

TreeView.prototype.calculateOffsetDeltaX = function (element) {
  var offset = element.offset().left - this.container.offset().left;
  return offset - 30;
}

