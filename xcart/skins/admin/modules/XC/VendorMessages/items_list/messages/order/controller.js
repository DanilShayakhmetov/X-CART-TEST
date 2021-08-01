/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function OrderMessagesListView()
{
    ItemsList.apply(this, [jQuery('.order-messages .items-list')]);
}

extend(OrderMessagesListView, ItemsList);

OrderMessagesListView.prototype.initialize = function(elem, urlparams, urlajaxparams)
{
    var result = ItemsList.prototype.initialize.apply(this, arguments);

    var orderMessagesContainer = jQuery('.order-messages');
    var submitBtn = orderMessagesContainer.find('.submit');
    orderMessagesContainer.find('.new-message textarea').bind('input change', function() {
      if (this.value.length) {
        submitBtn.removeClass('disabled');
      } else {
        submitBtn.addClass('disabled');
      }
    });

    return result;
}

OrderMessagesListView.prototype.listeners.common = function(handler)
{
    handler.container.parents('form').get(0).commonController.enableBackgroundSubmit(false, function () {
      var inputs = jQuery(this).find(':input');

      if (inputs.length) {
        _.each(inputs, function(input) {
          input.enable();
        });
      }
    });
    handler.container.find('.separator.closed a').click(_.bind(handler.handleOpenList, handler));
    handler.container.find('.separator.opened a').click(_.bind(handler.handleCloseList, handler));
    var btn = handler.container.find('button.open-dispute');
    if (btn.length > 0) {
        new PopupButtonOpenDispute(btn);
    }

    core.bind('ordermessagescreate', _.bind(handler.handleCreateMessage, handler));
};

OrderMessagesListView.prototype.handleOpenList = function(event)
{
    this.params.urlajaxparams.display_all = 1;
    this.loadWidget();

    return false;
};

OrderMessagesListView.prototype.handleCloseList = function(event)
{
    this.params.urlajaxparams.display_all = 0;
    this.loadWidget();

    return false;
};

OrderMessagesListView.prototype.handleCreateMessage = function(event)
{
    this.loadWidget();
};

// Get event namespace (prefix)
OrderMessagesListView.prototype.getEventNamespace = function()
{
    return 'list.order.messages';
};

/**
 * Load product lists controller
 */
core.autoload(OrderMessagesListView);
