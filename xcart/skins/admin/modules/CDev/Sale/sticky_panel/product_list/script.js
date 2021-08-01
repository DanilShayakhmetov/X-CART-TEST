/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky panel controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

decorate(
  'StickyPanelModelList',
  'handleAnyItemsSelected',
  function (selector) {
    arguments.callee.previousMethod.apply(this, arguments);

    this.base.find('button.action-enable-sale span:last').text(core.t('Put up for sale'));
    this.base.find('button.action-disable-sale span:last').text(core.t('Cancel sale'));
  }
);


decorate(
  'StickyPanelModelList',
  'handleNoSelectedItems',
  function (selector) {
    arguments.callee.previousMethod.apply(this, arguments);

    this.base.find('button.action-enable-sale span:last').text(core.t('Put all for sale'));
    this.base.find('button.action-disable-sale span:last').text(core.t('Cancel sale for all'));
  }
);


decorate(
  'StickyPanelModelList',
  'process',
  function (selector) {
    arguments.callee.previousMethod.apply(this, arguments);

    var self = this;

    core.bind('ProductsListMassUncheck', function () {
      //TODO change to self.getItemsListController() in 5.3.5
      var itemsListController = self.base.parents('form').eq(0).find('.widget.items-list').length > 0
        ? self.base.parents('form').eq(0).find('.widget.items-list').get(0).itemsListController
        : null;
      if (itemsListController) {
        jQuery('input.selectAll', itemsListController.container)
          .attr('checked', true)
          .click();
      }
    })
  }
);
