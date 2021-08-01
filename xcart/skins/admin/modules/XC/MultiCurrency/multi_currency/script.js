/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Currency page routines
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CurrencyManageForm()
{
  this.initialize();
}

CurrencyManageForm.prototype.patternCurrencyViewInfo = '.currency-view-info *';

CurrencyManageForm.prototype.initialize = function ()
{
  var obj = this;

  var defaultId = jQuery('input[name="defaultValue"]:checked').val();

  jQuery('#data-' + defaultId + '-format').change(function() {
    var tz = jQuery('#trailing-zeroes');

    jQuery(obj.patternCurrencyViewInfo).trigger(
        'formatCurrencyChange',
        [
          jQuery(this).val(),
          jQuery(tz).data('e'),
          jQuery(tz).data('thousandpart'),
          jQuery(tz).data('hundredspart'),
          jQuery(tz).data('delimiter')
        ]
    );
  });

  jQuery('#data-' + defaultId + '-prefix').keyup(function(event) {
    jQuery(obj.patternCurrencyViewInfo).trigger('prefixCurrencyChange', [jQuery(this).val()]);
  });

  jQuery('#data-' + defaultId + '-suffix').keyup(function(event) {
    jQuery(obj.patternCurrencyViewInfo).trigger('suffixCurrencyChange', [jQuery(this).val()]);
  });

  jQuery('#trailing-zeroes').bind(
    'trailingZeroesClick',
    function (event) {
      jQuery(obj.patternCurrencyViewInfo).trigger('trailingZeroesClick',[jQuery(this).prop('checked')]);
    }
  ).click(function (event) {
    jQuery(this).trigger('trailingZeroesClick');
  });

  jQuery(document).ready(function () {
    jQuery('#data-' + defaultId + '-format').trigger('change');

    jQuery('#data-' + defaultId + '-prefix, #data-' + defaultId + '-suffix').trigger('keyup');

    jQuery('#trailing-zeroes').trigger('trailingZeroesClick');
  });

  var unavailableCountries = core.getCommentedData(
    $('.items-list.xc-multicurrency-itemslist-model-currency-activecurrencies'),
    'unavailableCountries'
  );

  var renewCountriesList = function () {
    setTimeout(function () {
      $('.input-countries-select2 select option:not(:disabled, :selected)').filter(function () {
        return unavailableCountries !== null && unavailableCountries.indexOf($(this).val()) >= 0;
      }).attr('disabled', true);
      $('.input-countries-select2 select option:disabled').filter(function () {
        return unavailableCountries === null || unavailableCountries.indexOf($(this).val()) === -1;
      }).attr('disabled', false);

      $('.input-countries-select2 select').each(function () {
        $(this).select2($(this).data('select2').options.options);
      });
    });
  };

  $(function() {
    renewCountriesList();
  });

  $('.input-countries-select2 select').each(function () {
    this.oldCountries = $(this).val();
  });

  $('.input-countries-select2 select').change(function (event) {
    var removed = $(this.oldCountries).not($(this).val()).get();
    var added = $($(this).val()).not(this.oldCountries).get();

    unavailableCountries = $.merge($(unavailableCountries).not(removed), added).get();
    renewCountriesList();
    this.oldCountries = $(this).val();
  });
};

CurrencyManageForm.prototype.addCurrency = function ()
{
  var obj = jQuery('#currency-id');

  jQuery(obj).closest('form').find('input[name="action"]').val('add_currency');
  jQuery(obj).closest('form').submit();
};

core.autoload(CurrencyManageForm);

var currencyManageForm = new CurrencyManageForm();