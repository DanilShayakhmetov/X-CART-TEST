/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Zone details form controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind('load', function (event) {
  const $countries = $('#zone-countries');
  const $states = $('#zone-states');
  const $statesOptions = $states.find('option');

  const matchCountry = function($option, countryCodes) {
    let match = false;
    const value = $option.val();

    countryCodes.forEach(function (code) {
      if (value.startsWith(code + '_')) {
        match = true;
        return true;
      }
    });

    return match;
  };

  const updateStateList = function() {
    const countryCodes = $countries.val(); //array

    $states.html('');
    if (countryCodes.length) {
      $statesOptions.each(function () {
        const $option = $(this);
        if (matchCountry($option, countryCodes)) {
          $states.append(this);
        }
      });
    }

    $states.change();
  };

  if ($countries.length) {
    $countries.change(updateStateList);
    updateStateList();
  }
});
