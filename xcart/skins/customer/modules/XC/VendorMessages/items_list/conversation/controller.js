/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function MessagesListController(base) {
  MessagesListController.superclass.constructor.apply(this, arguments);
};

extend(MessagesListController, ListsController);

MessagesListController.prototype.name = 'MessagesListController';

MessagesListController.prototype.findPattern += '.items-list.conversation-messages';

MessagesListController.prototype.getListView = function () {
  return new MessagesListView(this.base);
};

function MessagesListView(base) {
  MessagesListView.superclass.constructor.apply(this, arguments);
}

extend(MessagesListView, ListView);

MessagesListView.prototype.postprocess = function (isSuccess, initial) {
  MessagesListView.superclass.postprocess.apply(this, arguments);

  if (isSuccess) {
    this.base.find('.separator.closed a').click(_.bind(this.handleOpenList, this));
    this.base.find('.separator.opened a').click(_.bind(this.handleCloseList, this));

    if (
      !_.isUndefined(this.base.parents('form').get(0))
      && !_.isUndefined(this.base.parents('form').get(0).commonController)
    ) {
      this.base.parents('form').get(0).commonController.enableBackgroundSubmit();
    }

    this.base.find('.action-buttons a').click(_.bind(this.handleBackgroundAction, this));

    core.bind('conversationMessageCreated', _.bind(this.handleCreateMessage, this));
  }
};

MessagesListView.prototype.handleOpenList = function (event) {
  this.load({display_all: 1});

  return false;
};

MessagesListView.prototype.handleCloseList = function (event) {
  this.load({display_all: 0});

  return false;
};

MessagesListView.prototype.handleCreateMessage = function (event) {
  this.load();
};

MessagesListView.prototype.handleBackgroundAction = function (event) {
  core.post(jQuery(event.target).attr('href'));

  return false;
};

// Get event namespace (prefix)
MessagesListView.prototype.getEventNamespace = function () {
  return 'list.conversation.messages';
};

/**
 * Load product lists controller
 */
core.autoload(MessagesListController);
