/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Onboarding setup tiles controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
    var checkTilesVisibility = function() {
        if (jQuery('.onboarding-setup-tile').length === 0) {
            jQuery('.onboarding-setup-tiles').hide();
        }
    };

    checkTilesVisibility();

    jQuery('.onboarding-setup-tile .setup-tile-close').click(function () {
        let parent = jQuery(this).closest('.onboarding-setup-tile').hide();

        jQuery.cookie(parent.data('tile-type') + '_tileClosed', 1);

        setTimeout(function () {
            parent.remove();
            checkTilesVisibility();
        }, 0);
    });

    jQuery('.onboarding-setup-tile[data-tile-type="addons_ad"] .setup-tile-button button').click(function () {
        let parent = jQuery(this).closest('.onboarding-setup-tile');
        jQuery.cookie(parent.data('tile-type') + '_tileClosed', 1);
    });
});
