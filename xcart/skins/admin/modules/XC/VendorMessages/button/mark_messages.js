/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Mark messages as
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

StickyPanelModelList.prototype.enableMarkSelected = function () {
  var exportBtn = this.base.find('button.mark-as:first');
  exportBtn.find('span:first').text(core.t('Mark selected'));
};

StickyPanelModelList.prototype.disableMarkSelected = function () {
  var exportBtn = this.base.find('button.mark-as:first');
  exportBtn.find('span:first').text(core.t('Mark all'));
};

StickyPanelModelList.prototype.showButton = function () {
  this.base.find('button.mark-as:first').show();
};

StickyPanelModelList.prototype.hideButton = function () {
  this.base.find('button.mark-as:first').hide();
};

decorate(
  'StickyPanelModelList',
  'reposition',
  function (selector) {
    arguments.callee.previousMethod.apply(this, arguments);

    var widget = this.base.parents('form').eq(0).find('.widget.items-list').length > 0
      ? this.base.parents('form').eq(0).find('.widget.items-list').get(0).itemsListController
      : null;

    if (widget) {
      widget.bind('local.selector.checked', _.bind(this.enableMarkSelected, this))
        .bind('local.selector.unchecked', _.bind(this.disableMarkSelected, this))
        .bind('local.selector.massChecked', _.bind(this.enableMarkSelected, this))
        .bind('local.selector.massUnchecked', _.bind(this.disableMarkSelected, this))
        .bind('local.empty-list', _.bind(this.hideButton, this))
        .bind('local.not-empty-list', _.bind(this.showButton, this));
      core.bind(
        'stickyPanelReposition',
        _.bind(this.disableMarkSelected, this)
      );
    }
  }
);
