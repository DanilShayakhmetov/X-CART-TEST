/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product filter
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

ProductFilterView.prototype.reduceFiltersHandler = function (acc, item) {
    if (item.name === 'filter[price][0]' || item.name === 'filter[price][1]') {
        var multicurrencyRate = core.getCommentedData(jQuery('.product-filter'), 'multicurrency_rate');
        var storeCurrencyE = core.getCommentedData(jQuery('.product-filter'), 'store_currency_e');

        var roundBase = Math.pow(10, parseInt(storeCurrencyE));
        item.value = Math.round((parseFloat(item.value) / parseFloat(multicurrencyRate)) * roundBase) / roundBase;
    }
    acc[item.name] = item.value;

    return acc;
};
