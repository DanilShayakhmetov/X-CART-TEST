/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * X-Payments popup widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function popupXpaymentsInfo(xpid) {
    popup.load(
    URLHandler.buildURL(
      {
        'target': 'popup_xpayments_info',
        'xpid': xpid,
        'widget': '\\XLite\\Module\\XPay\\XPaymentsCloud\\View\\PopupXpaymentsInfo'
      }
    )
  );
}

function showRebillBox(orderNumber, amount) {
    popup.load(
        URLHandler.buildURL(
            {
                'target': 'popup_xpayments_cards',
                'order_number': orderNumber,
                'amount': amount,
                'widget': '\\XLite\\Module\\XPay\\XPaymentsCloud\\View\\PopupXpaymentsCards'
            }
        )
    );
}
