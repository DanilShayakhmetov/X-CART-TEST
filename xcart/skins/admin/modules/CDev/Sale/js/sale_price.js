/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * CheckSale for overpercent
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('modules/CDev/Sale/js/sale_price', ['js/vue/vue', 'form_model'], function (XLiteVue) {
  XLiteVue.component('xlite-form-model', {
    methods: {
      checkSale: function (model, type) {
        var baseprice = jQuery('.form-prices-and-inventory-price-price input')
        model.type = type;
        if (model.type == 'sale_percent') {
          if (model.value > 100) {
            model.value = 0;
          }
        } else {
          if (model.value > Number(baseprice.val())) {
            model.value = Number(baseprice.val());

          }
        }
      }
    }
  })
})
CommonForm.elementControllers.push(
  {
    pattern: '.form-prices-and-inventory-price-sale-price',
    handler: function () {
      var baseprice = jQuery('.form-prices-and-inventory-price-price input');
      var field = jQuery(this);
      var input = field.find('input[type="text"]');
      var type = field.find('input[type="hidden"]');

      input.change(function () {
        if (type.val() == 'sale_percent') {
          if (Number(input.val()) > 100) {
            input.val(100);
          }
        } else {
          if (Number(input.val()) > Number(baseprice.val())) {
            input.val(baseprice.val());
          }
        }
      })
    }
  }
)