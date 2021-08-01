/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Products list class
function ConversationsListController(base) {
  ConversationsListController.superclass.constructor.apply(this, arguments);
};

extend(ConversationsListController, ListsController);

ConversationsListController.prototype.name = 'ConversationsListController';

ConversationsListController.prototype.findPattern += '.items-list.conversations';

ConversationsListController.prototype.getListView = function () {
  return new ConversationsListView(this.base);
};

function ConversationsListView(base) {
  ConversationsListView.superclass.constructor.apply(this, arguments);
}

extend(ConversationsListView, ListView);

ConversationsListView.prototype.postprocess = function (isSuccess, initial) {
  ConversationsListView.superclass.postprocess.apply(this, arguments);

  if (isSuccess) {
    this.base.find('.separator.closed a').click(_.bind(this.handleOpenList, this));
    this.base.find('.separator.opened a').click(_.bind(this.handleCloseList, this));

    if (
      !_.isUndefined(this.base.parents('form').get(0))
      && !_.isUndefined(this.base.parents('form').get(0).commonController)
    ) {
      this.base.parents('form').get(0).commonController.enableBackgroundSubmit();
    }

    core.bind('ordermessagescreate', _.bind(this.handleCreateMessage, this));
  }
};

ConversationsListView.prototype.handleOpenList = function (event) {
  this.load({display_all: 1});

  return false;
};

ConversationsListView.prototype.handleCloseList = function (event) {
  this.load({display_all: 0});

  return false;
};

ConversationsListView.prototype.handleCreateMessage = function (event) {
  this.load();
};


// Get event namespace (prefix)
ConversationsListView.prototype.getEventNamespace = function () {
  return 'list.order.messages';
};

/**
 * Load product lists controller
 */
core.autoload(ConversationsListController);
