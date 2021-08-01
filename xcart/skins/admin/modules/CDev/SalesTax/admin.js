/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Taxes controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    jQuery('#ignore-memberships').click(
      function() {
        jQuery('.edit-sales-tax').toggleClass('no-memberships', jQuery(this).is(':checked'));
      }
    );

    jQuery('#taxablebase').change(
      function() {
        if ('P' == jQuery(this).val()) {
          jQuery('.edit-sales-tax').removeClass('no-taxbase');

        } else {
          jQuery('.edit-sales-tax').addClass('no-taxbase');
        }
      }
    );

    jQuery('#items-list-switcher').click(
      function() {
        if (jQuery('#shipping-rates').hasClass('hidden')) {
          jQuery('#shipping-rates').removeClass('hidden');
          jQuery('i.fa', this).removeClass('fa-caret-right');
          jQuery('i.fa', this).addClass('fa-caret-down');
        } else {
          jQuery('#shipping-rates').addClass('hidden');
          jQuery('i.fa', this).removeClass('fa-caret-down');
          jQuery('i.fa', this).addClass('fa-caret-right');
        }
      }
    );

    jQuery('#common-settings-link span').click(
      function() {
        var box = jQuery('#common-settings');
        var doExpand = jQuery(box).hasClass('hidden');
        var boxAction;
        if (doExpand) {
          boxAction = 'expand';
          jQuery(box).removeClass('hidden');
          jQuery('#common-settings-link span.collapsed-common-settings').addClass('hidden');
          jQuery('#common-settings-link span.expanded-common-settings').removeClass('hidden');

        } else {
          boxAction = 'collapse';
          jQuery(box).addClass('hidden');
          jQuery('#common-settings-link span.expanded-common-settings').addClass('hidden');
          jQuery('#common-settings-link span.collapsed-common-settings').removeClass('hidden');
        }

        core.post(
          URLHandler.buildURL({target: 'sales_tax', action: boxAction})
        )
      }
    );
  }
);
