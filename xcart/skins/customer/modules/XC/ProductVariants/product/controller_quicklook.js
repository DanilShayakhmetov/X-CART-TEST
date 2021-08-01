/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product quicklook controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

window.ProductQuickLookVariantViewLoading = false;

/**
 * Controller
 */
function ProductQuickLookVariantView (base) {
  this.base = jQuery(base);
  var self = this;

  core.bind(
    'update-product-page',
    function(event, productId) {
      self.loadVariantsImages(productId);
    }
  );
}

ProductQuickLookVariantView.autoload = function()
{
  if (jQuery('.ui-dialog.ui-widget').length > 1) {
    jQuery('.ui-dialog.ui-widget:not(:last)').off();
    jQuery('.ui-dialog.ui-widget:not(:last)').remove();
  }

  jQuery('.widget-controller .product-quicklook .product-photo').each(
    function() {
      new ProductQuickLookVariantView(this);
    }
  );
};

// Load variant image
ProductQuickLookVariantView.prototype.loadVariantsImages = function(productId, shade)
{
  this.base = jQuery('.widget-controller .product-quicklook .product-photo');
  if (this.base.siblings('.has-variants-mark').length > 0
      && this.base.siblings('.has-variants-mark').data('value')
      && window.ProductQuickLookVariantViewLoading === false
  ) {
    window.ProductQuickLookVariantViewLoading = true;
    if (shade) {
      this.base.find('.image').append('<div class="single-progress-mark"><div></div></div>')
    }

    this.loadVariantsImagesShade = shade;

    // Request variant images info
    core.get(
      URLHandler.buildURL(this.getURLParametersForLoadVariantsImages(productId)),
      _.bind(this.handleLoadVariantsImages, this),
      null,
      { dataType: 'json' }
    );
  }
};

// Get URL parameters for variant image loading routine
ProductQuickLookVariantView.prototype.getURLParametersForLoadVariantsImages = function(productId)
{
  var params = {product_id: productId};
  params = array_merge(params, core.getWidgetsParams('update-product-page', params));

  return array_merge({'target': 'product', 'action': 'get_variant_images'}, params);
};

// Load variant image handler
ProductQuickLookVariantView.prototype.handleLoadVariantsImages = function (XMLHttpRequest, textStatus, data)
{
  if (data && _.isString(data)) {
    data = jQuery.parseJSON(data);
  }

  if (this.base.parents('.product-details:first').find('.product-image-gallery:visible').length > 0) {
    this.processVariantImageAsGallery(data);
  } else {
    this.processVariantImageAsImage(data);
  }

  if (this.loadVariantsImagesShade) {
    this.base.find('.product-details-info .single-progress-mark').remove();
  }

  window.ProductQuickLookVariantViewLoading = false;
};

ProductQuickLookVariantView.prototype.processVariantImageAsGallery = function (data) {
  var self = this;

  var imageChanged = false;

  var galleries = this.base.parents('.product-details:first').find('.product-image-gallery');

  galleries.each(function () {
    var $gallery = jQuery(this);

    if (data && _.isObject(data)) {

      imageChanged = imageChanged || $gallery.find('li.variant-image a').attr('href') != data.full.url;

      if (imageChanged) {

        // Remove old variant image
        var li = $gallery.find('li:eq(0)').clone(true);

        $gallery.find('li.variant-image').remove();

        // Change images
        var elm = li.find('a img');
        elm.attr('width', data.gallery.w)
          .attr('height', data.gallery.h)
          .attr('src', data.gallery.url)
          .attr('srcset', data.gallery.srcset)
          .attr('alt', data.gallery.alt)
          .css({width: data.gallery.w + 'px', height: data.gallery.h + 'px'});

        elm = li.find('img.middle');
        elm.attr('width', data.main.w)
          .attr('height', data.main.h)
          .attr('src', data.main.url)
          .attr('srcset', data.main.srcset)
          .attr('alt', data.main.alt)
          .css({width: data.main.w + 'px', height: data.main.h + 'px'});

        // Change gallery link
        li.find('a')
          .attr('href', data.full.url)
          .attr('rev', 'width: ' + data.full.w + ', height: ' + data.full.h);

        li.addClass('variant-image');

        $gallery.find('li:eq(0)').before(li);

        // Gallery icon vertical aligment
        var margin = (li.height() - li.find('a img').height()) / 2;

        li.find('a img').css({
          'margin-top': Math.ceil(margin) + 'px',
          'margin-bottom': Math.floor(margin) + 'px'
        });
      }

    } else if ($gallery.find('li.variant-image').length > 0) {

      imageChanged = true;

      // Remove old variant image
      $gallery.find('li.variant-image').remove();

    }
  });
  if (imageChanged) {
    self.initializeGallery();
    core.trigger('initialize-product-gallery');

    self.applyToGalleries(function (gallery) {
      if (gallery.get(0)) {
        $(gallery.get(0)).find('a').click();
      }
    });
  }
};

ProductQuickLookVariantView.prototype.initializeGallery = function ()
{
  var self = this;
  self.galleries = [];
  jQuery('.product-image-gallery', this.base.parents('.product-details:first')).each(function () {
    self.galleries.push($(this).find('li'));
  });
};

ProductQuickLookVariantView.prototype.applyToGalleries = function (callback)
{
  if (this.galleries) {
    var self = this;
    this.galleries.forEach(function (gallery) {
      callback.apply(self, [gallery]);
    })
  }
};

ProductQuickLookVariantView.prototype.processVariantImageAsImage = function(data)
{
  if (data && _.isObject(data)) {
    var elm = this.base.find('img.product-thumbnail');
    elm.attr('width', data.main.w)
      .attr('height', data.main.h)
      .attr('src', data.main.url)
      .attr('srcset', data.main.srcset)
      .attr('alt', data.main.alt)
      .css({ width: data.main.w + 'px', height: data.main.h + 'px' });

    this.base.find('a.cloud-zoom').attr('href', data.full.url);
    this.base.find('.cloud-zoom').trigger('cloud-zoom');

  } else {
    this.applyDefaultImage();
  }

};

ProductQuickLookVariantView.prototype.applyDefaultImage = function()
{
  var img = this.base.siblings('.default-image').find('img');
  var elm = this.base.find('img.product-thumbnail');

  elm.attr('src', img.attr('src'))
    .attr('width', img.attr('width'))
    .attr('height', img.attr('height'))
    .css({
      'width':  img.attr('width') + 'px',
      'height': img.attr('height') + 'px'
    });

  this.base.find('a.cloud-zoom').attr('href', img.attr('src'));

  this.base.find('.loupe').hide();

  var zoom = this.base.find('a.cloud-zoom').data('zoom');
  if (zoom) {
    zoom.destroy();
  }
  this.base.find('a.cloud-zoom')
    .unbind('click')
    .click(function() { return false; });
}


core.autoload(ProductQuickLookVariantView);

core.microhandlers.add(
  'ProductQuickLookVariantView',
  '.widget-controller .product-quicklook .product-photo',
  function (event) {
    ProductQuickLookVariantView.autoload();
  }
);
