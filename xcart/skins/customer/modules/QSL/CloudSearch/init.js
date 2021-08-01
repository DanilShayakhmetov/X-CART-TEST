/**
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($) {
    var body = $('body'),
        cloudSearchData = core.getCommentedData(body, 'cloudSearch');

    window.Cloud_Search = {
        apiUrl: cloudSearchData.apiUrl,
        apiKey: cloudSearchData.apiKey,
        price_template: cloudSearchData.priceTemplate,
        selector: cloudSearchData.selector,
        lang: cloudSearchData.lng,
        EventHandlers: {OnPopupRender: []},
        requestData: _.extend(cloudSearchData.requestData, document.documentElement.lang ? {lang: document.documentElement.lang} : {}),
        positionPopupAt: function (searchInput) {
            var elem = searchInput.closest('.simple-search-box');

            return elem.length === 1 ? elem : searchInput;
        },
        pricesUrl: cloudSearchData.dynamicPricesEnabled
            ? URLHandler.buildURL({target: 'cloud_search_api', action: 'get_prices'})
            : null
    };
})(jQuery);
