/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Pick address from address book controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
core.bind(
  'afterPopupPlace',
  function() {
    var box = jQuery('.select-address');
    box.find('.addresses > li').click(
      function() {
        var id = core.getValueFromClass(this, 'address')

        // Save address id
        var boxName;
        var shipping = jQuery('input[name="shippingAddress[id]"]');
        var billing = jQuery('input[name="billingAddress[id]"]');
        var same = jQuery('input[name="billingAddress[same_as_shipping]"]');
        if (box.hasClass('billing')) {
          billing.val(id);
          boxName = 'billingAddress';

        } else {
          shipping.val(id);
          boxName = 'shippingAddress';
        }

        // Set 'same-as-shipping' state
        var billingField = jQuery('.inline-field.profile-billingAddress');
        if (billing.val() == shipping.val()) {
          billingField.addClass('same-as-shipping');
          same.val('1');

        } else {
          billingField.removeClass('same-as-shipping');
          same.val('');
        }

        // Set text fields
        jQuery(this).find('li').each(
          function() {
            var elm = jQuery(this);
            jQuery('.profile-' + boxName + ' .address-' + elm.data('name') + ' .address-field').html(elm.find('.address-text-value').html());
          }
        );

        jQuery('.order-info form').change();

        popup.destroy();
      }
    );
  }
);
