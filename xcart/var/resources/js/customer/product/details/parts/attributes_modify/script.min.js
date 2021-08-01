/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product attributes functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Get product attribute element by name
function product_attribute(name_of_attribute)
{
  var e = jQuery('form[name="add_to_cart"] :input').filter(
    function() {
      return this.name && this.name.search(name_of_attribute) != -1;
    }
  );

  return e.get(0);
}

var textAttrCache = [];

function getAttributeValuesParams(product)
{
  var activeAttributeValues = '';
  var base = '.product-info-' + product.product_id;

  jQuery("ul.attribute-values input[type=checkbox]", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_';
    activeAttributeValues += jQuery(elem).is(":checked") ?  jQuery(elem).val() : jQuery(elem).data('unchecked');
    activeAttributeValues += ',';
  });

  jQuery("ul.attribute-values select", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_' + jQuery(elem).val() + ',';
  });

  jQuery("ul.attribute-values textarea", jQuery(base).last()).each(function(index, elem) {
    textAttrCache[jQuery(elem).data('attribute-id')] = jQuery(elem).val();
  });

  jQuery("ul.attribute-values input.blocks-input", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_' + jQuery(elem).val() + ',';
  });

  return {
    attribute_values: activeAttributeValues
  };
}

/**
 * Product attributes triggers are inputs and selectors
 * of the attribute-values block
 *
 * @returns {String}
 */
function getAttributeValuesTriggers()
{
  return 'ul.attribute-values input, ul.attribute-values select';
}

function getAttributeValuesShadowWidgets()
{
  return '.widget-fingerprint-product-price';
}

function bindAttributeValuesTriggers()
{
  var handler = function (productId) {
    core.trigger('update-product-page', productId);
  };

  var obj = jQuery("ul.attribute-values").closest('.product-details-info').find('form.product-details');
  if (obj.length > 0) {
    var productId = jQuery('input[name="product_id"]', obj).val();

    jQuery("ul.attribute-values input[type='checkbox']").unbind('change').bind('change', function (e) {handler(productId)});
    jQuery("ul.attribute-values input.blocks-input").unbind('change').bind('change', function (e) {handler(productId)});
    jQuery("ul.attribute-values select").unbind('change').bind('change', function (e) {handler(productId)});

    jQuery("ul.attribute-values textarea").each(function(index, elem) {
      if (textAttrCache[jQuery(elem).data('attribute-id')]) {
        jQuery(elem).val(textAttrCache[jQuery(elem).data('attribute-id')]);
      };
    });
  }
}

function BlocksSelector()
{
  jQuery('.product-details-info').on(
    'click',
    '.block-value:not(.selected)',
    function () {
      var $blockValue = jQuery(this);
      var blockValueId = $blockValue.data('value');
      var blockValueName = $blockValue.find('.block-value-name').html();
      var blockValueModifiers = $blockValue.data('modifiers');
      var blocksName = $blockValue.data('name');

      var $blocksSelector = $blockValue.closest('.blocks-selector');
      var $blocksTitle = $blocksSelector.find('.blocks-title');
      var $blocksInput = $blocksSelector.find('.blocks-input');

      $blocksTitle.removeClass('not-selected');
      $blocksInput.val(blockValueId).change();
      $blocksTitle.find('.attr-value-name').html(blockValueName);
      $blocksTitle.find('.attr-value-modifiers').html(blockValueModifiers);

      unselectAllBlocks(blocksName);
      $blockValue.addClass('selected');
    }
  );

  var timeout;
  var $tooltip;

  jQuery('.product-details-info').on({
    mouseenter: function () {
      $tooltip = jQuery(this).find('.unavailable-tooltip');
      timeout = setTimeout(function () {
        $tooltip.show(100);
      }, 250);
    },
    mouseleave: function () {
      clearTimeout(timeout);
      if ($tooltip !== undefined) {
        $tooltip.hide(100);
      }
    }
  }, '.block-value.unavailable');
}

function unselectAllBlocks(blocksName) {
  jQuery(`.block-value[data-name="${blocksName}"]`).removeClass('selected');
}

core.autoload(BlocksSelector);
core.bind('block.product.details.postprocess', BlocksSelector);
core.registerWidgetsParamsGetter('update-product-page', getAttributeValuesParams);
core.registerWidgetsTriggers('update-product-page', getAttributeValuesTriggers);
core.registerTriggersBind('update-product-page', bindAttributeValuesTriggers);
core.registerShadowWidgets('update-product-page', getAttributeValuesShadowWidgets);
core.registerShadowWidgets('update-product-page', function() {
  return '.widget-fingerprint-common-attributes';
});
