/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Review email field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'model-selector.profile.selected',
  function(event, data) {
    jQuery(data.element).closest('form').find('#reviewername').val(data.data.selected_value);
  }
);

core.bind(
  'model-selector.profile.not-selected',
  function(event, data) {
    jQuery(data.element).closest('form').find('#reviewername').val('');
  }
);
