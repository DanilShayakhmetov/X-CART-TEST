/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order list item controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var OrdersListItem = ALoadable.extend({
  constructor: function OrdersListItem(base) {
    this.widgetTarget = core.getCommentedData(base, 'widgetTarget');
    this.widgetClass = core.getCommentedData(base, 'widgetClass');
    this.widgetParams = core.getCommentedData(base, 'widgetParams');

    OrdersListItem.superclass.constructor.apply(this, arguments);

    this.base = base;
    this.visible = false;
    this.openAtStart = this.base.hasClass('open-at-start');
    this.initialize();
  },

  initialize: function() {
    var self = this;
    var $elem = jQuery(this.base);
    var action = jQuery('#' + jQuery('.order-body-items-list', $elem).prop('id') + '-action');

    jQuery('.order-body-items-list', $elem)
      .on('show.bs.collapse', function () {
        self.visible = true;
        action.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
      })
      .on('hidden.bs.collapse', function () {
        self.visible = false;
        action.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
      });

    if (this.openAtStart) {
      this.instantOpenItemsList();
    }
  },

  instantOpenItemsList: function() {
    this.visible = true;
    this.base.find('.order-switcher i').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
    this.base.find('.order-body-items-list').addClass('in');
  },

  postprocess: function(isSuccess, initial) {
    if (!initial && (this.visible || this.openAtStart)) {
      this.instantOpenItemsList();

      core.autoload(ConfirmDeliveryButton, '.confirm-delivery');
    }

    OrdersListItem.superclass.postprocess.apply(this, arguments);
  }
});

// autoloaded in OrdersListView