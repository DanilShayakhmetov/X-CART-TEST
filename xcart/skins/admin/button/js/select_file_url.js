/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Select file url button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var lastFileSelectorButton;

function PopupButtonSelectFileURL()
{
  PopupButtonSelectFileURL.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonSelectFileURL, PopupButton);

PopupButtonSelectFileURL.prototype.pattern = '.select-file-url-button';

PopupButtonSelectFileURL.prototype.enableBackgroundSubmit = false;

decorate(
  'PopupButtonSelectFileURL',
  'callback',
  function (selector)
  {
    var base = jQuery('.file-select-form ul');
    jQuery('form', selector).each(
      function() {
        jQuery(this).addClass('no-popup-ajax-submit');
        this.commonController.backgroundSubmit = false;
      }
    );

    jQuery('#url', base).click(
      function() {
        jQuery(this).parents('li').eq(0).prev().find('input').click();
      }
    );

    jQuery('#url-copy-to-local', base).prop('disabled', '');
    jQuery('.url-label', base).hide().find('input[type=radio]').prop('checked', true);

    // File select dialog cannot be submitted if no file is selected
    jQuery('.file-select-form').submit(function (event) {
      var fileInputEmpty = true;
      jQuery('.file-select-form input[type="text"]')
      .each(
        function (index, elem) {
          if (jQuery(elem).val() !== "") {
            jQuery(selector).find('.upload-file button').addClass('disabled').prop('disabled', 'disabled');
            fileInputEmpty = false;
          }
        }
      );
      return !fileInputEmpty;
    });

    lastFileSelectorButton = lastPopupButton;
  }
);

core.autoload(PopupButtonSelectFileURL);
