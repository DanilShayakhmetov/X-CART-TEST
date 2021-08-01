/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ConversationsListView() {
  ConversationsListView.superclass.constructor.apply(this, [jQuery('.conversations .items-list')]);

  this.bind('local.unshade', _.bind(this.forceUnshade, this));
  this.bind('local.unshade', _.bind(this.bindSelectAll, this));

  this.bindSelectAll();
}

extend(ConversationsListView, ItemsList);

// Force unshade
ConversationsListView.prototype.forceUnshade = function () {
  unassignWaitOverlay(jQuery('.conversations .dialog-content'), true);
};

ConversationsListView.prototype.bindSelectAll = function () {
  var self = this;
  var selectAll = jQuery('.conversations .dialog-content .items-list table .select-all input.selectAll');
  var selectors = jQuery('.conversations .dialog-content .items-list table td.select input[type=checkbox]');

  selectAll.change(function () {
    if ($(this).is(':checked')) {
      self.triggerVent('selector.massChecked', {widget: self});
      selectors.attr('checked', true);
    } else {
      selectors.attr('checked', false);
      self.triggerVent('selector.massUnchecked', {widget: self});
    }
  });

  selectors.change(function () {
    if (selectors.filter(':checked').length > 0) {
      self.triggerVent('selector.checked', {widget: self});
    } else {
      self.triggerVent('selector.unchecked', {widget: self});
    }
  });

  if (!selectors.length) {
    self.triggerVent('empty-list', {widget: self});
  } else {
    self.triggerVent('not-empty-list', {widget: self});
  }
};

// Show page by page link
ConversationsListView.prototype.showPage = function (handler) {
  return this.process('pageId', jQuery(handler).data('pageid'));
};

ConversationsListView.prototype.listeners.pagesCount = function (handler) {
  jQuery(':input.page-length', handler.container).change(
    function () {
      if (this.form) {
        var hnd = function () {
          return false;
        };
        jQuery(this.form).submit(hnd);
        var f = this.form;
        setTimeout(
          function () {
            jQuery(f).unbind('submit', hnd);
          },
          500
        );
      }

      return !handler.changePageLength(this);
    }
  );
};

// Get event namespace (prefix)
ConversationsListView.prototype.getEventNamespace = function () {
  return 'list.conversations';
};

/**
 * Load product lists controller
 */
core.autoload(ConversationsListView);