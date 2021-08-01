/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * File selector button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonSaleSelectedButton()
{
  PopupButtonSaleSelectedButton.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonSaleSelectedButton, PopupButton);

PopupButtonSaleSelectedButton.prototype.pattern = '.sale-selected-button';

decorate(
  'PopupButtonSaleSelectedButton',
  'callback',
  function (selector, link)
  {
    // previous method call
    arguments.callee.previousMethod.apply(this, arguments);

    // Autoloading of browse server link (popup widget).
    // TODO. make it dynamically and move it to ONE widget initialization (Main widget)
    core.autoload(SalePriceValue);
  }
);

decorate(
  'PopupButtonSaleSelectedButton',
  'afterSubmit',
  function (selector)
  {
    // previous method call
    arguments.callee.previousMethod.apply(this, arguments);

    var itemsListController = this.base
      .closest('.sticky-panel')
      .siblings('.widget.items-list')
      .get(0).itemsListController;

    itemsListController.loadWidget();
  }
);

decorate(
  'PopupButtonSaleSelectedButton',
  'getURLParams',
  function (button)
  {
    var urlParams = arguments.callee.previousMethod.apply(this, arguments);

    var itemsListForm = this.base.closest('form');
    itemsListForm.find('[name*="select"]:checked')
      .each(
        function (index, elem) {
          urlParams[elem.name] = 1
        }
      );
    urlParams['itemsList'] = itemsListForm.find('input[name="itemsList"]').val();

    if ('category_products' === core.getTarget() && '' !== core.getURLParam('id')) {
      urlParams['category_id'] = core.getURLParam('id');
    }

    return urlParams;
  }
);

decorate(
  'PopupButtonSaleSelectedButton',
  'eachClick',
  function (elem)
  {
    if (elem.linkedDialog) {
      jQuery(elem.linkedDialog).dialog('close').remove();
      elem.linkedDialog = undefined;
    }

    return arguments.callee.previousMethod.apply(this, arguments);
  }
);

core.autoload(PopupButtonSaleSelectedButton);
