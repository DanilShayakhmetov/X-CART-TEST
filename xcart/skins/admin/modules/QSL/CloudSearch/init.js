/**
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


(function ($) {
    $(function () {
        var cloudSearchData = core.getCommentedData($('#cloud_search_popup_data'), 'cloudSearch');

        window.Cloud_Search = {
            apiUrl: cloudSearchData.apiUrl,
            apiKey: cloudSearchData.apiKey,
            price_template: cloudSearchData.priceTemplate,
            selector: cloudSearchData.selector,
            lang: cloudSearchData.lng,
            EventHandlers: {OnPopupRender: []},
            requestData: cloudSearchData.requestData
        };

        window.Cloud_Search.EventHandlers.OnPopupRender.push(function (searchTerm, element) {
            element.find('dt').each(function (i, dt) {
                var id = $(dt).attr('data-id'),
                    url = 'admin.php?target=product&product_id=' + id;

                $(dt).find('a').each(function (i, link) {
                    $(link).attr('href', url);
                });
            })
        });
    });
})(jQuery);