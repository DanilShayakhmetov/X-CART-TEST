/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Products list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ProductsListView(base)
{
  ProductsListView.superclass.constructor.apply(this, arguments);

  core.bind('popup.close', _.bind(this.handleCloseQuickLookPopup, this));

  this.requests = [];
}

extend(ProductsListView, ListView);

// Products list class
function ProductsListController(base)
{
  ProductsListController.superclass.constructor.apply(this, arguments);

  core.bind('updateCart', _.bind(this.updateCartHandler, this));
}

extend(ProductsListController, ListsController);

ProductsListController.prototype.name = 'ProductsListController';

ProductsListController.prototype.findPattern += '.items-list-products';

ProductsListController.prototype.getListView = function()
{
  return new ProductsListView(this.base);
};

ProductsListController.prototype.updateCartHandler = function(event, data) {
  var productPattern, product;

  if (_.isUndefined(data.items)) {
    return;
  }

  for (var i = 0; i < data.items.length; i++) {
    if (data.items[i].object_type == 'product') {

      // Added mark
      productPattern = '.product.productid-' + data.items[i].object_id;
      product = jQuery(productPattern, this.base);

      if (data.items[i].quantity > 0) {
        product.addClass('product-added');
        if (this.block) {
          this.block.triggerVent('item.addedToCart', {'view': this, 'item': product});
        }

      } else {
        product.removeClass('product-added');
        if (this.block) {
          this.block.triggerVent('item.removedFromCart', {'view': this, 'item': product});
        }
      }

      // Check inventory limit
      if (data.items[i].is_limit) {
        product.addClass('out-of-stock');

        if (this.block) {
          this.block.triggerVent('item.outOfStock', {'view': this, 'item': product});
        }

      } else {
        product.removeClass('out-of-stock');

        if (this.block) {
          this.block.triggerVent('item.stockIncrease', {'view': this, 'item': product});
        }
      }
    }
  }
};

//ProductsListView.prototype.touchProcess = false;

ProductsListView.prototype.postprocess = function(isSuccess, initial)
{
  ProductsListView.superclass.postprocess.apply(this, arguments);

  var self = this;

  if (isSuccess) {

    // Column switcher for 'table' display mode
    jQuery('.products-table .column-switcher', this.base).commonController('markAsColumnSwitcher');

    // Must be done before any event handled on 'A' tags. IE fix
    if (jQuery.browser.msie) {
      jQuery(this.productPattern, this.base).find('a')
        .each(function() {
          this.defferHref = this.href;
          this.href = 'javascript:void(0);';
        })
        .click(function() {
          if (!self.base.hasClass('ie-link-blocker')) {
            window.self.location = this.defferHref;
          }
        });
    }

    // Register "Changing display mode" handler
    jQuery('.display-modes a', this.base).click(
      function() {
        core.clearHash('pageId');
        return !self.load({'displayMode': jQuery(this).attr('class')});
      }
    );

    // Register "Sort by" selector handler
    jQuery('.sort-crit a', this.base).click(
      function () {
        core.clearHash('pageId');
        return !self.load({
          'sortBy': jQuery(this).data('sort-by'),
          'sortOrder': jQuery(this).data('sort-order'),
          'mode': 'append'
        });
      }
    );

    core.bind(
      'afterPopupPlace',
      function() {
        new ProductDetailsController(jQuery('.ui-dialog div.product-quicklook'));
      }
    );

    // Manual set cell's height
    this.base.find('table.products-grid tr').each(
      function () {
        var height = 0;
        jQuery('div.product', this).each(
          function() {
            height = Math.max(height, jQuery(this).height());
          }
        );
      }
    );

    // Process click on 'Add to cart' buttons by AJAX
    jQuery('.add-to-cart', this.base).not('.link').each(
      function (index, elem) {
        jQuery(elem).click(function() {
          var product = $(elem).closest('.product-cell').find('.product');
          if (!product.length) {
            product = $(elem).closest('.product-cell');
          }

          var pid = core.getValueFromClass(product, 'productid');
          var forceOptions = product.is('.need-choose-options');
          var btnStateHolder = $(elem).prop('disabled');

          if (pid && !self.isLoading) {
            $(elem).prop('disabled', true);
          }

          if (forceOptions) {
            $(elem).prop('disabled', btnStateHolder);
            self.openQuickLook(product);
          } else {
            core.trigger('addToCartViaClick', {productId: pid});
            self.addToCart(elem)
              .always(function() {
                $(elem).prop('disabled', btnStateHolder);
              });
          }
        });
      }
    );
  } // if (isSuccess)
}; // ProductsListView.prototype.postprocess()


ProductsListView.prototype.productPattern = '.products-grid .product, .products-list .product, .products-sidebar .product';


// Post AJAX request to add product to cart
ProductsListView.prototype.addToCart = function (elem) {
  elem = jQuery(elem);
  var pid = core.getValueFromClass(elem, 'productid');

  var xhr = new $.Deferred();

  if (pid && !this.isLoading) {

    if (this)
    xhr = core.post(
      URLHandler.buildURL({ target: 'cart', action: 'add' }),
      _.bind(this.handleAddToCart, this),
      this.addToCartRequestParams(elem),
      {
        rpc: true
      }
    );
  } else {
    xhr.reject();
  }

  return xhr;
};

ProductsListView.prototype.addToCartRequestParams = function (elem) {
  var pid = core.getValueFromClass(elem, 'productid');

  return {
    target:     'cart',
    action:     'add',
    product_id: pid
  }
}

ProductsListView.prototype.handleAddToCart = function (XMLHttpRequest, textStatus, data, isValid) {
  if (!isValid) {
    core.trigger(
      'message',
      {
        text: 'An error occurred during adding the product to cart. Please refresh the page and try to drag the product to cart again or contact the store administrator.',
        type: 'error'
      }
    );
  }
};

ProductsListView.prototype.focusOnFirstOption = _.once(function() {
  core.bind('afterPopupPlace', function(event, data){
    if (popup.currentPopup.box.hasClass('ctrl-customer-quicklook')) {
      var option = popup.currentPopup.box.find('.editable-attributes select, input').filter(':visible').first();
      option.focus();
    }
  })
});

ProductsListView.prototype.openQuickLook = function(elem) {
  this.focusOnFirstOption();
  return !popup.load(
    URLHandler.buildURL(this.openQuickLookParams(elem)),
    function () {
      jQuery('.formError').hide();
    },
    50000
  );
};

ProductsListView.prototype.handleCloseQuickLookPopup = function(event, data)
{
  if (data.box && data.box.find('.product-quicklook').length > 0 && jQuery('body').find(data.box).length > 0) {
    data.box.dialog('destroy');
    data.box.remove();
  }
};

ProductsListView.prototype.openQuickLookParams = function (elem) {
  var product_id = core.getValueFromClass(elem, 'productid');

  return {
    target:      'quick_look',
    action:      '',
    product_id:  product_id,
    only_center: 1
  }
}

// Get event namespace (prefix)
ProductsListView.prototype.getEventNamespace = function () {
  return 'list.products';
};

/**
 * Load product lists controller
 */
core.autoload(ProductsListController);
