/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky panel controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

StickyPanelModelList.prototype.handleAnyItemsSelected = function () {
  this.base.find('button.action-enable span:last').text(core.t('Enable selected'));
  this.base.find('button.action-disable span:last').text(core.t('Disable selected'));
};

StickyPanelModelList.prototype.handleNoSelectedItems = function () {
  this.base.find('button.action-enable span:last').text(core.t('Enable all'));
  this.base.find('button.action-disable span:last').text(core.t('Disable all'));
};

decorate(
  'StickyPanelModelList',
  'process',
  function () {
    arguments.callee.previousMethod.apply(this, arguments);

    this.handleNoSelectedItems();
  }
);

decorate(
  'StickyPanelModelList',
  'reposition',
  function (selector) {
    arguments.callee.previousMethod.apply(this, arguments);


    var widget = this.base.parents('form').eq(0).find('.widget.items-list').length > 0
      ? this.base.parents('form').eq(0).find('.widget.items-list').get(0).itemsListController
      : null;

    if (widget) {
      widget.bind('local.selector.checked', _.bind(this.handleAnyItemsSelected, this))
        .bind('local.selector.massChecked', _.bind(this.handleAnyItemsSelected, this))
        .bind('local.selector.unchecked', _.bind(this.handleNoSelectedItems, this))
        .bind('local.selector.massUnchecked', _.bind(this.handleNoSelectedItems, this));

      core.bind(
        'stickyPanelReposition',
        _.bind(this.handleNoSelectedItems, this)
      );
    }
  }
);
