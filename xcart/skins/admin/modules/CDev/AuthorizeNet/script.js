/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Authorize Net SIM settings
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function () {
  $('#settings_hash_type').change(function () {
    if ($(this).val() === 'sha512') {
      $('#settings_signature').closest('tr').show();
      $('#settings_hash_key').closest('tr').hide();
    } else if ($(this).val() === 'md5') {
      $('#settings_signature').closest('tr').hide();
      $('#settings_hash_key').closest('tr').show();
    } else {
      $('#settings_signature').closest('tr').show();
      $('#settings_hash_key').closest('tr').show();
    }
  }).change();
});