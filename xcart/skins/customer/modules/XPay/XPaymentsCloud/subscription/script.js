/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Orders list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function SubscriptionDetails()
{
    jQuery(this.base).each(function (index, elem) {
        var $elem = jQuery(elem);
        var action = jQuery('#' + jQuery('.subscription-orders', $elem).prop('id') + '-action');

        jQuery('.subscription-orders', $elem)
            .on('show.bs.collapse', function () {
                action.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            })
            .on('hidden.bs.collapse', function () {
                action.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            });
    });

    jQuery('i', this.base).eq(0).click();
}

SubscriptionDetails.prototype.base = '.subscription';

core.autoload('SubscriptionDetails');

jQuery('.card-change-btn').click(function() {
    var id = $(this).data('subscription-id');
    $('[id^=saved-cards-container-]').hide();
    $('[id^=current-card-]').show();
    $('#saved-cards-container-' + id).show();
    $('#current-card-' + id).hide();
});

jQuery('.status-control').parent().find('.status-text').click(function() {
    jQuery(this).parent().find('.status-control').slideToggle(100);
    var $caret = jQuery(this).find('.status-caret');
    if ($caret.hasClass('fa-caret-down')) {
        $caret.removeClass('fa-caret-down').addClass('fa-caret-up');
    } else {
        $caret.removeClass('fa-caret-up').addClass('fa-caret-down');
    }
});
