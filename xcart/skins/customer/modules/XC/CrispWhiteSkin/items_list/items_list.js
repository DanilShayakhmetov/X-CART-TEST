/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

decorate(
  'ListView',
  'postprocess',
  function(isSuccess, initial)
  {
    arguments.callee.previousMethod.apply(this, arguments);

    if (isSuccess) {
      var o = this;

      jQuery('.per-page-box .list-type-grid a', this.base).click(function () {
        jQuery('html, body').animate({scrollTop: o.base.offset().top});
        return !o.load({'itemsPerPage': jQuery(this).data('items-per-page')});
      });

      jQuery('.product-cell > .product').mouseenter(function(){
        product = jQuery(this);
        
        productNameContainer = product.children("h5.product-name");
        productNameContainerHeight = parseInt(productNameContainer.css("line-height"), 10)*2; // 2 because of min 2 lines in product name

        productName = productNameContainer.children("a");
        productNameHeight = parseInt(productName.css("height"), 10);
        
        productMargin = 0;

        if (product.hasClass('low-stock') || product.hasClass('out-of-stock')) {
          productMargin = productMargin - parseInt(product.find(".product-items-available").css("height"), 10);
        }

        if (product.find(".buttons-container").children().length) {
          productMargin = productMargin - parseInt(product.find(".buttons-container").css("height"), 10);
        }
        
        if (productNameHeight > productNameContainerHeight) {
          productNameDelta = productNameContainerHeight - productNameHeight;          
          product.css({'margin-bottom': productMargin+productNameDelta + 'px'});
        }
        
      }).mouseleave(function(){
        product = jQuery(this);
        product.removeAttr("style");
      });
    }
  }
);
