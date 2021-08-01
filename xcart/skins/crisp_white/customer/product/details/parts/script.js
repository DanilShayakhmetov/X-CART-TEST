/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Single language selector
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


jQuery(function() {
  core.bind('block.product.details.postprocess', function() {
    $('.product-details-tabs ul.tabs').tabCollapse({
      tabsClass: 'hidden-xs',
      accordionClass: 'visible-xs'
    });
  });

  core.registerTriggersBind('update-product-page', function () {
    var heightFixer = function () {
      jQuery(this).addClass('notransition');

      if (this.scrollHeight > this.clientHeight) {
        this.style.height = (this.scrollHeight + 10) + 'px';
      }

      jQuery(this).removeClass('notransition');
    };

    jQuery('ul.attribute-values .form-control').floatingLabel();
    jQuery('ul.attribute-values textarea.form-control').keyup(heightFixer).keyup();
  });
});