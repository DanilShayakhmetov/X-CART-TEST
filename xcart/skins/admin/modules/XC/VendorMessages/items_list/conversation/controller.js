/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function MessagesListView()
{
    ItemsList.apply(this, [jQuery('.conversation-messages .items-list')]);
}

extend(MessagesListView, ItemsList);

MessagesListView.prototype.listeners.common = function(handler)
{
    handler.container.parents('form').get(0).commonController.enableBackgroundSubmit();
    handler.container.find('.separator.closed a').click(_.bind(handler.handleOpenList, handler));
    handler.container.find('.separator.opened a').click(_.bind(handler.handleCloseList, handler));
    var btn = handler.container.find('button.open-dispute');
    if (btn.length > 0) {
        new PopupButtonOpenDispute(btn);
    }

    core.bind('conversationMessageCreated', _.bind(handler.handleCreateMessage, handler));
};

MessagesListView.prototype.handleOpenList = function(event)
{
    this.params.urlajaxparams.display_all = 1;
    this.loadWidget();

    return false;
};

MessagesListView.prototype.handleCloseList = function(event)
{
    this.params.urlajaxparams.display_all = 0;
    this.loadWidget();

    return false;
};

MessagesListView.prototype.handleCreateMessage = function(event)
{
    this.loadWidget();
};

// Get event namespace (prefix)
MessagesListView.prototype.getEventNamespace = function()
{
    return 'list.conversation.messages';
};

/**
 * Load product lists controller
 */
core.autoload(MessagesListView);
