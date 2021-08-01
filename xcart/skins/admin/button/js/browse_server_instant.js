/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Browser server button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonBrowseServerInstant(base) {
  var obj = this;

  if (base) {
    this.base = base;
    this.eachCallback(base);
  } else {
    this.base = jQuery(this.pattern);
    this.base.each(
      function () {
        obj.eachCallback(this);
      }
    );
  }

  this.base.find('input.browse-server-input').change(function () {
    if ($(this).val()) {
      var data = core.getCommentedData($(this).parent());

      var form = $('<form method="post" style="display: none;"></form>');
      $('body').append(form);

      [
        'target',
        'action',
        'object',
        'objectId',
        'fileObject',
        'fileObjectId'
      ].forEach(function (a) {
        form.find('input[name="' + a + '"]').remove();
        form.append($('<input type="hidden">').prop('name', a).val(data[a]));
      });

      form.append($('<input type="hidden">').prop('name', 'xcart_form_id').val(xliteConfig.form_id));
      form.append($('<input type="hidden">').prop('name', 'file_select').val('local'));
      form.append($(this).clone());

      form.submit();
    }
  });
  PopupButtonBrowseServerInstant.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonBrowseServerInstant, PopupButton);

PopupButtonBrowseServerInstant.prototype.pattern = '.browse-server-instant-button';

PopupButtonBrowseServerInstant.prototype.options = {
  minWidth: 860,
  minHeight: 300
};

PopupButtonBrowseServerInstant.prototype.loadDialog = function (browseServerObject, link, catalog) {
  return loadDialogByLink(
    link,
    URLHandler.buildURL({
      'target': 'browse_server',
      'widget': '\\XLite\\View\\BrowseServer',
      'catalog': catalog
    }),
    this.options,
    function (selectorCallback, linkCallback) {
      PopupButtonBrowseServerInstant.prototype.callback.call(browseServerObject, selectorCallback, linkCallback);
    }
  );
};

decorate(
  'PopupButtonBrowseServerInstant',
  'callback',
  function (selector, link) {
    var browseServerObject = this;

    // Store fileinfo structure
    var fileInfo = core.getCommentedData('.up-level');

    // Delete categories popup dialog has 'back-button' button with defined action.
    // We change this action to 'popup dialog close' action.
    jQuery('.back-button').remove();

    // Double click event on UP LEVEL element.
    jQuery('a.up-level').each(
      function () {
        if (fileInfo.current_catalog != "") {
          jQuery(this).parent().bind(
            'dblclick',
            function () {

              // Close the previous popup window
              link.linkedDialog = undefined;
              jQuery(selector).dialog('close').remove();

              // Open new popup window. "Catalog" parameter is taken from "UP CATALOG" value
              return PopupButtonBrowseServerInstant.prototype.loadDialog.call(this, browseServerObject, link, fileInfo.up_catalog);
            }
          );
        } else {
          jQuery(this).parent().addClass('not-file-entry');
        }
      }
    );

    jQuery('.fs-entry a').each(
      function () {
        var entry = this;

        jQuery(this).parent().bind(
          // Selected entry by "one-mouse-click" gets "selected" CSS class
          'click',
          function () {
            jQuery('.fs-entry').removeClass('selected');
            jQuery(entry).parent().addClass('selected');
          }
        ).bind(
          // "double-click" event
          // AJAX-browse in file system of local server
          'dblclick',
          function () {
            var entryName = fileInfo.current_catalog + '/' + entry.title;

            // Close previous popup

            if (jQuery(entry).hasClass('type-catalog')) {
              // Catalog entry clicking opens new popup
              link.linkedDialog = undefined;
              jQuery(selector).dialog('close').remove();
              return PopupButtonBrowseServerInstant.prototype.loadDialog.call(this, browseServerObject, link, entryName);
            } else {
              jQuery('.browse-server-input', lastPopupButton).val(entryName.replace(/\\/g, '/')).change();

              assignWaitOverlay(jQuery(selector));
            }
          }
        );

        jQuery('.browse-selector-actions .choose-file-button').bind(
          'click',
          function () {
            var entry = jQuery('.fs-entry.selected');

            if (jQuery('a.type-file', entry).length > 0) {
              jQuery('.browse-server-input', lastPopupButton).val(
                fileInfo.current_catalog + '/' + jQuery('a.type-file', entry).attr('title').replace(/\\/g, '/')
              ).change();
              assignWaitOverlay(jQuery(selector));
            }
          }
        );
      }
    );

  }
);

core.autoload(PopupButtonBrowseServerInstant);
