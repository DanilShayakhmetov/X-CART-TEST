/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Variants
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(function () {
  var widget = $('.widget.items-list.product_variants');
  var listController = widget.get(0).itemsListController;

  listController.bind('local.newLineCreated', function (event, params) {
    if (!_.isUndefined(params.line)) {
      var field = params.line.find('.productvariant-sku input[type=text]').get(0);

      if (!_.isUndefined(field) && !_.isUndefined(field.commonController)) {
        field.commonController.element.initialValue = null;
      }
    }
  });

  listController.bind('local.line.new.remove', function (event, params) {
    var form = $(widget).parents('form').get(0);
    if (form) {
      jQuery(form).change();
    }
  });
});