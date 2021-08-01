/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Date field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function validateCouponDate(field, rules, i, options)
{
  var pattern = field.attr('name').replace(/Begin/, '%').replace(/End/, '%');
  var begin = -1 == field.attr('name').search(/Begin/)
    ? field.parents('form').find('[name="' + pattern.replace(/%/, 'Begin') + '"].datepicker-value-input').eq(0)
    : field.siblings('.datepicker-value-input');
  var end = -1 == field.attr('name').search(/End/)
    ? field.parents('form').find('[name="' + pattern.replace(/%/, 'End') + '"].datepicker-value-input').eq(0)
    : field.siblings('.datepicker-value-input');

  var commentedData = core.getCommentedData(field.parents('.input-field-wrapper'));
  var bd = Date.parse(begin.val());
  var ed = Date.parse(end.val());

  if (begin.length && end.length && begin.val() && end.val()) {
    if (!isNaN(bd) && !isNaN(ed)) {
      if (begin.get(0).name == field.get(0).name && bd > ed) {
        return lbl_validate_coupons_begin_date_error;

      } else if (end.get(0).name == field.get(0).name && bd > ed) {
        return lbl_validate_coupons_end_date_error;
      }
    }
  }

  var curDate = Date.parse(field.val());

  if (!isNaN(curDate) && curDate > Date.UTC(commentedData.highYear) || curDate < Date.UTC(commentedData.lowYear)) {
    return lbl_validate_coupons_max_date_error;
  }
}

